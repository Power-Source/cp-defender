<?php


namespace CP_Defender\Module\Hardener\Component;

use CP_Defender\Component\Error_Code;
use CP_Defender\Module\Hardener\Model\Settings;
use CP_Defender\Module\Hardener\IRule_Service;
use CP_Defender\Module\Hardener\Rule_Service;

class Prevent_PHP_Service extends Rule_Service implements IRule_Service {
	const PROTECTION_ENABLED_KEY = 'prevent_php_protection_enabled';

	/**
	 * @return bool
	 */
	public function check() {
		$settings = Settings::instance();
		if ( ! $settings->getDValues( self::PROTECTION_ENABLED_KEY ) ) {
			return false;
		}

		if ( ! $this->hasExpectedProtectionFiles( $settings->active_server ) ) {
			$settings->setDValues( self::PROTECTION_ENABLED_KEY, null );

			return false;
		}

		return true;
	}

	/**
	 * Apache and LiteSpeed protections are only active while their managed
	 * .htaccess blocks remain present. NGINX and IIS rules are configured
	 * outside the PHP process and cannot be verified from here.
	 *
	 * @param string $server
	 *
	 * @return bool
	 */
	private function hasExpectedProtectionFiles( $server ) {
		if ( ! in_array( $server, array( 'apache', 'litespeed' ), true ) ) {
			return true;
		}

		$paths = array(
			WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.htaccess',
			ABSPATH . WPINC . DIRECTORY_SEPARATOR . '.htaccess',
		);
		if ( defined( 'UPLOADS' ) ) {
			$paths[] = ABSPATH . UPLOADS . DIRECTORY_SEPARATOR . '.htaccess';
		}

		foreach ( $paths as $path ) {
			if ( ! is_file( $path ) || false === strpos( (string) file_get_contents( $path ), '## PS Security - Protect PHP Executed ##' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function process() {
		return new \WP_Error( Error_Code::INVALID, __( "Dieser Vorgang ist auf Deinem aktuellen Server nicht möglich.", 'cpsec' ) );
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function revert() {
		return new \WP_Error( Error_Code::INVALID, __( "Das Zurücksetzen ist auf Deinem aktuellen Server nicht möglich.", 'cpsec' ) );
	}
}