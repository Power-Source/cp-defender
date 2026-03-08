<?php

namespace CP_Defender\Module\Anti_Spam\Behavior;

use CP_Defender\Module\Anti_Spam\Model\Settings;
use CP_Defender\Module\Anti_Spam\Model\Pattern;

/**
 * Migrationshilfe von Anti-Splog zu CP-Defender Anti-Spam
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Migration {
	
	/**
	 * Prüft ob Migration nötig ist
	 */
	public static function needs_migration(): bool {
		global $wpdb;
		
		// Prüfe auf alte Anti-Splog Tabelle
		$old_table = $wpdb->base_prefix . 'ust';
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$old_table'" );
		
		if ( ! $table_exists ) {
			return false;
		}
		
		// Prüfe ob bereits migriert
		$migrated = get_site_option( 'cp_defender_antispam_migrated' );
		
		return ! $migrated;
	}
	
	/**
	 * Führt vollständige Migration aus
	 */
	public static function migrate(): array {
		$results = array(
			'success' => true,
			'blogs_migrated' => 0,
			'settings_migrated' => false,
			'patterns_migrated' => 0,
			'errors' => array()
		);
		
		// Migriere Datenbank-Tabelle
		if ( Database::migrate_from_antisplog() ) {
			global $wpdb;
			$results['blogs_migrated'] = $wpdb->get_var( 
				"SELECT COUNT(*) FROM {$wpdb->base_prefix}defender_antispam_blogs" 
			);
		} else {
			$results['errors'][] = 'Datenbank-Migration fehlgeschlagen.';
		}
		
		// Migriere Einstellungen
		$old_settings = get_site_option( 'ust_settings' );
		if ( $old_settings ) {
			$new_settings = self::convert_settings( $old_settings );
			if ( Settings::save( $new_settings ) ) {
				$results['settings_migrated'] = true;
			} else {
				$results['errors'][] = 'Einstellungen-Migration fehlgeschlagen.';
			}
		}
		
		// Migriere Patterns
		$old_patterns = get_site_option( 'ust_patterns' );
		if ( $old_patterns && is_array( $old_patterns ) ) {
			foreach ( $old_patterns as $old_pattern ) {
				$new_pattern = self::convert_pattern( $old_pattern );
				if ( Pattern::save( $new_pattern ) >= 0 ) {
					$results['patterns_migrated']++;
				}
			}
		}
		
		// Markiere als migriert
		update_site_option( 'cp_defender_antispam_migrated', current_time( 'mysql' ) );
		
		if ( ! empty( $results['errors'] ) ) {
			$results['success'] = false;
		}
		
		return $results;
	}
	
	/**
	 * Konvertiert alte Einstellungen zu neuem Format
	 */
	private static function convert_settings( array $old_settings ): array {
		$new_settings = array();
		
		// IP-Blocking
		if ( isset( $old_settings['ip_blocking'] ) ) {
			$new_settings['ip_blocking_enabled'] = (bool) $old_settings['ip_blocking'];
			$new_settings['ip_blocking_threshold'] = absint( $old_settings['ip_blocking'] );
		}
		
		// Rate Limiting
		if ( isset( $old_settings['num_signups'] ) ) {
			$new_settings['rate_limit_enabled'] = ! empty( $old_settings['num_signups'] );
			$new_settings['rate_limit_count'] = absint( $old_settings['num_signups'] );
			$new_settings['rate_limit_period'] = 24; // Default
		}
		
		// Auto-Spam
		if ( isset( $old_settings['certainty'] ) ) {
			$new_settings['auto_spam_enabled'] = $old_settings['certainty'] < 999;
			$new_settings['auto_spam_certainty'] = absint( $old_settings['certainty'] );
		}
		
		// Human Verification
		if ( isset( $old_settings['signup_protect'] ) ) {
			switch ( $old_settings['signup_protect'] ) {
				case 'recaptcha':
					$new_settings['human_verification'] = 'recaptcha';
					break;
				case 'questions':
					$new_settings['human_verification'] = 'questions';
					break;
				default:
					$new_settings['human_verification'] = 'none';
			}
		}
		
		// reCAPTCHA Keys
		$old_recaptcha = get_site_option( 'ust_recaptcha' );
		if ( $old_recaptcha ) {
			$new_settings['recaptcha_site_key'] = $old_recaptcha['pubkey'] ?? '';
			$new_settings['recaptcha_secret_key'] = $old_recaptcha['privkey'] ?? '';
		}
		
		// Q&A
		$old_qa = get_site_option( 'ust_qa' );
		if ( $old_qa && is_array( $old_qa ) ) {
			$new_qa = array();
			foreach ( $old_qa as $qa ) {
				if ( is_array( $qa ) && count( $qa ) >= 2 ) {
					$new_qa[] = array(
						'question' => $qa[0],
						'answer' => $qa[1]
					);
				}
			}
			$new_settings['qa_questions'] = $new_qa;
		}
		
		// Weitere Optionen
		if ( isset( $old_settings['spam_blog_users'] ) ) {
			$new_settings['spam_user_on_blog_spam'] = (bool) $old_settings['spam_blog_users'];
		}
		
		if ( isset( $old_settings['hide_adminbar'] ) ) {
			$new_settings['show_toolbar_menu'] = ! (bool) $old_settings['hide_adminbar'];
		}
		
		return $new_settings;
	}
	
	/**
	 * Konvertiert altes Pattern zu neuem Format
	 */
	private static function convert_pattern( array $old_pattern ): array {
		return array(
			'regex' => $old_pattern['regex'] ?? '',
			'desc' => $old_pattern['desc'] ?? '',
			'type' => $old_pattern['type'] ?? Pattern::TYPE_DOMAIN,
			'action' => $old_pattern['action'] ?? Pattern::ACTION_SPAM,
			'matched' => $old_pattern['matched'] ?? 0,
		);
	}
	
	/**
	 * Bereinigt alte Anti-Splog-Daten (nach erfolgreicher Migration)
	 */
	public static function cleanup_old_data(): void {
		// Nur aufrufen, wenn sicher migriert wurde!
		global $wpdb;
		
		// Lösche alte Tabelle (optional, mit Vorsicht!)
		// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}ust" );
		
		// Lösche alte Optionen
		delete_site_option( 'ust_settings' );
		delete_site_option( 'ust_patterns' );
		delete_site_option( 'ust_recaptcha' );
		delete_site_option( 'ust_qa' );
		delete_site_option( 'ust_signup' );
		delete_site_option( 'ust_version' );
		delete_site_option( 'ust_installed' );
		delete_site_option( 'ust_spam_count' );
		delete_site_option( 'ust_salt' );
		delete_site_option( 'ust_key_dismiss' );
	}
	
	/**
	 * Zeigt Migration-Notice im Admin
	 */
	public static function show_migration_notice(): void {
		if ( ! is_super_admin() || ! self::needs_migration() ) {
			return;
		}
		
		$migration_url = add_query_arg( array(
			'page' => 'cp-defender-antispam-settings',
			'migrate' => '1',
			'_wpnonce' => wp_create_nonce( 'defender_antispam_migrate' )
		), network_admin_url( 'admin.php' ) );
		
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php _e( 'PS Security Anti-Spam:', 'cpsec' ); ?></strong> 
				<?php _e( 'Wir haben erkannt, dass du vorher Anti-Splog verwendet hast. Möchtest du die Daten migrieren?', 'cpsec' ); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( $migration_url ); ?>" class="button button-primary">
					<?php _e( 'Jetzt migrieren', 'cpsec' ); ?>
				</a>
				<a href="<?php echo esc_url( add_query_arg( 'dismiss_migration', '1' ) ); ?>" class="button">
					<?php _e( 'Später', 'cpsec' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}
