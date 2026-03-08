<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * Pattern Model für Spam-Erkennung
 * Verwaltet Regex-Patterns für Domain, Username, Email und Title
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Pattern {
	
	const OPTION_KEY = 'cp_defender_antispam_patterns';
	
	const TYPE_DOMAIN = 'domain';
	const TYPE_USERNAME = 'username';
	const TYPE_EMAIL = 'email';
	const TYPE_TITLE = 'title';
	
	const ACTION_BLOCK = 'block';   // Blockiert Signup komplett
	const ACTION_SPAM = 'spam';     // Markiert als Spam nach Signup
	
	/**
	 * Holt alle Patterns
	 */
	public static function get_all(): array {
		$patterns = get_site_option( self::OPTION_KEY, array() );
		
		// Initialisiere Default-Patterns beim ersten Aufruf
		if ( empty( $patterns ) ) {
			$patterns = self::get_default_patterns();
			self::save_all( $patterns );
		}
		
		return $patterns;
	}
	
	/**
	 * Holt ein einzelnes Pattern
	 */
	public static function get( int $id ): ?array {
		$patterns = self::get_all();
		return $patterns[ $id ] ?? null;
	}
	
	/**
	 * Speichert ein Pattern
	 */
	public static function save( array $pattern, ?int $id = null ): int {
		$patterns = self::get_all();
		
		// Validiere Pattern
		if ( ! self::validate( $pattern ) ) {
			return -1;
		}
		
		// Pattern-Hash für Statistiken
		$pattern['hash'] = md5( $pattern['regex'] . $pattern['type'] );
		$pattern['matched'] = $pattern['matched'] ?? 0;
		$pattern['created_at'] = $pattern['created_at'] ?? current_time( 'mysql' );
		$pattern['updated_at'] = current_time( 'mysql' );
		
		if ( $id !== null && isset( $patterns[ $id ] ) ) {
			// Update bestehendes Pattern
			$patterns[ $id ] = $pattern;
		} else {
			// Neues Pattern hinzufügen
			$patterns[] = $pattern;
			$id = count( $patterns ) - 1;
		}
		
		self::save_all( $patterns );
		
		return $id;
	}
	
	/**
	 * Löscht ein Pattern
	 */
	public static function delete( int $id ): bool {
		$patterns = self::get_all();
		
		if ( ! isset( $patterns[ $id ] ) ) {
			return false;
		}
		
		unset( $patterns[ $id ] );
		$patterns = array_values( $patterns ); // Re-index
		
		return self::save_all( $patterns );
	}
	
	/**
	 * Löscht mehrere Patterns
	 */
	public static function delete_multiple( array $ids ): int {
		$patterns = self::get_all();
		$deleted = 0;
		
		foreach ( $ids as $id ) {
			if ( isset( $patterns[ $id ] ) ) {
				unset( $patterns[ $id ] );
				$deleted++;
			}
		}
		
		$patterns = array_values( $patterns );
		self::save_all( $patterns );
		
		return $deleted;
	}
	
	/**
	 * Speichert alle Patterns
	 */
	private static function save_all( array $patterns ): bool {
		return update_site_option( self::OPTION_KEY, $patterns );
	}
	
	/**
	 * Validiert ein Pattern
	 */
	public static function validate( array $pattern ): bool {
		// Prüfe Pflichtfelder
		if ( empty( $pattern['regex'] ) || empty( $pattern['type'] ) || empty( $pattern['action'] ) ) {
			return false;
		}
		
		// Prüfe Regex-Syntax
		if ( @preg_match( $pattern['regex'], '' ) === false ) {
			return false;
		}
		
		// Prüfe gültige Werte
		$valid_types = array( self::TYPE_DOMAIN, self::TYPE_USERNAME, self::TYPE_EMAIL, self::TYPE_TITLE );
		$valid_actions = array( self::ACTION_BLOCK, self::ACTION_SPAM );
		
		if ( ! in_array( $pattern['type'], $valid_types ) || ! in_array( $pattern['action'], $valid_actions ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Testet ein Pattern gegen Daten
	 */
	public static function test( string $regex, string $type, int $limit = 50 ): array {
		global $wpdb;
		
		$matches = array();
		
		switch ( $type ) {
			case self::TYPE_DOMAIN:
				$results = $wpdb->get_col( 
					"SELECT SUBSTRING_INDEX(domain, '.', 1) as domain 
					FROM {$wpdb->blogs} 
					ORDER BY registered DESC 
					LIMIT 10000"
				);
				break;
				
			case self::TYPE_USERNAME:
				$results = $wpdb->get_col( 
					"SELECT user_login 
					FROM {$wpdb->users} 
					ORDER BY user_registered DESC 
					LIMIT 10000"
				);
				break;
				
			case self::TYPE_EMAIL:
				$results = $wpdb->get_col( 
					"SELECT user_email 
					FROM {$wpdb->users} 
					ORDER BY user_registered DESC 
					LIMIT 10000"
				);
				break;
				
			case self::TYPE_TITLE:
				return array(
					'matches' => array(),
					'total' => 0,
					'error' => __( 'Live-Tests für Site-Titel sind nicht verfügbar.', 'cpsec' )
				);
				
			default:
				return array( 'matches' => array(), 'total' => 0, 'error' => __( 'Ungültiger Typ.', 'cpsec' ) );
		}
		
		// Test Regex
		if ( @preg_match( $regex, '' ) === false ) {
			return array( 'matches' => array(), 'total' => 0, 'error' => __( 'Ungültiger Regex-Ausdruck.', 'cpsec' ) );
		}
		
		$matches = @preg_grep( $regex, $results );
		
		if ( $matches === false ) {
			return array( 'matches' => array(), 'total' => 0, 'error' => __( 'Fehler beim Testen des Patterns.', 'cpsec' ) );
		}
		
		$total = count( $matches );
		$matches = array_slice( $matches, 0, $limit );
		
		return array(
			'matches' => array_values( $matches ),
			'total' => $total,
			'shown' => count( $matches )
		);
	}
	
	/**
	 * Prüft einen Wert gegen alle aktiven Patterns eines Typs
	 */
	public static function check( string $value, string $type, string $action_filter = null ): ?array {
		$patterns = self::get_all();
		
		foreach ( $patterns as $id => $pattern ) {
			// Filter nach Typ
			if ( $pattern['type'] !== $type ) {
				continue;
			}
			
			// Filter nach Action (optional)
			if ( $action_filter !== null && $pattern['action'] !== $action_filter ) {
				continue;
			}
			
			// Teste Regex
			if ( @preg_match( $pattern['regex'], $value ) ) {
				// Erhöhe Match-Counter
				self::increment_match_count( $id );
				
				return array(
					'id' => $id,
					'pattern' => $pattern,
					'matched_value' => $value
				);
			}
		}
		
		return null;
	}
	
	/**
	 * Erhöht den Match-Counter eines Patterns
	 */
	private static function increment_match_count( int $id ): void {
		$patterns = self::get_all();
		
		if ( isset( $patterns[ $id ] ) ) {
			$patterns[ $id ]['matched'] = ( $patterns[ $id ]['matched'] ?? 0 ) + 1;
			$patterns[ $id ]['last_matched'] = current_time( 'mysql' );
			self::save_all( $patterns );
		}
	}
	
	/**
	 * Default-Patterns beim Setup
	 */
	private static function get_default_patterns(): array {
		return array(
			array(
				'regex'      => '/[a-z]+[0-9]{1,3}[a-z]+/',
				'desc'       => __( 'Domains mit Zahlen zwischen Wörtern (z.B. "some45blog")', 'cpsec' ),
				'type'       => self::TYPE_DOMAIN,
				'action'     => self::ACTION_SPAM,
				'matched'    => 0,
				'created_at' => current_time( 'mysql' ),
				'hash'       => md5( '/[a-z]+[0-9]{1,3}[a-z]+/' . self::TYPE_DOMAIN )
			),
			array(
				'regex'      => '/\b(viagra|cialis|porn|casino|poker|ugg|louboutin|pharma|warez|loan)\b/i',
				'desc'       => __( 'Bekannte Spam-Keywords', 'cpsec' ),
				'type'       => self::TYPE_DOMAIN,
				'action'     => self::ACTION_SPAM,
				'matched'    => 0,
				'created_at' => current_time( 'mysql' ),
				'hash'       => md5( '/\b(viagra|cialis|porn|casino|poker|ugg|louboutin|pharma|warez|loan)\b/i' . self::TYPE_DOMAIN )
			),
			array(
				'regex'      => '/^\d+$/',
				'desc'       => __( 'Nur Zahlen in Domain blockieren', 'cpsec' ),
				'type'       => self::TYPE_DOMAIN,
				'action'     => self::ACTION_BLOCK,
				'matched'    => 0,
				'created_at' => current_time( 'mysql' ),
				'hash'       => md5( '/^\d+$/' . self::TYPE_DOMAIN )
			),
			array(
				'regex'      => '/[a-z]+[0-9]{1,3}[a-z]+/',
				'desc'       => __( 'Usernames mit Zahlen zwischen Buchstaben', 'cpsec' ),
				'type'       => self::TYPE_USERNAME,
				'action'     => self::ACTION_SPAM,
				'matched'    => 0,
				'created_at' => current_time( 'mysql' ),
				'hash'       => md5( '/[a-z]+[0-9]{1,3}[a-z]+/' . self::TYPE_USERNAME )
			),
		);
	}
}
