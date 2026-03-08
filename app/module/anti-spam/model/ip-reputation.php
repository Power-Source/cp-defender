<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * IP-Reputation Model
 * Trackt IPs und ihre Spam-Historie
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class IP_Reputation {
	
	/**
	 * Holt Reputation-Daten für eine IP
	 */
	public static function get( string $ip ): ?array {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		
		$result = $wpdb->get_row( 
			$wpdb->prepare( 
				"SELECT * FROM {$table} WHERE ip_address = %s", 
				$ip 
			), 
			ARRAY_A 
		);
		
		return $result ?: null;
	}
	
	/**
	 * Erstellt oder aktualisiert IP-Eintrag
	 */
	public static function update( string $ip, array $data ): bool {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		$existing = self::get( $ip );
		
		if ( $existing ) {
			// Update
			return (bool) $wpdb->update(
				$table,
				$data,
				array( 'ip_address' => $ip ),
				self::get_format( $data ),
				array( '%s' )
			);
		} else {
			// Insert
			$data['ip_address'] = $ip;
			return (bool) $wpdb->insert(
				$table,
				$data,
				self::get_format( $data )
			);
		}
	}
	
	/**
	 * Erhöht Spam-Counter für eine IP
	 */
	public static function increment_spam_count( string $ip ): int {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		$current = self::get( $ip );
		
		if ( $current ) {
			$new_count = $current['spam_count'] + 1;
			
			$wpdb->update(
				$table,
				array(
					'spam_count' => $new_count,
					'last_spam_date' => current_time( 'mysql' )
				),
				array( 'ip_address' => $ip ),
				array( '%d', '%s' ),
				array( '%s' )
			);
		} else {
			$wpdb->insert(
				$table,
				array(
					'ip_address' => $ip,
					'spam_count' => 1,
					'signup_count' => 1,
					'last_spam_date' => current_time( 'mysql' )
				),
				array( '%s', '%d', '%d', '%s' )
			);
			
			$new_count = 1;
		}
		
		return $new_count;
	}
	
	/**
	 * Erhöht Signup-Counter für eine IP
	 */
	public static function increment_signup_count( string $ip ): int {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		$current = self::get( $ip );
		
		if ( $current ) {
			$new_count = $current['signup_count'] + 1;
			
			$wpdb->update(
				$table,
				array( 'signup_count' => $new_count ),
				array( 'ip_address' => $ip ),
				array( '%d' ),
				array( '%s' )
			);
		} else {
			$wpdb->insert(
				$table,
				array(
					'ip_address' => $ip,
					'signup_count' => 1
				),
				array( '%s', '%d' )
			);
			
			$new_count = 1;
		}
		
		return $new_count;
	}
	
	/**
	 * Blockiert eine IP
	 */
	public static function block( string $ip, string $reason = '' ): bool {
		return self::update( $ip, array(
			'is_blocked' => 1,
			'block_reason' => $reason
		) );
	}
	
	/**
	 * Entblockt eine IP
	 */
	public static function unblock( string $ip ): bool {
		return self::update( $ip, array(
			'is_blocked' => 0,
			'block_reason' => null
		) );
	}
	
	/**
	 * Prüft ob eine IP blockiert ist
	 */
	public static function is_blocked( string $ip ): bool {
		$data = self::get( $ip );
		return $data && (bool) $data['is_blocked'];
	}
	
	/**
	 * Holt Spam-Historie einer IP
	 */
	public static function get_spam_count( string $ip ): int {
		$data = self::get( $ip );
		return $data ? (int) $data['spam_count'] : 0;
	}
	
	/**
	 * Holt Signup-Count einer IP in einem Zeitraum
	 */
	public static function get_recent_signup_count( string $ip, int $hours = 24 ): int {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		$since = date( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );
		
		$count = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE last_ip = %s AND signup_date >= %s",
				$ip,
				$since
			)
		);
		
		// Zusätzlich aus registration_log prüfen (WP Core)
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->registration_log}'" ) ) {
			$reg_count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->registration_log} WHERE IP = %s AND date_registered >= %s",
					$ip,
					$since
				)
			);
			
			$count += $reg_count;
		}
		
		return (int) $count;
	}
	
	/**
	 * Holt alle blockierten IPs
	 */
	public static function get_blocked_ips( int $limit = 100, int $offset = 0 ): array {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE is_blocked = 1 ORDER BY last_spam_date DESC LIMIT %d OFFSET %d",
				$limit,
				$offset
			),
			ARRAY_A
		);
		
		return $results ?: array();
	}
	
	/**
	 * Holt Top-Spam-IPs
	 */
	public static function get_top_spammers( int $limit = 10 ): array {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE spam_count > 0 ORDER BY spam_count DESC LIMIT %d",
				$limit
			),
			ARRAY_A
		);
		
		return $results ?: array();
	}
	
	/**
	 * Löscht alte Einträge
	 */
	public static function cleanup( int $days = 90 ): int {
		global $wpdb;
		
		$table = $wpdb->base_prefix . 'defender_antispam_ips';
		$date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
		
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table} WHERE is_blocked = 0 AND spam_count = 0 AND first_seen < %s",
				$date
			)
		);
		
		return (int) $deleted;
	}
	
	/**
	 * Hilfsfunktion für wpdb Format-Array
	 */
	private static function get_format( array $data ): array {
		$format = array();
		
		foreach ( $data as $key => $value ) {
			if ( in_array( $key, array( 'spam_count', 'signup_count', 'is_blocked' ) ) ) {
				$format[] = '%d';
			} else {
				$format[] = '%s';
			}
		}
		
		return $format;
	}
}
