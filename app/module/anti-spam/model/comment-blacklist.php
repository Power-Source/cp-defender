<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * Comment Blacklist Model
 * Verwaltet netzwerkweite Blacklisten für Kommentare
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Comment_Blacklist {
	
	const TABLE_BLACKLIST = 'defender_antispam_comment_blacklist';
	const CACHE_KEY = 'cp_defender_comment_blacklist';
	const CACHE_TTL = 3600; // 1 Stunde
	
	/**
	 * Prüft ob eine E-Mail auf der Blacklist steht
	 */
	public static function is_email_blacklisted( string $email ): bool {
		if ( empty( $email ) ) {
			return false;
		}
		
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$result = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$table} WHERE type = %s AND value = %s AND status = %s",
			'email',
			strtolower( $email ),
			'active'
		) );
		
		return ! is_null( $result );
	}
	
	/**
	 * Prüft ob eine IP auf der Blacklist steht
	 */
	public static function is_ip_blacklisted( string $ip ): bool {
		if ( empty( $ip ) ) {
			return false;
		}
		
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$result = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$table} WHERE type = %s AND value = %s AND status = %s",
			'ip',
			$ip,
			'active'
		) );
		
		return ! is_null( $result );
	}
	
	/**
	 * Prüft ob eine Domain auf der Blacklist steht
	 */
	public static function is_domain_blacklisted( string $domain ): bool {
		if ( empty( $domain ) ) {
			return false;
		}
		
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		// Entferne www. für einheitliche Prüfung
		$domain = preg_replace( '/^www\./', '', strtolower( $domain ) );
		
		// Exakte Übereinstimmung UND Subdomain-Matching
		$result = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$table} WHERE type = %s AND (value = %s OR %s LIKE CONCAT('%%.', value)) AND status = %s",
			'domain',
			$domain,
			$domain,
			'active'
		) );
		
		return ! is_null( $result );
	}
	
	/**
	 * Prüft ob eine URL-Domain auf der Blacklist steht
	 */
	public static function is_url_domain_blacklisted( string $url ): bool {
		if ( empty( $url ) ) {
			return false;
		}
		
		$domain = wp_parse_url( $url, PHP_URL_HOST );
		
		if ( ! $domain ) {
			return false;
		}
		
		return self::is_domain_blacklisted( $domain );
	}
	
	/**
	 * Fügt einen Eintrag zur Blacklist hinzu
	 */
	public static function add( string $type, string $value, string $reason = '', int $certainty = 0 ): bool {
		if ( ! in_array( $type, array( 'email', 'ip', 'domain' ), true ) ) {
			return false;
		}
		
		if ( empty( $value ) ) {
			return false;
		}
		
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		// Normalisiere Werte
		if ( $type === 'email' ) {
			$value = strtolower( $value );
		} elseif ( $type === 'domain' ) {
			$value = preg_replace( '/^www\./', '', strtolower( $value ) );
		}
		
		// Prüfe ob bereits vorhanden
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$table} WHERE type = %s AND value = %s",
			$type,
			$value
		) );
		
		if ( $existing ) {
			// Update nur wenn newer certainty höher ist
			return (bool) $wpdb->update(
				$table,
				array(
					'reason'      => $reason,
					'certainty'   => max( $certainty, $wpdb->get_var( $wpdb->prepare(
						"SELECT certainty FROM {$table} WHERE id = %d",
						$existing
					) ) ),
					'updated_at'  => current_time( 'mysql' ),
				),
				array( 'id' => $existing ),
				array( '%s', '%d', '%s' ),
				array( '%d' )
			);
		}
		
		// Einfügen
		return (bool) $wpdb->insert(
			$table,
			array(
				'type'        => $type,
				'value'       => $value,
				'reason'      => $reason,
				'certainty'   => $certainty,
				'status'      => 'active',
				'created_at'  => current_time( 'mysql' ),
				'updated_at'  => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);
	}
	
	/**
	 * Fügt mehrere Einträge hinzu (Batch)
	 */
	public static function add_batch( array $entries ): int {
		$added = 0;
		
		foreach ( $entries as $entry ) {
			if ( self::add( $entry['type'], $entry['value'], $entry['reason'] ?? '', $entry['certainty'] ?? 0 ) ) {
				$added++;
			}
		}
		
		// Leere Cache
		wp_cache_delete( self::CACHE_KEY );
		
		return $added;
	}
	
	/**
	 * Entfernt einen Eintrag von der Blacklist
	 */
	public static function remove( string $type, string $value ): bool {
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$result = $wpdb->delete(
			$table,
			array(
				'type'  => $type,
				'value' => $value,
			),
			array( '%s', '%s' )
		);
		
		if ( $result ) {
			wp_cache_delete( self::CACHE_KEY );
		}
		
		return (bool) $result;
	}
	
	/**
	 * Holt alle aktiven Blacklist-Einträge
	 */
	public static function get_all( string $type = '', int $limit = 0 ): array {
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$query = "SELECT * FROM {$table} WHERE status = 'active'";
		$params = array();
		
		if ( ! empty( $type ) ) {
			$query .= " AND type = %s";
			$params[] = $type;
		}
		
		$query .= " ORDER BY certainty DESC, updated_at DESC";
		
		if ( $limit > 0 ) {
			$query .= " LIMIT %d";
			$params[] = $limit;
		}
		
		if ( ! empty( $params ) ) {
			$query = $wpdb->prepare( $query, $params );
		}
		
		return $wpdb->get_results( $query, ARRAY_A ) ?: array();
	}
	
	/**
	 * Zählt die Blacklist-Einträge
	 */
	public static function count( string $type = '' ): int {
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$query = "SELECT COUNT(*) FROM {$table} WHERE status = 'active'";
		
		if ( ! empty( $type ) ) {
			$query = $wpdb->prepare( "$query AND type = %s", $type );
		}
		
		return (int) $wpdb->get_var( $query );
	}
	
	/**
	 * Leert die komplette Blacklist
	 */
	public static function clear(): bool {
		global $wpdb;
		$table = $wpdb->base_prefix . self::TABLE_BLACKLIST;
		
		$result = (bool) $wpdb->query( "TRUNCATE TABLE {$table}" );
		
		if ( $result ) {
			wp_cache_delete( self::CACHE_KEY );
		}
		
		return $result;
	}
}
