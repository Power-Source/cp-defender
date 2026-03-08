<?php

namespace CP_Defender\Module\Anti_Spam\Behavior;

use CP_Defender\Module\Anti_Spam\Model\Settings;

/**
 * Disposable Email Domain Manager
 * Verwaltet lokale Liste von Wegwerf-Email-Domains + Auto-Update
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.2.0
 */
class Disposable_Email {
	
	const DATA_FILE = 'data/disposable-domains.json';
	const GITHUB_SOURCE = 'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf';
	const CRON_HOOK = 'defender_update_disposable_domains';
	
	/**
	 * @var array Cached domain list
	 */
	private static ?array $domains_cache = null;
	
	/**
	 * Prüft ob Email-Adresse eine Disposable-Domain verwendet
	 * 
	 * @param string $email Email-Adresse
	 * @return bool True wenn disposable, sonst false
	 */
	public static function is_disposable( string $email ): bool {
		// Feature disabled?
		if ( ! Settings::get( 'disposable_email_check_enabled', true ) ) {
			return false;
		}
		
		// Extrahiere Domain aus Email
		$domain = self::extract_domain( $email );
		if ( empty( $domain ) ) {
			return false;
		}
		
		// Lade Domain-Liste
		$domains = self::get_domains();
		if ( empty( $domains ) ) {
			return false; // Fail-open wenn Liste nicht verfügbar
		}
		
		// Case-insensitive Prüfung
		return in_array( strtolower( $domain ), $domains, true );
	}
	
	/**
	 * Extrahiert Domain aus Email-Adresse
	 * 
	 * @param string $email
	 * @return string|null
	 */
	private static function extract_domain( string $email ): ?string {
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return null;
		}
		
		$parts = explode( '@', $email );
		return isset( $parts[1] ) ? strtolower( trim( $parts[1] ) ) : null;
	}
	
	/**
	 * Lädt Domain-Liste aus JSON-Datei
	 * 
	 * @return array
	 */
	private static function get_domains(): array {
		// Verwende Cache falls vorhanden
		if ( self::$domains_cache !== null ) {
			return self::$domains_cache;
		}
		
		$file_path = self::get_data_file_path();
		
		if ( ! file_exists( $file_path ) ) {
			self::$domains_cache = array();
			return array();
		}
		
		$json = file_get_contents( $file_path );
		if ( $json === false ) {
			self::$domains_cache = array();
			return array();
		}
		
		$data = json_decode( $json, true );
		if ( ! is_array( $data ) || ! isset( $data['domains'] ) ) {
			self::$domains_cache = array();
			return array();
		}
		
		// Normalisiere alle Domains zu lowercase
		self::$domains_cache = array_map( 'strtolower', $data['domains'] );
		
		return self::$domains_cache;
	}
	
	/**
	 * Gibt den absoluten Pfad zur JSON-Datei zurück
	 * 
	 * @return string
	 */
	private static function get_data_file_path(): string {
		return dirname( __DIR__ ) . '/' . self::DATA_FILE;
	}
	
	/**
	 * Aktualisiert Domain-Liste von GitHub
	 * 
	 * @return array ['success' => bool, 'message' => string, 'count' => int]
	 */
	public static function update_domain_list(): array {
		// Download von GitHub
		$response = wp_remote_get( self::GITHUB_SOURCE, array(
			'timeout' => 30,
			'user-agent' => 'CP-Defender-AntiSpam/1.2',
		) );
		
		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => 'Download fehlgeschlagen: ' . $response->get_error_message(),
				'count' => 0,
			);
		}
		
		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code !== 200 ) {
			return array(
				'success' => false,
				'message' => 'HTTP Error ' . $status_code,
				'count' => 0,
			);
		}
		
		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return array(
				'success' => false,
				'message' => 'Leere Antwort vom Server',
				'count' => 0,
			);
		}
		
		// Parse line-by-line (jede Zeile = eine Domain)
		$lines = explode( "\n", $body );
		$domains = array();
		
		foreach ( $lines as $line ) {
			$line = trim( $line );
			// Skip Kommentare und leere Zeilen
			if ( empty( $line ) || strpos( $line, '#' ) === 0 ) {
				continue;
			}
			$domains[] = strtolower( $line );
		}
		
		if ( empty( $domains ) ) {
			return array(
				'success' => false,
				'message' => 'Keine Domains gefunden',
				'count' => 0,
			);
		}
		
		// Erstelle JSON-Struktur
		$data = array(
			'version' => '1.0.0',
			'last_update' => time(),
			'source' => self::GITHUB_SOURCE,
			'count' => count( $domains ),
			'domains' => $domains,
		);
		
		// Speichere zu Datei
		$file_path = self::get_data_file_path();
		$json = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		
		$result = file_put_contents( $file_path, $json );
		
		if ( $result === false ) {
			return array(
				'success' => false,
				'message' => 'Konnte Datei nicht schreiben',
				'count' => 0,
			);
		}
		
		// Update Timestamp in Settings
		Settings::set( 'disposable_domains_last_update', time() );
		
		// Cache leeren
		self::$domains_cache = null;
		
		return array(
			'success' => true,
			'message' => sprintf( '%s Domains erfolgreich aktualisiert', number_format_i18n( count( $domains ) ) ),
			'count' => count( $domains ),
		);
	}
	
	/**
	 * Gibt Informationen über die Domain-Liste zurück
	 * 
	 * @return array ['count' => int, 'last_update' => int, 'file_exists' => bool]
	 */
	public static function get_list_info(): array {
		$file_path = self::get_data_file_path();
		$file_exists = file_exists( $file_path );
		
		if ( ! $file_exists ) {
			return array(
				'count' => 0,
				'last_update' => 0,
				'file_exists' => false,
			);
		}
		
		$json = file_get_contents( $file_path );
		$data = json_decode( $json, true );
		
		return array(
			'count' => $data['count'] ?? 0,
			'last_update' => $data['last_update'] ?? 0,
			'file_exists' => true,
		);
	}
	
	/**
	 * Anzahl der Domains in der Liste
	 * 
	 * @return int
	 */
	public static function get_domain_count(): int {
		$info = self::get_list_info();
		return $info['count'];
	}
	
	/**
	 * Letztes Update als Unix-Timestamp
	 * 
	 * @return int
	 */
	public static function get_last_update(): int {
		$info = self::get_list_info();
		return $info['last_update'];
	}
	
	/**
	 * Registriert WP-Cron Job für Auto-Updates (wöchentlich)
	 */
	public static function schedule_auto_update(): void {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'weekly', self::CRON_HOOK );
		}
	}
	
	/**
	 * Entfernt WP-Cron Job
	 */
	public static function unschedule_auto_update(): void {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}
	
	/**
	 * Cron-Callback: Aktualisiert Domain-Liste automatisch
	 * Wird von WP-Cron aufgerufen
	 */
	public static function cron_update_domains(): void {
		// Nur updaten wenn Auto-Update aktiviert ist
		if ( ! Settings::get( 'disposable_domains_auto_update', true ) ) {
			return;
		}
		
		self::update_domain_list();
	}
	
	/**
	 * Initialisierung: Registriert Cron-Hook
	 */
	public static function init(): void {
		// Registriere Cron Hook
		add_action( self::CRON_HOOK, array( __CLASS__, 'cron_update_domains' ) );
		
		// Schedule Cron wenn Auto-Update aktiviert
		if ( Settings::get( 'disposable_domains_auto_update', true ) ) {
			self::schedule_auto_update();
		} else {
			self::unschedule_auto_update();
		}
	}
}
