<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * Settings Model für Anti-Spam Modul
 * Verwaltet alle Plugin-Einstellungen
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Settings {
	
	const OPTION_KEY = 'cp_defender_antispam_settings';
	
	/**
	 * @var array Default-Einstellungen
	 */
	private static array $defaults = array(
		// IP-Blocking
		'ip_blocking_enabled'     => true,
		'ip_blocking_threshold'   => 1,        // Blockiere nach X Spam-Blogs
		
		// Rate Limiting
		'rate_limit_enabled'      => true,
		'rate_limit_count'        => 3,        // Max Signups
		'rate_limit_period'       => 24,       // In Stunden
		
		// Auto-Spam
		'auto_spam_enabled'       => true,
		'auto_spam_certainty'     => 80,       // Certainty-Schwellwert (%)
		
		// Pattern Matching
		'patterns_enabled'        => true,
		
		// Human Verification
		'human_verification'      => 'turnstile', // none, turnstile, recaptcha, questions
		'turnstile_site_key'      => '',
		'turnstile_secret_key'    => '',
		'recaptcha_site_key'      => '',
		'recaptcha_secret_key'    => '',
		'honeypot_enabled'        => true,
		
		// Disposable Email Protection
		'disposable_email_check_enabled' => true,
		'disposable_domains_auto_update' => true,
		'disposable_domains_last_update' => 0,
		
		// Q&A Protection
		'qa_questions'            => array(),
		
		// Dynamic Signup URL
		'dynamic_signup_enabled'  => false,
		'dynamic_signup_slug'     => '',
		'dynamic_signup_expires'  => 0,
		
		// Post Monitoring
		'post_monitoring_enabled' => false,
		'post_spam_certainty'     => 90,
		
		// Notifications
		'notify_on_spam'          => false,
		'notify_email'            => '',
		
		// Moderation
		'spam_user_on_blog_spam'  => false,
		'show_toolbar_menu'       => true,
	);
	
	/**
	 * Lädt die Einstellungen
	 */
	public static function get_all(): array {
		$settings = get_site_option( self::OPTION_KEY, array() );
		return wp_parse_args( $settings, self::$defaults );
	}
	
	/**
	 * Holt einen einzelnen Einstellungswert
	 */
	public static function get( string $key, $default = null ) {
		$settings = self::get_all();
		return $settings[ $key ] ?? $default ?? self::$defaults[ $key ] ?? null;
	}
	
	/**
	 * Speichert einen einzelnen Wert
	 */
	public static function set( string $key, $value ): bool {
		$settings = self::get_all();
		$settings[ $key ] = $value;
		return update_site_option( self::OPTION_KEY, $settings );
	}
	
	/**
	 * Speichert alle Einstellungen
	 */
	public static function save( array $settings ): bool {
		// Sanitize Eingaben
		$settings = self::sanitize( $settings );
		
		// Merge mit bestehenden Einstellungen
		$current = self::get_all();
		$settings = array_merge( $current, $settings );
		
		return update_site_option( self::OPTION_KEY, $settings );
	}
	
	/**
	 * Setzt die Einstellungen auf Defaults zurück
	 */
	public static function reset(): bool {
		return update_site_option( self::OPTION_KEY, self::$defaults );
	}
	
	/**
	 * Sanitized Einstellungen
	 */
	private static function sanitize( array $settings ): array {
		$sanitized = array();
		
		foreach ( $settings as $key => $value ) {
			switch ( $key ) {
				case 'ip_blocking_enabled':
				case 'rate_limit_enabled':
				case 'auto_spam_enabled':
				case 'patterns_enabled':
				case 'honeypot_enabled':
				case 'dynamic_signup_enabled':
				case 'post_monitoring_enabled':
				case 'notify_on_spam':
				case 'spam_user_on_blog_spam':
				case 'show_toolbar_menu':
					$sanitized[ $key ] = (bool) $value;
					break;
					
				case 'ip_blocking_threshold':
				case 'rate_limit_count':
				case 'rate_limit_period':
				case 'auto_spam_certainty':
				case 'post_spam_certainty':
					$sanitized[ $key ] = absint( $value );
					break;
					
				case 'human_verification':
					$sanitized[ $key ] = in_array( $value, array( 'none', 'turnstile', 'recaptcha', 'questions' ) )
						? $value 
						: 'none';
					break;
					
				case 'turnstile_site_key':
				case 'turnstile_secret_key':
				case 'recaptcha_site_key':
				case 'recaptcha_secret_key':
				case 'dynamic_signup_slug':
					$sanitized[ $key ] = sanitize_text_field( $value );
					break;
					
				case 'notify_email':
					$sanitized[ $key ] = sanitize_email( $value );
					break;
					
				case 'qa_questions':
					$sanitized[ $key ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : array();
					break;
					
				default:
					$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}
		
		return $sanitized;
	}
}
