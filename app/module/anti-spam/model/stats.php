<?php

namespace CP_Defender\Module\Anti_Spam\Model;

/**
 * Persistente Zaehler fuer Anti-Spam Block-Events.
 */
class Stats {
	const OPTION_KEY = 'cp_defender_antispam_stats';

	private static $defaults = array(
		'honeypot_blocked'          => 0,
		'disposable_signup_blocked' => 0,
		'disposable_comment_blocked'=> 0,
	);

	public static function get_all(): array {
		$current = get_site_option( self::OPTION_KEY, array() );
		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$stats = wp_parse_args( $current, self::$defaults );
		foreach ( $stats as $key => $value ) {
			$stats[ $key ] = max( 0, (int) $value );
		}

		return $stats;
	}

	public static function increment( string $key, int $amount = 1 ): void {
		if ( $amount < 1 ) {
			return;
		}

		$stats = self::get_all();
		if ( ! array_key_exists( $key, self::$defaults ) ) {
			return;
		}

		$stats[ $key ] = (int) $stats[ $key ] + $amount;
		update_site_option( self::OPTION_KEY, $stats );
	}
}
