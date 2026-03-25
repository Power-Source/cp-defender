<?php

namespace CP_Defender\Module\Anti_Spam\Controller;

use CP_Defender\Module\Anti_Spam\Behavior\Database;
use CP_Defender\Module\Anti_Spam\Behavior\Disposable_Email;
use CP_Defender\Module\Anti_Spam\Behavior\Comment_Protection;
use CP_Defender\Module\Anti_Spam\Model\Settings;
use CP_Defender\Module\Anti_Spam\Model\Pattern;
use CP_Defender\Module\Anti_Spam\Model\Blog_Log;

/**
 * Haupt-Controller für Anti-Spam Modul
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Main {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// Installation/Update Hook
		add_action( 'admin_init', array( $this, 'maybe_install' ) );
		
		// Migration Check
		add_action( 'network_admin_notices', array( $this, 'maybe_show_migration_notice' ) );
		
		// Disposable Email Protection initialisieren
		Disposable_Email::init();
		
		// Comment Protection initialisieren (Netzwerkweiter Spam-Schutz)
		Comment_Protection::init();
		
		// Signup Protection laden
		new Signup_Protection();
		
		// Admin Menu
		add_action( 'network_admin_menu', array( $this, 'register_menu' ) );
		
		// Admin Scripts & Styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// AJAX Handler
		add_action( 'wp_ajax_defender_antispam_test_pattern', array( $this, 'ajax_test_pattern' ) );
		add_action( 'wp_ajax_defender_antispam_toggle_blog', array( $this, 'ajax_toggle_blog' ) );
		add_action( 'wp_ajax_defender_update_disposable_domains', array( $this, 'ajax_update_disposable_domains' ) );
		
		// Dashboard Widget
		add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}
	
	/**
	 * Zeigt Migration-Notice wenn nötig
	 */
	public function maybe_show_migration_notice(): void {
		\CP_Defender\Module\Anti_Spam\Behavior\Migration::show_migration_notice();
	}
	
	/**
	 * Installiert oder updated die Datenbank
	 */
	public function maybe_install(): void {
		if ( ! is_super_admin() ) {
			return;
		}
		
		Database::install();
	}
	
	/**
	 * Registriert Admin-Menü
		
				// Lade das Plugin-interne UI-Framework (WDEV)
				\WDEV_Plugin_Ui::load( cp_defender()->getPluginUrl() . 'shared-ui/' );
		
	 */
	public function register_menu(): void {
		// Hauptseite (unter PS Security)
		add_submenu_page(
			'cp-defender',
			__( 'Anti-Spam', 'cpsec' ),
			__( 'Anti-Spam', 'cpsec' ),
			'manage_network_options',
			'cp-defender-antispam',
			array( $this, 'render_moderation_page' )
		);
		
		// Pattern-Seite
		add_submenu_page(
			'cp-defender',
			__( 'Spam-Patterns', 'cpsec' ),
			__( 'Patterns', 'cpsec' ),
			'manage_network_options',
			'cp-defender-antispam-patterns',
			array( $this, 'render_patterns_page' )
		);
		
		// Einstellungen
		add_submenu_page(
			'cp-defender',
			__( 'Anti-Spam Einstellungen', 'cpsec' ),
			__( 'Anti-Spam Einstellungen', 'cpsec' ),
			'manage_network_options',
			'cp-defender-antispam-settings',
			array( $this, 'render_settings_page' )
		);
		
		// Statistiken
		add_submenu_page(
			'cp-defender',
			__( 'Anti-Spam Statistiken', 'cpsec' ),
			__( 'Statistiken', 'cpsec' ),
			'manage_network_options',
			'cp-defender-antispam-stats',
			array( $this, 'render_stats_page' )
		);
	}
	
	/**
	 * Lädt Admin-Scripts
	 */
	public function enqueue_scripts( string $hook ): void {
		// Nur auf unseren Seiten laden
		if ( strpos( $hook, 'cp-defender-antispam' ) === false ) {
			return;
		}

		// Gleiche UI-Grundlage wie die restlichen Defender-Module.
		\WDEV_Plugin_Ui::load( cp_defender()->getPluginUrl() . 'shared-ui/' );
		wp_enqueue_style( 'defender' );
		
		wp_enqueue_style(
			'cp-defender-antispam',
			plugins_url( 'assets/css/anti-spam.css', CP_DEFENDER_FILE ),
			array( 'defender' ),
			CP_DEFENDER_VERSION
		);
		
		wp_enqueue_script(
			'cp-defender-antispam',
			plugins_url( 'assets/js/anti-spam.js', CP_DEFENDER_FILE ),
			array( 'jquery' ),
			CP_DEFENDER_VERSION,
			true
		);
		
		wp_localize_script( 'cp-defender-antispam', 'defenderAntiSpam', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'defender_antispam_nonce' ),
			'i18n' => array(
				'testing' => __( 'Teste Pattern...', 'cpsec' ),
				'confirm_delete' => __( 'Möchtest du die ausgewählten Patterns wirklich löschen?', 'cpsec' ),
			)
		) );
	}
	
	/**
	 * AJAX: Pattern testen
	 */
	public function ajax_test_pattern(): void {
		check_ajax_referer( 'defender_antispam_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_network_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Keine Berechtigung', 'cpsec' ) ) );
		}
		
		$regex = wp_unslash( $_POST['regex'] ?? '' );
		$type = sanitize_text_field( $_POST['type'] ?? '' );
		
		$result = Pattern::test( $regex, $type );
		
		wp_send_json_success( $result );
	}
	
	/**
	 * AJAX: Blog-Status togglen (Spam/Ham/Ignore)
	 */
	public function ajax_toggle_blog(): void {
		check_ajax_referer( 'defender_antispam_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_network_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Keine Berechtigung', 'cpsec' ) ) );
		}
		
		$blog_id = absint( $_POST['blog_id'] ?? 0 );
		$action = sanitize_text_field( $_POST['action_type'] ?? '' );
		
		if ( ! $blog_id ) {
			wp_send_json_error( array( 'message' => __( 'Ungültige Blog-ID', 'cpsec' ) ) );
		}
		
		switch ( $action ) {
			case 'spam':
				update_blog_status( $blog_id, 'spam', 1 );
				break;
			case 'unspam':
				update_blog_status( $blog_id, 'spam', 0 );
				break;
			case 'ignore':
				// Markiere als ignoriert in unserer Tabelle
				global $wpdb;
				$wpdb->update(
					$wpdb->base_prefix . 'defender_antispam_blogs',
					array( 'is_ignored' => 1 ),
					array( 'blog_id' => $blog_id ),
					array( '%d' ),
					array( '%d' )
				);
				break;
		}
		
		wp_send_json_success( array( 'message' => __( 'Status aktualisiert', 'cpsec' ) ) );
	}
	
	/**
	 * Dashboard Widget
	 */
	public function add_dashboard_widget(): void {
		if ( ! is_super_admin() ) {
			return;
		}
		
		wp_add_dashboard_widget(
			'cp_defender_antispam_widget',
			__( '🛡️ Anti-Spam Status', 'cpsec' ),
			array( $this, 'render_dashboard_widget' )
		);
	}
	
	/**
	 * Rendert Dashboard Widget
	 */
	public function render_dashboard_widget(): void {
		$counts = Blog_Log::get_counts();
		$total_blogs = $counts['total'];
		$spam_count = $counts['spam'];
		$suspicious_count = $counts['suspicious'];
		$patterns_count = count( Pattern::get_all() );
		
		echo '<div class="defender-antispam-widget">';
		echo '<ul>';
		echo '<li><strong>' . __( 'Aktive Blogs:', 'cpsec' ) . '</strong> ' . number_format_i18n( $total_blogs ) . '</li>';
		echo '<li><strong>' . __( 'Spam-Blogs:', 'cpsec' ) . '</strong> ' . number_format_i18n( $spam_count ) . '</li>';
		echo '<li><strong>' . __( 'Verdächtig:', 'cpsec' ) . '</strong> ' . number_format_i18n( $suspicious_count ) . '</li>';
		echo '<li><strong>' . __( 'Aktive Patterns:', 'cpsec' ) . '</strong> ' . number_format_i18n( $patterns_count ) . '</li>';
		echo '</ul>';
		echo '<p><a href="' . network_admin_url( 'admin.php?page=cp-defender-antispam' ) . '" class="button button-primary">' . __( 'Zur Moderation', 'cpsec' ) . '</a></p>';
		echo '</div>';
	}
	
	/**
	 * Rendert Moderation-Seite
	 */
	public function render_moderation_page(): void {
		require_once __DIR__ . '/../view/moderation.php';
	}
	
	/**
	 * Rendert Patterns-Seite
	 */
	public function render_patterns_page(): void {
		require_once __DIR__ . '/../view/patterns.php';
	}
	
	/**
	 * AJAX: Aktualisiert Disposable-Domain-Liste
	 */
	public function ajax_update_disposable_domains(): void {
		check_ajax_referer( 'defender_antispam_nonce', 'nonce' );
		
		if ( ! is_super_admin() ) {
			wp_send_json_error( array(
				'message' => __( 'Du bist nicht berechtigt, diese Aktion durchzuführen.', 'cpsec' ),
			), 403 );
		}
		
		$result = Disposable_Email::update_domain_list();
		
		if ( $result['success'] ) {
			wp_send_json_success( array(
				'message' => $result['message'],
				'count' => $result['count'],
			) );
		} else {
			wp_send_json_error( array(
				'message' => $result['message'],
			) );
		}
	}
	
	/**
	 * Rendert Einstellungen-Seite
	 */
	public function render_settings_page(): void {
		require_once __DIR__ . '/../view/settings.php';
	}
	
	/**
	 * Rendert Statistiken-Seite
	 */
	public function render_stats_page(): void {
		require_once __DIR__ . '/../view/stats.php';
	}
}
