<?php


namespace CP_Defender\Module\Hardener\Component;

use Hammer\Helper\HTTP_Helper;
use CP_Defender\Module\Hardener\Model\Settings;
use CP_Defender\Module\Hardener\Rule;

class XML_RPC_Protection extends Rule {
	static $slug = 'xmlrpc-protection';
	static $service;

	function getDescription() {
		$this->renderPartial( 'rules/xml-rpc-protection' );
	}

	function check() {
		return $this->getService()->check();
	}

	public function getTitle() {
		return __( 'XML-RPC-Schutz konfigurieren', 'cpsec' );
	}

	public function getService() {
		if ( null === self::$service ) {
			self::$service = new XML_RPC_Protection_Service();
		}

		return self::$service;
	}

	public function addHooks() {
		$this->add_action( 'processingHardener' . self::$slug, 'process' );
		$this->add_action( 'processRevert' . self::$slug, 'revert' );
		$this->add_action( 'processUpdate' . self::$slug, 'update' );

		$mode = XML_RPC_Protection_Service::getEffectiveMode();
		if ( XML_RPC_Protection_Service::MODE_DISABLED === $mode ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'xmlrpc_methods', array( $this, 'disableMethods' ) );
			return;
		}

		if ( XML_RPC_Protection_Service::MODE_MULTICALL === $mode || XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK === $mode ) {
			add_filter( 'xmlrpc_methods', array( $this, 'filterMethods' ) );
		}
	}

	public function process() {
		if ( ! $this->verifyNonce() ) {
			return;
		}

		$this->saveNetworkOptions();
		Settings::instance()->addToResolved( self::$slug );
	}

	public function update() {
		if ( ! $this->verifyNonce() ) {
			return;
		}

		$this->saveNetworkOptions();
	}

	public function revert() {
		if ( ! $this->verifyNonce() ) {
			return;
		}

		XML_RPC_Protection_Service::clearNetworkOptions();
		Settings::instance()->addToIssues( self::$slug );
	}

	private function saveNetworkOptions() {
		$mode = HTTP_Helper::retrieve_post( 'xmlrpc_protection_mode', XML_RPC_Protection_Service::MODE_OFF );
		$allow_site_overrides = HTTP_Helper::retrieve_post( 'allow_site_overrides', 0 ) === '1';
		XML_RPC_Protection_Service::setNetworkOptions( $mode, $allow_site_overrides );
	}

	public function disableMethods( $methods ) {
		return array();
	}

	public function filterMethods( $methods ) {
		unset( $methods['system.multicall'] );
		if ( XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK === XML_RPC_Protection_Service::getEffectiveMode() ) {
			unset( $methods['pingback.ping'] );
		}

		return $methods;
	}
}
