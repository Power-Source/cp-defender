<?php


namespace CP_Defender\Module\Hardener\Component;

use Hammer\Helper\HTTP_Helper;
use CP_Defender\Module\Hardener\Model\Settings;
use CP_Defender\Module\Hardener\Rule;

class Change_Admin extends Rule {
	static $slug = 'change_admin';
	static $service;

	public function getDescription() {
		$this->renderPartial( 'rules/change-admin' );
	}

	public function check() {
		return $this->getService()->check();
	}

	public function addHooks() {
		$this->add_action( 'processingHardener' . self::$slug, 'process' );
	}

	public function revert() {
		// TODO: Implement revert() method.
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return __( "Standard-Administratorkonto ändern", cp_defender()->domain );
	}

	/**
	 *
	 */
	public function process() {
		if ( ! $this->verifyNonce() ) {
			return;
		}
		$username = HTTP_Helper::retrieve_post( 'username' );
		$this->getService()->setUsername( $username );
		$ret = $this->getService()->process();
		if ( is_wp_error( $ret ) ) {
			wp_send_json_error( array(
				'message' => $ret->get_error_message()
			) );
		} else {
			Settings::instance()->addToResolved( self::$slug );
			wp_send_json_success( array(
				'message' => sprintf( __( "Dein Administratorname hat sich geändert. Du musst dich <a href='" . wp_login_url() . "'><strong>%s</strong></a>.<br/>Dies wird nach <span class='hardener-timer'>10</span> Sekunden automatisch neu geladen.", cp_defender()->domain ), "neu anmelden" ),
				'reload'  => 10
			) );
		}
	}

	/**
	 * @return Change_Admin_Service
	 */
	public function getService() {
		if ( self::$service == null ) {
			self::$service = new Change_Admin_Service();
		}

		return self::$service;
	}
}