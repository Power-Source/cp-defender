<?php


namespace CP_Defender\Module\Hardener\Component;

use Hammer\Helper\WP_Helper;
use Hammer\Helper\HTTP_Helper;
use CP_Defender\Module\Hardener\Model\Settings;
use CP_Defender\Module\Hardener\Rule;
use CP_Defender\Module\Hardener\Component\Servers\Apache_Service;
use CP_Defender\Module\Hardener\Component\Servers\Iis_Service;

class Prevent_Php extends Rule {
	static $slug = 'prevent-php-executed';
	static $service;
	static $apache_service;
	static $iis_service;
	const EXCEPTION_WRITE_ERROR_KEY = 'prevent_php_exception_write_error';

	function getDescription() {
		$this->renderPartial( 'rules/prevent-php-executed' );
	}

	/**
	 * @return bool|false|mixed|null
	 */
	function check() {
		return $this->getService()->check();
	}

	/**
	 * @return string|void
	 */
	public function getTitle() {
		return __( "PHP-Ausführung verhindern", 'cpsec' );
	}


	function revert() {
		if ( ! $this->verifyNonce() ) {
			return;
		}
		$settings = Settings::instance();
		if ( in_array( $settings->active_server, array( 'apache', 'litespeed' ) ) ) {
			$service = $this->getApacheService();
			$service->setHtConfig( $settings->getNewHtConfig() );
		} else if ( $settings->active_server == 'iis-7' ) {
			$service = $this->getIisService();
		} else {
			$service = $this->getService();
		}
		$ret = $service->revert();
		if ( ! is_wp_error( $ret ) ) {
			if ( in_array( $settings->active_server, array( 'apache', 'litespeed' ) ) ) {
				$settings->saveExcludedFilePaths( array() );
				$settings->saveNewHtConfig( array() );
			}
			$settings->setDValues( Prevent_PHP_Service::PROTECTION_ENABLED_KEY, null );
			$settings->addToIssues( self::$slug );
		} else {
			wp_send_json_error( array(
				'message' => $ret->get_error_message()
			) );
		}
	}

	function addHooks() {
		$this->add_action( 'processingHardener' . self::$slug, 'process', 10, 2 );
		$this->add_action( 'processRevert' . self::$slug, 'revert' );
		$this->add_action( 'processUpdate' . self::$slug, 'update', 10, 2 );
	}

	function process() {
		if ( ! $this->verifyNonce() ) {
			return;
		}
		$file_paths = HTTP_Helper::retrieve_post( 'file_paths' ); //File paths to ignore. Apache and litespeed mainly
		if ( $file_paths ) {
			$file_paths = sanitize_textarea_field( $file_paths );
		} else {
			$file_paths = '';
		}
		$server = HTTP_Helper::retrieve_post( 'current_server' ); //Current server

		if ( in_array( $server, array( 'apache', 'litespeed' ) ) ) {
			$service = $this->getApacheService();
			$service->setExcludeFilePaths( $file_paths ); //Set the paths
		} else if ( $server == 'iis-7' ) {
			$service = $this->getIisService();
		} else {
			$service = $this->getService();
		}
		$ret = $service->process();
		if ( ! is_wp_error( $ret ) ) {
			$settings = Settings::instance();
			if ( in_array( $server, array( 'apache', 'litespeed' ) ) ) {
				$settings->saveExcludedFilePaths( $service->getExcludedFilePaths() );
				$settings->saveNewHtConfig( $service->getNewHtConfig() );
			}
			$settings->setActiveServer( $server );
			$settings->setDValues( Prevent_PHP_Service::PROTECTION_ENABLED_KEY, true );
			$settings->addToResolved( self::$slug );
		} else {
			wp_send_json_error( array(
				'message' => $ret->get_error_message()
			) );
		}
	}

	function update() {
		if ( ! $this->verifyNonce() ) {
			return;
		}
		$settings   = Settings::instance();

		$file_paths = HTTP_Helper::retrieve_post( 'file_paths' ); //File paths to ignore. Apache and litespeed mainly
		if ( $file_paths ) {
			$file_paths = sanitize_textarea_field( $file_paths );
		} else {
			$file_paths = '';
		}

		$server = HTTP_Helper::retrieve_post( 'current_server' ); //Current server

		if ( in_array( $server, array( 'apache', 'litespeed' ) ) ) {
			$service = $this->getApacheService();
			$service->setHtConfig( $settings->getNewHtConfig() ); //Set the previous template
			$ret = $service->unProtectContentDir(); //revert first
			if ( is_wp_error( $ret ) ) {
				$settings->setDValues( self::EXCEPTION_WRITE_ERROR_KEY, $ret->get_error_message() );
				wp_send_json_error( array(
					'message' => $ret->get_error_message()
				) );
				return;
			}
			$service->setExcludeFilePaths( $file_paths ); //Set the paths
		} else {
			$service = $this->getService();
		}
		$ret = $service->process();
		if ( ! is_wp_error( $ret ) ) {
			if ( in_array( $server, array( 'apache', 'litespeed' ) ) ) {
				$settings->saveExcludedFilePaths( $service->getExcludedFilePaths() );
				$settings->saveNewHtConfig( $service->getNewHtConfig() );
			}
			$settings->setActiveServer( $server );
			$settings->setDValues( Prevent_PHP_Service::PROTECTION_ENABLED_KEY, true );
			$settings->setDValues( self::EXCEPTION_WRITE_ERROR_KEY, null );
			$settings->save();
		} else {
			$settings->setDValues( self::EXCEPTION_WRITE_ERROR_KEY, $ret->get_error_message() );
			wp_send_json_error( array(
				'message' => $ret->get_error_message()
			) );
		}
	}

	/**
	 * @return Prevent_PHP_Service
	 */
	public function getService() {
		if ( self::$service == null ) {
			self::$service = new Prevent_PHP_Service();
		}

		return self::$service;
	}

	/**
	 * @return Apache_Service
	 */
	public function getApacheService() {
		if ( self::$apache_service == null ) {
			self::$apache_service = new Apache_Service();
		}

		return self::$apache_service;
	}

	/**
	 * @return Iis_Service
	 */
	public function getIisService() {
		if ( self::$iis_service == null ) {
			self::$iis_service = new Iis_Service();
		}

		return self::$iis_service;
	}
}