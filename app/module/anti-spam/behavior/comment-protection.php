<?php

namespace CP_Defender\Module\Anti_Spam\Behavior;

use CP_Defender\Module\Anti_Spam\Model\Settings;
use CP_Defender\Module\Anti_Spam\Model\Comment_Blacklist;
use CP_Defender\Module\Anti_Spam\Model\IP_Reputation;

/**
 * Comment Protection Behavior
 * Schützt alle Sub-Sites vor Spam-Kommentaren netzwerkweit
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Comment_Protection {
	
	/**
	 * Initialisiert den Comment Protection
	 */
	public static function init(): void {
		// Pre-comment approval Hook (frühe Prüfung)
		add_filter( 'pre_comment_approved', array( self::class, 'check_comment' ), 10, 2 );
		
		// Spam-Klassifizierung
		add_filter( 'wp_blacklist_check', array( self::class, 'check_blacklist' ), 10, 2 );
		
		// Bei Blog-Spammen: Automatisch Kommentator blacklisten
		add_action( 'cp_defender_blog_marked_spam', array( self::class, 'on_blog_marked_spam' ) );
		
		// Bei neuer Signup: IP/Email präventiv erkennen
		add_action( 'cp_defender_new_signup_logged', array( self::class, 'on_new_signup' ) );
	}
	
	/**
	 * Hauptprüfung für Kommentare
	 * Prüft vor dem Speichern ob Kommentar blockiert werden soll
	 */
	public static function check_comment( $approved, $comment_data ): string | int {
		// Nur wenn Comment-Protection aktiviert ist
		if ( ! Settings::get( 'comment_protection_enabled' ) ) {
			return $approved;
		}
		
		// Super-Admin Kommentare nicht prüfen
		if ( current_user_can( 'manage_network' ) ) {
			return $approved;
		}
		
		// Bereits markiert als Spam/trash
		if ( $approved === 'spam' || $approved === 'trash' ) {
			return $approved;
		}
		
		$comment_author_email = isset( $comment_data['comment_author_email'] ) ? $comment_data['comment_author_email'] : '';
		$comment_author_url = isset( $comment_data['comment_author_url'] ) ? $comment_data['comment_author_url'] : '';
		$comment_author_ip = isset( $comment_data['comment_author_IP'] ) ? $comment_data['comment_author_IP'] : '';
		$comment_content = isset( $comment_data['comment_content'] ) ? $comment_data['comment_content'] : '';
		
		// Prüfungen durchführen
		$block_reason = self::check_against_blacklist(
			$comment_author_email,
			$comment_author_url,
			$comment_author_ip,
			$comment_content
		);
		
		if ( $block_reason ) {
			// Log den Spam-Versuch
			self::log_blocked_comment( $comment_data, $block_reason );
			
			// Markiere als Spam
			return 'spam';
		}
		
		return $approved;
	}
	
	/**
	 * Prüft Kommentar gegen Blacklisten
	 */
	private static function check_against_blacklist(
		string $email,
		string $url,
		string $ip,
		string $content
	): string {
		$settings = Settings::get_all();
		
		// E-Mail Prüfung
		if ( $settings['comment_block_email'] && ! empty( $email ) ) {
			if ( Comment_Blacklist::is_email_blacklisted( $email ) ) {
				return sprintf( 'email_blacklist: %s', $email );
			}
		}
		
		// IP Prüfung
		if ( $settings['comment_block_ip'] && ! empty( $ip ) ) {
			if ( Comment_Blacklist::is_ip_blacklisted( $ip ) ) {
				return sprintf( 'ip_blacklist: %s', $ip );
			}
			
			// Prüfe auch gegen IP-Reputation
			if ( IP_Reputation::is_blocked( $ip ) ) {
				return sprintf( 'ip_reputation: %s', $ip );
			}
		}
		
		// Domain/URL Prüfung
		if ( $settings['comment_block_domain'] && ! empty( $url ) ) {
			if ( Comment_Blacklist::is_url_domain_blacklisted( $url ) ) {
				return sprintf( 'domain_blacklist: %s', $url );
			}
		}
		
		// Disposable Email Prüfung
		if ( $settings['comment_block_disposable_email'] && ! empty( $email ) ) {
			if ( Disposable_Email::is_disposable( $email ) ) {
				return sprintf( 'disposable_email: %s', $email );
			}
		}
		
		// Content-Pattern Prüfung
		if ( $settings['comment_check_content'] && ! empty( $content ) ) {
			$blocked_patterns = self::get_blocked_patterns();
			
			foreach ( $blocked_patterns as $pattern ) {
				if ( @preg_match( '/' . $pattern . '/i', $content ) ) {
					return sprintf( 'content_pattern: %s', $pattern );
				}
			}
		}
		
		return ''; // Nicht blockiert
	}
	
	/**
	 * Holt blockierte Content-Muster (von erweiterten Patterns)
	 */
	private static function get_blocked_patterns(): array {
		$patterns = array(
			// Häufige SPAM-Patterns
			'viagra|cialis|casino|poker',
			'porn|xxx|sex\s*cam',
			'click\s*here|buy\s*now|limited\s*offer',
			'congratulations|you\s*won',
		);
		
		// Benutzer definierte Patterns laden
		$custom_patterns = Settings::get( 'comment_block_patterns' );
		
		if ( is_array( $custom_patterns ) ) {
			$patterns = array_merge( $patterns, $custom_patterns );
		}
		
		return array_filter( $patterns );
	}
	
	/**
	 * Prüft gegen WordPress Blacklist (wp_blacklist_check)
	 */
	public static function check_blacklist( $approved, $comment_data ): string | int {
		// Wenn bereits als Spam markiert, nicht nochmal prüfen
		if ( $approved === 'spam' ) {
			return $approved;
		}
		
		// Hier könnte man zusätzliche Logik einbauen
		// aber WordPress macht das normalerweise schon
		
		return $approved;
	}
	
	/**
	 * Wird aufgerufen wenn ein Blog als Spam markiert wird
	 * Fügt dessen Daten zur Comment-Blacklist hinzu
	 */
	public static function on_blog_marked_spam( array $blog_data ): void {
		if ( ! Settings::get( 'auto_blacklist_spam_blogs' ) ) {
			return;
		}
		
		$entries = array();
		
		// Füge Admin-Email hinzu
		if ( ! empty( $blog_data['admin_email'] ) ) {
			$entries[] = array(
				'type'        => 'email',
				'value'       => $blog_data['admin_email'],
				'reason'      => 'Auto-blocked from spam blog creation',
				'certainty'   => 90,
			);
		}
		
		// Füge Signup-IP hinzu
		if ( ! empty( $blog_data['signup_ip'] ) ) {
			$entries[] = array(
				'type'        => 'ip',
				'value'       => $blog_data['signup_ip'],
				'reason'      => 'Auto-blocked from spam blog creation',
				'certainty'   => 80,
			);
		}
		
		// Füge Blog-Domain hinzu (wenn nicht main site ist)
		if ( ! empty( $blog_data['blog_domain'] ) && ! self::is_main_site_domain( $blog_data['blog_domain'] ) ) {
			$entries[] = array(
				'type'        => 'domain',
				'value'       => $blog_data['blog_domain'],
				'reason'      => 'Auto-blocked from spam blog domain',
				'certainty'   => 85,
			);
		}
		
		if ( ! empty( $entries ) ) {
			Comment_Blacklist::add_batch( $entries );
			
			// Log die Aktion
			do_action( 'cp_defender_comment_blacklist_updated', 'auto_spam_blog', count( $entries ) );
		}
	}
	
	/**
	 * Wird aufgerufen bei neuer Signup (verdächtig)
	 */
	public static function on_new_signup( array $signup_data ): void {
		if ( ! Settings::get( 'auto_blacklist_suspicious_signups' ) ) {
			return;
		}
		
		// Nur wenn eine minimale Certainty erreicht wird
		$certainty = $signup_data['certainty'] ?? 0;
		
		if ( $certainty < Settings::get( 'suspicious_certainty_threshold' ) ) {
			return;
		}
		
		$entries = array();
		
		// Füge Email hinzu
		if ( ! empty( $signup_data['user_email'] ) ) {
			$entries[] = array(
				'type'        => 'email',
				'value'       => $signup_data['user_email'],
				'reason'      => 'Suspicious signup detected',
				'certainty'   => min( $certainty, 50 ), // Conservativ für Signups
			);
		}
		
		if ( ! empty( $entries ) ) {
			Comment_Blacklist::add_batch( $entries );
		}
	}
	
	/**
	 * Loggt blockierte Kommentare
	 */
	private static function log_blocked_comment( array $comment_data, string $reason ): void {
		// Kann optional in eine separate Tabelle geloggt werden
		// Für jetzt: Silence ist golden, aber Actions können Plugins nutzen
		
		do_action( 'cp_defender_comment_blocked', $comment_data, $reason );
	}
	
	/**
	 * Prüft ob Domain zur Hauptsite gehört
	 */
	private static function is_main_site_domain( string $domain ): bool {
		$main_site = get_site( 1 );
		$main_domain = wp_parse_url( $main_site->siteurl ?? '', PHP_URL_HOST );
		
		return strcasecmp( $domain, $main_domain ) === 0;
	}
}
