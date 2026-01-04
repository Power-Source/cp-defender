<?php
/**
 * Plugin Name: PS Security
 * Plugin URI: https://power-source.github.io/cp-defender/
 * Version:     1.0.1
 * Description: Erhalte regelmäßige Sicherheitsüberprüfungen, Schwachstellenberichte, Sicherheitsempfehlungen und individuelle Sicherheitsmaßnahmen für Deine Webseite – mit nur wenigen Klicks. PS Security ist Dein Analyst und Sicherheitsexperte, der rund um die Uhr für Dich da ist.
 * Author:      PSOURCE
 * Author URI:  https://github.com/Power-Source
 * License:     GNU General Public License (Version 2 - GPLv2)
 * Text Domain: cpsec
 * Network: true
 */

// PS Update Manager - Hinweis wenn nicht installiert
add_action( 'admin_notices', function() {
    // Prüfe ob Update Manager aktiv ist
    if ( ! function_exists( 'ps_register_product' ) && current_user_can( 'install_plugins' ) ) {
        $screen = get_current_screen();
        if ( $screen && in_array( $screen->id, array( 'plugins', 'plugins-network' ) ) ) {
            // Prüfe ob bereits installiert aber inaktiv
            $plugin_file = 'ps-update-manager/ps-update-manager.php';
            $all_plugins = get_plugins();
            $is_installed = isset( $all_plugins[ $plugin_file ] );
            
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo '<strong>PSOURCE MANAGER:</strong> ';
            
            if ( $is_installed ) {
                // Installiert aber inaktiv - Aktivierungs-Link
                $activate_url = wp_nonce_url(
                    admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $plugin_file ) ),
                    'activate-plugin_' . $plugin_file
                );
                echo sprintf(
                    __( 'Aktiviere den <a href="%s">PS Update Manager</a> für automatische Updates von GitHub.', 'psource-chat' ),
                    esc_url( $activate_url )
                );
            } else {
                // Nicht installiert - Download-Link
                echo sprintf(
                    __( 'Installiere den <a href="%s" target="_blank">PS Update Manager</a> für automatische Updates aller PSource Plugins & Themes.', 'psource-chat' ),
                    'https://github.com/Power-Source/ps-update-manager/releases/latest'
                );
            }
            
            echo '</p></div>';
        }
    }
});

class CP_Defender {

	/**
	 * Store the CP_Defender object for singleton implement
	 *
	 * @var CP_Defender
	 */
	private static $_instance;
	/**
	 * @var string
	 */
	private $plugin_path;

	/**
	 * @return string
	 */
	public function getPluginPath() {
		return $this->plugin_path;
	}

	/**
	 * @return string
	 */
	public function getPluginUrl() {
		return $this->plugin_url;
	}

	/**
	 * @var string
	 */
	private $plugin_url;
	/**
	 * @var string
	 */
	public $domain = 'cpsec';

	/**
	 * @var string
	 */
	public $version = "1.5";

	/**
	 * @var string
	 */
	public $isFree = false;
	/**
	 * @var array
	 */
	public $global = array();
	/**
	 * @var string
	 */
	public $plugin_slug = 'cp-defender/cp-defender.php';

	public $db_version = "1.5";

	/**
	 * @return CP_Defender
	 */
	public static function instance() {
		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new CP_Defender();
		}

