<?php


namespace CP_Defender\Module\Hardener\Controller;

use Hammer\Base\Container;
use Hammer\Helper\HTTP_Helper;
use Hammer\Helper\Log_Helper;
use CP_Defender\Controller;
use CP_Defender\Module\Hardener;
use CP_Defender\Module\Hardener\Component\XML_RPC_Protection_Service;

class Main extends Controller {
	protected $slug = 'wdf-hardener';
	public $layout = 'layout';

	/**
	 * @return array
	 */
	public function behaviors() {
		return array(
			'utils' => '\CP_Defender\Behavior\Utils'
		);
	}

	/**
	 * Main constructor.
	 */
	public function __construct() {
		if ( $this->is_network_activate( cp_defender()->plugin_slug ) ) {
			$this->add_action( 'network_admin_menu', 'adminMenu' );
			$this->add_action( 'admin_menu', 'siteXmlrpcSettingsMenu' );
		} else {
			$this->add_action( 'admin_menu', 'adminMenu' );
		}
		$this->add_action( 'admin_post_cp_defender_save_xmlrpc_site_settings', 'saveSiteXmlrpcSettings' );

		if ( $this->isInPage() || $this->isSiteXmlrpcSettingsPage() ) {
			$this->add_action( 'defender_enqueue_assets', 'scripts', 11 );
		}

		$this->add_ajax_action( 'processHardener', 'processHardener' );
		$this->add_ajax_action( 'processRevert', 'processRevert' );
		$this->add_ajax_action( 'ignoreHardener', 'ignoreHardener' );
		$this->add_ajax_action( 'restoreHardener', 'restoreHardener' );
		$this->add_ajax_action( 'updateHardener', 'updateHardener' );
	}

