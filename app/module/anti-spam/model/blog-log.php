<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * Blog_Log Model
 * Verwaltet Blog-Tracking-Daten
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Blog_Log {
	
	/**
	 * Prüft ob die Tracking-Tabelle existiert
	 */
	private static function table_exists(): bool {
		global $wpdb;

		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );

		return $exists === $table;
	}
	
	/**
	 * Holt Log-Daten für einen Blog
	 */
	public static function get( int $blog_id ): ?array {
		global $wpdb;

		if ( ! self::table_exists() ) {
			return null;
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		
		$result = $wpdb->get_row( 
			$wpdb->prepare( 
				"SELECT * FROM {$table} WHERE blog_id = %d", 
				$blog_id 
			), 
			ARRAY_A 
		);
		
		return $result ?: null;
	}
	
	/**
	 * Erstellt oder aktualisiert Blog-Eintrag
	 */
	public static function update( int $blog_id, array $data ): bool {
		global $wpdb;

		if ( ! self::table_exists() ) {
			return false;
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		$existing = self::get( $blog_id );
		
		if ( $existing ) {
			// Update
			return (bool) $wpdb->update(
				$table,
				$data,
				array( 'blog_id' => $blog_id ),
				self::get_format( $data ),
				array( '%d' )
			);
		} else {
			// Insert
			$data['blog_id'] = $blog_id;
			return (bool) $wpdb->insert(
				$table,
				$data,
				self::get_format( $data )
			);
		}
	}
	
	/**
	 * Markiert Blog als ignoriert
	 */
	public static function ignore( int $blog_id, bool $ignored = true ): bool {
		return self::update( $blog_id, array( 'is_ignored' => $ignored ? 1 : 0 ) );
	}
	
	/**
	 * Markiert Blog als Spam
	 */
	public static function mark_spam( int $blog_id, int $certainty = 100, ?string $pattern_matched = null ): bool {
		$data = array(
			'spammed_date' => current_time( 'mysql' ),
			'certainty' => $certainty
		);
		
		if ( $pattern_matched ) {
			$data['pattern_matched'] = $pattern_matched;
		}
		
		return self::update( $blog_id, $data );
	}
	
	/**
	 * Holt verdächtige Blogs (Certainty > 50, nicht ignoriert, nicht gespammed)
	 */
	public static function get_suspicious( int $limit = 100, int $offset = 0 ): array {
		global $wpdb;

		if ( ! self::table_exists() ) {
			return array();
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.*, b.domain, b.path, b.registered, b.spam 
				FROM {$table} a
				INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id
				WHERE a.certainty > 50 
				AND a.is_ignored = 0 
				AND b.spam = 0
				ORDER BY a.certainty DESC, a.signup_date DESC
				LIMIT %d OFFSET %d",
				$limit,
				$offset
			),
			ARRAY_A
		);
		
		return $results ?: array();
	}
	
	/**
	 * Holt Spam-Blogs
	 */
	public static function get_spam_blogs( int $limit = 100, int $offset = 0 ): array {
		global $wpdb;

		if ( ! self::table_exists() ) {
			return array();
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.*, b.domain, b.path, b.registered 
				FROM {$table} a
				INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id
				WHERE b.spam = 1
				ORDER BY a.spammed_date DESC
				LIMIT %d OFFSET %d",
				$limit,
				$offset
			),
			ARRAY_A
		);
		
		return $results ?: array();
	}
	
	/**
	 * Count-Statistiken
	 */
	public static function get_counts(): array {
		global $wpdb;

		$counts = array(
			'total'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->blogs}" ),
			'spam'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->blogs} WHERE spam = 1" ),
			'suspicious' => 0,
			'ignored'    => 0,
		);

		if ( ! self::table_exists() ) {
			return $counts;
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';

		$counts['suspicious'] = (int) $wpdb->get_var(
				"SELECT COUNT(*) FROM {$table} a 
				INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id 
				WHERE a.certainty > 50 AND a.is_ignored = 0 AND b.spam = 0"
		);
		$counts['ignored'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE is_ignored = 1" );

		return $counts;
	}
	
	/**
	 * Löscht alte Logs
	 */
	public static function cleanup( int $days = 180 ): int {
		global $wpdb;

		if ( ! self::table_exists() ) {
			return 0;
		}
		
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		$date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
		
		// Nur Einträge löschen, die nicht zu gespammten Blogs gehören
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE a FROM {$table} a
				INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id
				WHERE b.spam = 0 
				AND a.certainty = 0 
				AND a.is_ignored = 0
				AND a.signup_date < %s",
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
			if ( in_array( $key, array( 'blog_id', 'last_user_id', 'certainty', 'is_ignored' ) ) ) {
				$format[] = '%d';
			} else {
				$format[] = '%s';
			}
		}
		
		return $format;
	}
}
