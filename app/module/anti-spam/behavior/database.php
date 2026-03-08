<?php

namespace CP_Defender\Module\Anti_Spam\Behavior;

/**
 * Database Handler für Anti-Spam Modul
 * Verwaltet Custom Tables für Blog-Tracking und IP-Reputation
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Database {
	
	const VERSION = '1.0.0';
	const VERSION_OPTION = 'cp_defender_antispam_db_version';
	
	/**
	 * Installiert oder aktualisiert die Datenbank-Tabellen
	 */
	public static function install(): void {
		global $wpdb;
		
		$installed_version = get_site_option( self::VERSION_OPTION );
		
		if ( $installed_version === self::VERSION ) {
			return;
		}
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$charset_collate = $wpdb->get_charset_collate();
		$table_prefix = $wpdb->base_prefix;
		
		// Blog-Tracking-Tabelle
		$sql_blogs = "CREATE TABLE {$table_prefix}defender_antispam_blogs (
			blog_id bigint(20) unsigned NOT NULL,
			last_user_id bigint(20) NULL DEFAULT NULL,
			last_ip varchar(45) DEFAULT NULL,
			last_user_agent varchar(255) DEFAULT NULL,
			signup_date datetime DEFAULT '0000-00-00 00:00:00',
			spammed_date datetime DEFAULT '0000-00-00 00:00:00',
			certainty int(3) NOT NULL DEFAULT 0,
			is_ignored tinyint(1) NOT NULL DEFAULT 0,
			pattern_matched varchar(100) DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (blog_id),
			KEY last_ip (last_ip),
			KEY certainty (certainty),
			KEY is_ignored (is_ignored)
		) $charset_collate;";
		
		// IP-Reputation-Tabelle
		$sql_ips = "CREATE TABLE {$table_prefix}defender_antispam_ips (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			ip_address varchar(45) NOT NULL,
			spam_count int(11) NOT NULL DEFAULT 0,
			signup_count int(11) NOT NULL DEFAULT 0,
			last_spam_date datetime DEFAULT NULL,
			first_seen datetime DEFAULT CURRENT_TIMESTAMP,
			is_blocked tinyint(1) NOT NULL DEFAULT 0,
			block_reason varchar(255) DEFAULT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY ip_address (ip_address),
			KEY is_blocked (is_blocked)
		) $charset_collate;";
		
		// Pattern-Statistiken-Tabelle
		$sql_patterns = "CREATE TABLE {$table_prefix}defender_antispam_pattern_stats (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			pattern_hash varchar(32) NOT NULL,
			pattern_type varchar(20) NOT NULL,
			matched_count int(11) NOT NULL DEFAULT 0,
			last_matched datetime DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY pattern_hash (pattern_hash),
			KEY pattern_type (pattern_type)
		) $charset_collate;";
		
		dbDelta( $sql_blogs );
		dbDelta( $sql_ips );
		dbDelta( $sql_patterns );
		
		update_site_option( self::VERSION_OPTION, self::VERSION );
	}
	
	/**
	 * Deinstalliert die Datenbank-Tabellen
	 */
	public static function uninstall(): void {
		global $wpdb;
		
		$table_prefix = $wpdb->base_prefix;
		
		$wpdb->query( "DROP TABLE IF EXISTS {$table_prefix}defender_antispam_blogs" );
		$wpdb->query( "DROP TABLE IF EXISTS {$table_prefix}defender_antispam_ips" );
		$wpdb->query( "DROP TABLE IF EXISTS {$table_prefix}defender_antispam_pattern_stats" );
		
		delete_site_option( self::VERSION_OPTION );
	}
	
	/**
	 * Migriert Daten aus dem alten Anti-Splog Plugin (falls vorhanden)
	 */
	public static function migrate_from_antisplog(): bool {
		global $wpdb;
		
		$old_table = $wpdb->base_prefix . 'ust';
		
		// Prüfe ob alte Tabelle existiert
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$old_table'" );
		
		if ( ! $table_exists ) {
			return false;
		}
		
		$new_table = $wpdb->base_prefix . 'defender_antispam_blogs';
		
		// Migriere Daten
		$wpdb->query( "
			INSERT INTO {$new_table} (blog_id, last_user_id, last_ip, last_user_agent, spammed_date, certainty, is_ignored)
			SELECT blog_id, last_user_id, last_ip, last_user_agent, spammed, certainty, `ignore`
			FROM {$old_table}
			ON DUPLICATE KEY UPDATE
				last_user_id = VALUES(last_user_id),
				last_ip = VALUES(last_ip),
				last_user_agent = VALUES(last_user_agent),
				spammed_date = VALUES(spammed_date),
				certainty = VALUES(certainty),
				is_ignored = VALUES(is_ignored)
		" );
		
		return true;
	}
}