	public function restoreHardener() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$slug = HTTP_Helper::retrieve_post( 'slug' );
		$rule = Hardener\Model\Settings::instance()->getRuleBySlug( $slug );
		if ( is_object( $rule ) ) {
			$rule->restore();
			wp_send_json_success( array(
				'message' => __( "Sicherheitsoptimierung erfolgreich wiederhergestellt.", 'cpsec' ),
				'issues'  => $this->getCount( 'issues' ),
				'fixed'   => $this->getCount( 'fixed' ),
				'ignore'  => $this->getCount( 'ignore' )
			) );
		}
	}

	public function ignoreHardener() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$slug = HTTP_Helper::retrieve_post( 'slug' );
		$rule = Hardener\Model\Settings::instance()->getRuleBySlug( $slug );
		if ( is_object( $rule ) ) {
			$rule->ignore();
			wp_send_json_success( array(
				'message' => __( "Sicherheitsoptimierung erfolgreich ignoriert.", 'cpsec' ),
				'issues'  => $this->getCount( 'issues' ),
				'fixed'   => $this->getCount( 'fixed' ),
				'ignore'  => $this->getCount( 'ignore' )
			) );
		}
	}

	public function processRevert() {
		if ( ! $this->checkPermission() ) {
			return;
		}
		$slug = HTTP_Helper::retrieve_post( 'slug' );
		do_action( "processRevert" . $slug );
		//fall back
		wp_send_json_success( array(
			'message' => __( "Sicherheitsanpassung erfolgreich rückgängig gemacht.", 'cpsec' ),
			'issues'  => $this->getCount( 'issues' ),
			'fixed'   => $this->getCount( 'fixed' ),
			'ignore'  => $this->getCount( 'ignore' )
		) );
	}

	/**
	 * Ajax to process or ignore a rule
	 */
	public function processHardener() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$slug 		= HTTP_Helper::retrieve_post( 'slug' );

		do_action( "processingHardener" . $slug );
		//fall back
		wp_send_json_success( array(
			'message' => __( "Sicherheitsoptimierung erfolgreich behoben.", 'cpsec' ),
			'issues'  => $this->getCount( 'issues' ),
			'fixed'   => $this->getCount( 'fixed' ),
			'ignore'  => $this->getCount( 'ignore' )
		) );
	}

	/**
	 * Update Hardener
	 * Update existing rules
	 */
	public function updateHardener() {
		if ( ! $this->checkPermission() ) {
			return;
		}

		$slug = HTTP_Helper::retrieve_post( 'slug' );

		do_action( "processUpdate" . $slug );
		//fall back
		wp_send_json_success( array(
			'message' => __( "Sicherheitsoptimierung erfolgreich aktualisiert.", 'cpsec' ),
			'issues'  => $this->getCount( 'issues' ),
			'fixed'   => $this->getCount( 'fixed' ),
			'ignore'  => $this->getCount( 'ignore' ),
			'update'  => false
		) );
	}

	/**
	 * Add submit admin page
	 */
	public function adminMenu() {
// Wenn netzwerkweit aktiviert, nur im Netzwerk-Admin anzeigen
if ( $this->is_network_activate( cp_defender()->plugin_slug ) && ! is_network_admin() ) {
return;
}

		$cap = is_multisite() ? 'manage_network_options' : 'manage_options';
		add_submenu_page( 'cp-defender', esc_html__( "Sicherheits-Tweaks", 'cpsec' ), esc_html__( "Sicherheits-Tweaks", 'cpsec' ), $cap, $this->slug, array(
			&$this,
			'actionIndex'
		) );

	}

	public function siteXmlrpcSettingsMenu() {
		if ( ! XML_RPC_Protection_Service::isSiteOverrideAllowed() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_management_page( esc_html__( "XML-RPC-Schutz", 'cpsec' ), esc_html__( "XML-RPC-Schutz", 'cpsec' ), 'manage_options', 'wdf-xmlrpc-protection', array(
			&$this,
			'renderSiteXmlrpcSettings'
		) );
	}

	public function renderSiteXmlrpcSettings() {
		if ( ! XML_RPC_Protection_Service::isSiteOverrideAllowed() || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Einstellungen zu verwalten.', 'cpsec' ) );
		}

		$this->render( 'xmlrpc-site-settings', array(
			'mode'         => XML_RPC_Protection_Service::getSiteMode(),
			'network_mode' => XML_RPC_Protection_Service::getNetworkMode(),
		) );
	}

	public function saveSiteXmlrpcSettings() {
		if ( ! XML_RPC_Protection_Service::isSiteOverrideAllowed() || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Du hast keine Berechtigung, diese Einstellungen zu verwalten.', 'cpsec' ) );
		}
		check_admin_referer( 'cp_defender_save_xmlrpc_site_settings' );

		XML_RPC_Protection_Service::setSiteMode( HTTP_Helper::retrieve_post( 'xmlrpc_protection_mode', XML_RPC_Protection_Service::MODE_INHERIT ) );
		wp_safe_redirect( admin_url( 'tools.php?page=wdf-xmlrpc-protection&updated=1' ) );
		exit;
	}

	private function isSiteXmlrpcSettingsPage() {
		return XML_RPC_Protection_Service::isSiteOverrideAllowed()
			&& isset( $GLOBALS['pagenow'] )
			&& 'tools.php' === $GLOBALS['pagenow']
			&& 'wdf-xmlrpc-protection' === HTTP_Helper::retrieve_get( 'page' );
	}

	/**
	 *
	 */
	public function actionIndex() {
		switch ( HTTP_Helper::retrieve_get( 'view' ) ) {
			case 'issues':
			default:
				$this->_renderIssues();
				break;
			case 'resolved':
				$this->_renderResolved();
				break;
			case 'notification':
				break;
			case 'ignored':
				$this->_renderIgnored();
				break;
		}
	}

	private function _renderIssues() {
		$this->render( 'issues' );
	}

	private function _renderResolved() {
		$this->render( 'resolved' );
	}

	private function _renderIgnored() {
		$this->render( 'ignore' );
	}

	private function _renderNotification() {

	}

	/**
	 * Enqueue scripts & styles
	 */
	public function scripts() {
		\WDEV_Plugin_Ui::load( cp_defender()->getPluginUrl() . 'shared-ui/' );
		wp_enqueue_script( 'defender' );
		wp_enqueue_script( 'hardener', cp_defender()->getPluginUrl() . 'app/module/hardener/js/scripts.js', array(
			'jquery'
		) );
		wp_enqueue_style( 'defender' );
	}

	/**
	 *
	 * @param $type
	 *
	 * @return int
	 */
	public function getCount( $type ) {
		$settings = Hardener\Model\Settings::instance();
		switch ( $type ) {
			case 'issues':
				return count( $settings->issues );
				break;
			case 'fixed':
				return count( $settings->fixed );
				break;
			case 'ignore':
				return count( $settings->ignore );
				break;
		}
	}
}