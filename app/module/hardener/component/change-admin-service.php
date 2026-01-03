<?php

namespace CP_Defender\Module\Hardener\Component;

use CP_Defender\Component\Error_Code;
use CP_Defender\Module\Hardener\IRule_Service;
use CP_Defender\Module\Hardener\Rule_Service;

class Change_Admin_Service extends Rule_Service implements IRule_Service {
	/**
	 * @var null
	 */
	protected $username;

	/**
	 * @return mixed
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param mixed $username
	 */
	public function setUsername( $username ) {
		$this->username = $username;
	}

	public function __construct( $username = null ) {
		$this->username = $username;
	}

	/**
	 * @return bool
	 */
	public function check() {
		$user = get_user_by( 'login', 'admin' );

		return ( ! is_object( $user ) );
	}

	/**
	 * @return bool|\WP_Error
	 */
	public function process() {
		if ( is_wp_error( ( $error = $this->validate() ) ) ) {
			return $error;
		}

		$admin_data = get_user_by( 'login', 'admin' );

		//create new admin account
		global $wpdb;
		$wpdb->update( $wpdb->users, array(
			'ID'         => $admin_data->ID,
			'user_login' => trim( $this->username )
		), array(
			'ID' => $admin_data->ID
		) );

		if ( is_multisite() ) {
			$site_admins = get_site_option( 'site_admins' );
			if ( is_array( $site_admins ) ) {
				//replace the admin
				$a_key = array_search( strtolower( 'admin' ), array_map( 'strtolower', $site_admins ) );
				if ( isset( $site_admins[ $a_key ] ) ) {
					$site_admins[ $a_key ] = $this->username;
					//reupdate
					update_site_option( 'site_admins', $site_admins );
				}
			}
		}
		clean_user_cache( $admin_data->ID );

		return true;
	}

	/**
	 * @param null $username
	 *
	 * @return bool|\WP_Error
	 */
	public function validate( $username = null ) {
		if ( is_null( $username ) ) {
			$username = $this->username;
		}
		if ( strlen( $username ) == 0 ) {
			return new \WP_Error( Error_Code::VALIDATE, __( "Der Benutzername darf nicht leer sein!", cp_defender()->domain ) );
		}
		if ( strtolower( $username ) == 'admin' ) {
			return new \WP_Error( Error_Code::VALIDATE, __( "Du kannst admin nicht erneut als Benutzernamen verwenden!", cp_defender()->domain ) );
		}

		if ( ! validate_username( $username ) ) {
			return new \WP_Error( Error_Code::VALIDATE, __( "Der Benutzername ist ungültig!", cp_defender()->domain ) );
		}

		//now check if the username unique
		if ( username_exists( $username ) ) {
			return new \WP_Error( Error_Code::VALIDATE, __( "Der Benutzername existiert bereits!", cp_defender()->domain ) );
		}

		return true;
	}

	public function revert() {

	}

	public function listen() {

	}
}