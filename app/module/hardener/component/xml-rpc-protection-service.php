<?php


namespace CP_Defender\Module\Hardener\Component;

use Hammer\Helper\WP_Helper;
use CP_Defender\Module\Hardener\Model\Settings;

class XML_RPC_Protection_Service {
	const NETWORK_MODE_KEY = 'xmlrpc_protection_mode';
	const ALLOW_SITE_OVERRIDES_KEY = 'xmlrpc_allow_site_overrides';
	const NETWORK_CONFIGURED_KEY = 'xmlrpc_protection_configured';
	const SITE_MODE_OPTION = 'cp_defender_xmlrpc_protection_mode';

	const MODE_OFF = 'off';
	const MODE_MULTICALL = 'multicall';
	const MODE_MULTICALL_PINGBACK = 'multicall_pingback';
	const MODE_DISABLED = 'disabled';
	const MODE_INHERIT = 'inherit';

	public function check() {
		return self::isNetworkConfigured();
	}

	public static function getNetworkMode() {
		$mode = Settings::instance()->getDValues( self::NETWORK_MODE_KEY );

		return self::isValidMode( $mode ) ? $mode : self::MODE_OFF;
	}

	public static function setNetworkOptions( $mode, $allow_site_overrides ) {
		$settings = Settings::instance();
		$settings->data[ self::NETWORK_MODE_KEY ] = self::isValidMode( $mode ) ? $mode : self::MODE_OFF;
		$settings->data[ self::ALLOW_SITE_OVERRIDES_KEY ] = (bool) $allow_site_overrides;
		$settings->data[ self::NETWORK_CONFIGURED_KEY ] = true;
		$settings->save();
	}

	public static function isNetworkConfigured() {
		return (bool) Settings::instance()->getDValues( self::NETWORK_CONFIGURED_KEY );
	}

	public static function clearNetworkOptions() {
		$settings = Settings::instance();
		unset(
			$settings->data[ self::NETWORK_MODE_KEY ],
			$settings->data[ self::ALLOW_SITE_OVERRIDES_KEY ],
			$settings->data[ self::NETWORK_CONFIGURED_KEY ]
		);
		$settings->save();
	}

	public static function isSiteOverrideAllowed() {
		if ( ! is_multisite() || ! WP_Helper::is_network_activate( cp_defender()->plugin_slug ) ) {
			return false;
		}

		return (bool) Settings::instance()->getDValues( self::ALLOW_SITE_OVERRIDES_KEY );
	}

	public static function getSiteMode() {
		if ( ! self::isSiteOverrideAllowed() ) {
			return self::MODE_INHERIT;
		}

		$mode = get_option( self::SITE_MODE_OPTION, self::MODE_INHERIT );

		return self::isValidMode( $mode ) ? $mode : self::MODE_INHERIT;
	}

	public static function setSiteMode( $mode ) {
		if ( ! self::isSiteOverrideAllowed() ) {
			return false;
		}

		$mode = self::isValidMode( $mode ) ? $mode : self::MODE_INHERIT;
		update_option( self::SITE_MODE_OPTION, $mode );

		return true;
	}

	public static function getEffectiveMode() {
		$site_mode = self::getSiteMode();
		if ( self::MODE_INHERIT !== $site_mode ) {
			return $site_mode;
		}

		return self::getNetworkMode();
	}

	public static function isValidMode( $mode ) {
		return in_array( $mode, array(
			self::MODE_OFF,
			self::MODE_MULTICALL,
			self::MODE_MULTICALL_PINGBACK,
			self::MODE_DISABLED,
			self::MODE_INHERIT,
		), true );
	}
}