		return self::$_instance;
	}

	/**
	 * CP_Defender constructor.
	 */
	private function __construct() {
		$this->initVars();
		$this->includeVendors();
		$this->autoload();
		add_action( 'admin_enqueue_scripts', array( &$this, 'register_styles' ) );
		add_action( 'plugins_loaded', array( &$this, 'loadTextdomain' ) );
		$phpVersion = phpversion();
		if ( version_compare( $phpVersion, '5.3', '>=' ) ) {
			include_once $this->getPluginPath() . 'main-activator.php';
			$this->global['bootstrap'] = new WD_Main_Activator( $this );
		} else {
			include_once $this->getPluginPath() . 'legacy-activator.php';
			$this->global['bootstrap'] = new WD_Legacy_Activator( $this );
		}
	}

	public function loadTextdomain() {
		load_plugin_textdomain( $this->domain, false, $this->plugin_path . 'languages' );
		
		// Replace "WordPress" with "ClassicPress" in all plugin texts when running on ClassicPress
		if ( function_exists( 'classicpress_version' ) ) {
			add_filter( 'gettext', array( &$this, 'classicpress_text_replacer' ), 20, 3 );
			add_filter( 'ngettext', array( &$this, 'classicpress_plural_replacer' ), 20, 5 );
		}
	}
	
	/**
	 * Replace WordPress with ClassicPress in all plugin texts
	 */
	public function classicpress_text_replacer( $translated, $text, $domain ) {
		// Only modify our own plugin's texts
		if ( $domain !== $this->domain && $domain !== 'default' ) {
			return $translated;
		}
		
		// Replace WordPress with ClassicPress
		$translated = str_replace( 'WordPress', 'ClassicPress', $translated );
		$translated = str_replace( 'wordpress', 'classicpress', $translated );
		
		return $translated;
	}
	
	/**
	 * Replace WordPress with ClassicPress in plural texts
	 */
	public function classicpress_plural_replacer( $translated, $single, $plural, $number, $domain ) {
		// Only modify our own plugin's texts
		if ( $domain !== $this->domain && $domain !== 'default' ) {
			return $translated;
		}
		
		// Replace WordPress with ClassicPress
		$translated = str_replace( 'WordPress', 'ClassicPress', $translated );
		$translated = str_replace( 'wordpress', 'classicpress', $translated );
		
		return $translated;
	}

	/**
	 * Init values
	 */
	private function initVars() {
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
	}

	/**
	 * Including vendors
	 */
	private function includeVendors() {
		$phpVersion = phpversion();
		if ( version_compare( $phpVersion, '5.3', '>=' ) && ! function_exists( 'initCacheEngine' ) ) {
			include_once $this->plugin_path . 'vendor' . DIRECTORY_SEPARATOR . 'hammer' . DIRECTORY_SEPARATOR . 'bootstrap.php';
		}

		include_once $this->plugin_path . 'shared-ui/plugin-ui.php';

	}

	/**
	 * Register the autoload
	 */
	private function autoload() {
		spl_autoload_register( array( &$this, '_autoload' ) );
	}

	/**
	 * Register globally css, js will be load on each module
	 */
	public function register_styles() {
		wp_enqueue_style( 'defender-menu', $this->getPluginUrl() . 'assets/css/defender-icon.css' );

		$css_files = array(
			'defender' => $this->plugin_url . 'assets/css/styles.css'
		);

		foreach ( $css_files as $slug => $file ) {
			wp_register_style( $slug, $file, array(), $this->version );
		}

		$js_files = array(
			'defender' => $this->plugin_url . 'assets/js/scripts.js'
		);

		foreach ( $js_files as $slug => $file ) {
			wp_register_script( $slug, $file, array(), $this->version );
		}

		do_action( 'defender_enqueue_assets' );
	}

	/**
	 * @param $class
	 */
	public function _autoload( $class ) {
		$base_path = __DIR__ . DIRECTORY_SEPARATOR;
		$pools     = explode( '\\', $class );

		if ( $pools[0] != 'CP_Defender' ) {
			return;
		}
		if ( $pools[1] == 'Vendor' ) {
			unset( $pools[0] );
		} else {
			$pools[0] = 'App';
		}

		//build the path
		$path = implode( DIRECTORY_SEPARATOR, $pools );
		$path = $base_path . strtolower( str_replace( '_', '-', $path ) ) . '.php';
		if ( file_exists( $path ) ) {
			include_once $path;
		}
	}
}

//if we found defender free, then deactivate it
if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( is_plugin_active( 'defender-security/cp-defender.php' ) ) {
	deactivate_plugins( array( 'defender-security/cp-defender.php' ) );
	update_site_option( 'defenderJustUpgrade', 1 );
}

if ( ! function_exists( 'cp_defender' ) ) {

	/**
	 * Shorthand to get the instance
	 * @return CP_Defender
	 */
	function cp_defender() {
		return CP_Defender::instance();
	}

	//init
	cp_defender();

	function cp_defender_deactivate() {
		//we disable any cron running
		wp_clear_scheduled_hook( 'processScanCron' );
		wp_clear_scheduled_hook( 'lockoutReportCron' );
		wp_clear_scheduled_hook( 'auditReportCron' );
		wp_clear_scheduled_hook( 'cleanUpOldLog' );
		wp_clear_scheduled_hook( 'scanReportCron' );
	}

	function cp_defender_activate() {

		$phpVersion = phpversion();
		if ( version_compare( $phpVersion, '5.3', '>=' ) ) {
			cp_defender()->global['bootstrap']->activationHook();
		}
	}

	register_deactivation_hook( __FILE__, 'cp_defender_deactivate' );
	register_activation_hook( __FILE__, 'cp_defender_activate' );
}