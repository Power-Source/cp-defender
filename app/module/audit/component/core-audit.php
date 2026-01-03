<?php


namespace CP_Defender\Module\Audit\Component;

use CP_Defender\Behavior\Utils;
use CP_Defender\Module\Audit\Event_Abstract;

class Core_Audit extends Event_Abstract {
	const ACTION_ACTIVATED = 'activated', ACTION_DEACTIVATED = 'deactivated', ACTION_INSTALLED = 'installed', ACTION_UPGRADED = 'upgraded';
	const CONTEXT_THEME = 'ct_theme', CONTEXT_PLUGIN = 'ct_plugin', CONTEXT_CORE = 'ct_core';
	protected $type = 'system';

	public function get_hooks() {
		return array(
			'switch_theme'              => array(
				'args'        => array( 'new_name' ),
				'text'        => sprintf( esc_html__( "%s aktiviertes Design: %s", cp_defender()->domain ), '{{wp_user}}', '{{new_name}}' ),
				'level'       => self::LOG_LEVEL_NOTICE,
				'event_type'  => $this->type,
				'context'     => self::CONTEXT_THEME,
				'action_type' => self::ACTION_ACTIVATED,
			),
			'activated_plugin'          => array(
				'args'         => array( 'plugin' ),
				'text'         => sprintf( esc_html__( "%s aktiviertes Plugin: %s, Version %s", cp_defender()->domain ), '{{wp_user}}', '{{plugin_name}}', '{{plugin_version}}' ),
				'level'        => self::LOG_LEVEL_NOTICE,
				'event_type'   => $this->type,
				'action_type'  => self::ACTION_ACTIVATED,
				'context'      => self::CONTEXT_PLUGIN,
				'program_args' => array(
					'plugin_abs_path' => array(
						'callable' => array( '\CP_Defender\Module\Audit\Component\Core_Audit', 'get_plugin_abs_path' ),
						'params'   => array(
							'{{plugin}}',
						),
					),
					'plugin_name'     => array(
						'callable'        => 'get_plugin_data',
						'params'          => array(
							'{{plugin_abs_path}}',
						),
						'result_property' => 'Name'
					),
					'plugin_version'  => array(
						'callable'        => 'get_plugin_data',
						'params'          => array(
							'{{plugin_abs_path}}',
						),
						'result_property' => 'Version'
					),
				)
			),
			'deleted_plugin'            => array(
				'args'        => array( 'plugin_file', 'deleted' ),
				'text'        => sprintf( esc_html__( "%s hat das Plugin %s gelöscht", cp_defender()->domain ), '{{wp_user}}', '{{plugin_file}}' ),
				'level'       => self::LOG_LEVEL_NOTICE,
				'action_type' => self::ACTION_DEACTIVATED,
				'event_type'  => $this->type,
				'context'     => self::CONTEXT_PLUGIN,
			),
			'deactivated_plugin'        => array(
				'args'         => array( 'plugin' ),
				'text'         => sprintf( esc_html__( "%s deaktiviertes Plugin: %s, Version %s", cp_defender()->domain ), '{{wp_user}}', '{{plugin_name}}', '{{plugin_version}}' ),
				'level'        => self::LOG_LEVEL_NOTICE,
				'action_type'  => self::ACTION_DEACTIVATED,
				'event_type'   => $this->type,
				'context'      => self::CONTEXT_PLUGIN,
				'program_args' => array(
					'plugin_abs_path' => array(
						'callable' => array( '\CP_Defender\Module\Audit\Component\Core_Audit', 'get_plugin_abs_path' ),
						'params'   => array(
							'{{plugin}}',
						),
					),
					'plugin_name'     => array(
						'callable'        => 'get_plugin_data',
						'params'          => array(
							'{{plugin_abs_path}}',
						),
						'result_property' => 'Name'
					),
					'plugin_version'  => array(
						'callable'        => 'get_plugin_data',
						'params'          => array(
							'{{plugin_abs_path}}',
						),
						'result_property' => 'Version'
					),
				)
			),
			'upgrader_process_complete' => array(
				'args'        => array( 'upgrader', 'options' ),
				'level'       => self::LOG_LEVEL_NOTICE,
				'callback'    => array( '\CP_Defender\Module\Audit\Component\Core_Audit', 'process_installer' ),
				'action_type' => self::ACTION_UPGRADED,
				'event_type'  => $this->type
			),
			'wd_plugin/theme_changed'   => array(
				'args'        => array( 'type', 'object', 'file' ),
				'action_type' => 'update',
				'event_type'  => $this->type,
				'callback'    => array( '\CP_Defender\Module\Audit\Component\Core_Audit', 'process_content_changed' ),
			),
			'wd_checksum/new_file'      => array(
				'args'        => array( 'file' ),
				'action_type' => 'file_added',
				'event_type'  => $this->type,
				'context'     => self::CONTEXT_CORE,
				'text'        => sprintf( esc_html__( 'Eine neue Datei wurde hinzugefügt, Pfad %s', cp_defender()->domain ), '{{file}}' )
			),
			'wd_checksum_file_modified' => array(
				'args'        => array( 'file' ),
				'action_type' => 'file_modified',
				'event_type'  => $this->type,
				'context'     => self::CONTEXT_CORE,
				'text'        => sprintf( esc_html__( 'Eine Datei wurde geändert, Pfad %s', cp_defender()->domain ), '{{file}}' )
			)
		);
	}

	public static function process_content_changed() {
		$args   = func_get_args();
		$type   = $args[1]['type'];
		$object = $args[1]['object'];
		$file   = $args[1]['file'];

		return array(
			sprintf( esc_html__( '%s hat die Datei %s von %s %s aktualisiert', cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $file, $type, $object ),
			$type == 'plugin' ? self::CONTEXT_PLUGIN : self::CONTEXT_THEME
		);
	}

	private static function upgrade_core() {
		$update_core = get_site_transient( 'update_core' );
		if ( is_object( $update_core ) ) {
			$updates = $update_core->updates;
			$updates = array_shift( $updates );
			if ( is_object( $updates ) && property_exists( $updates, 'version' ) ) {
				return array(
					sprintf( esc_html__( "%s hat WordPress auf %s aktualisiert", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $updates->version ),
					self::CONTEXT_CORE
				);
			}
		}

		return false;
	}

	private static function bulk_upgrade( $upgrader, $options ) {
		if ( $options['type'] == 'theme' ) {
			$texts = array();
			foreach ( $options['themes'] as $t ) {
				$theme = wp_get_theme( $t );
				if ( is_object( $theme ) ) {
					$texts[] = sprintf( esc_html__( "%s auf %s", cp_defender()->domain ), $theme->Name, $theme->get( 'Version' ) );
				}
			}
			if ( count( $texts ) ) {
				return array(
					sprintf( esc_html__( "%s hat Themes aktualisiert: %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), implode( ', ', $texts ) ),
					self::CONTEXT_THEME
				);
			} else {
				return false;
			}
		} elseif ( $options['type'] == 'plugin' ) {
			$texts = array();
			foreach ( $options['plugins'] as $t ) {
				$plugin = get_plugin_data( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $t );
				if ( is_array( $plugin ) && isset( $plugin['Name'] ) && ! empty( $plugin['Name'] ) ) {
					$texts[] = sprintf( esc_html__( "%s auf %s", cp_defender()->domain ), $plugin['Name'], $plugin['Version'] );
				}
			}
			if ( count( $texts ) ) {
				return array(
					sprintf( esc_html__( "%s hat Plugins aktualisiert: %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), implode( ', ', $texts ) ),
					self::CONTEXT_PLUGIN
				);
			} else {
				return false;
			}
		}
	}

	private static function single_upgrade( $upgrader, $options ) {
		if ( isset( $upgrader->skin->theme ) ) {
			$theme = wp_get_theme( $upgrader->skin->theme );
			if ( is_object( $theme ) ) {
				$name    = $theme->Name;
				$version = $theme->get( 'Version' );

				return array(
					sprintf( esc_html__( "%s hat das Theme aktualisiert: %s, Version %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $name, $version ),
					self::CONTEXT_THEME
				);
			} else {
				return false;
			}
		} elseif ( isset( $upgrader->skin->plugin ) ) {
			$slug = $upgrader->skin->plugin;
			$data = get_plugin_data( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $slug );
			if ( is_array( $data ) ) {
				$name    = $data['Name'];
				$version = $data['Version'];

				return array(
					sprintf( esc_html__( "%s hat das Plugin aktualisiert: %s, Version %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $name, $version ),
					self::CONTEXT_PLUGIN
				);
			} else {
				return false;
			}
		}
	}

	private static function single_install( $upgrader, $options ) {
		if ( ! is_object( $upgrader->skin ) ) {
			return false;
		}
		if ( @is_object( $upgrader->skin->api ) ) {
			$name    = $upgrader->skin->api->name;
			$version = $upgrader->skin->api->version;
		} elseif ( ! empty( $upgrader->skin->result ) ) {
			if ( is_array( $upgrader->skin->result ) && isset( $upgrader->skin->result['destination_name'] ) ) {
				$name    = $upgrader->skin->result['destination_name'];
				$version = esc_html__( "unknown", cp_defender()->domain );
			} elseif ( is_object( $upgrader->skin->result ) && property_exists( $upgrader->skin->result, 'destination_name' ) ) {
				$name    = $upgrader->skin->result->destination_name;
				$version = esc_html__( "unknown", cp_defender()->domain );
			}
		}

		if ( ! isset( $name ) ) {
			return false;
		}

		if ( isset( $upgrader->skin->api->preview_url ) ) {
			return array(
				sprintf( esc_html__( "%s hat das Theme installiert: %s, Version %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $name, $version ),
				self::CONTEXT_THEME,
				self::ACTION_INSTALLED
			);
		} else {
			return array(
				sprintf( esc_html__( "%s hat das Plugin installiert: %s, Version %s", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $name, $version ),
				self::CONTEXT_PLUGIN,
				self::ACTION_INSTALLED
			);
		}

		return false;
	}

	/**
	 * @return string
	 */
	public static function process_installer() {
		$args     = func_get_args();
		$upgrader = $args[1]['upgrader'];
		$options  = $args[1]['options'];
		if ( $options['type'] == 'core' ) {
			return self::upgrade_core();
			//if this is core, we just create text and return
		} elseif ( isset( $options['bulk'] ) && $options['bulk'] == true ) {
			return self::bulk_upgrade( $upgrader, $options );
		} elseif ( $options['action'] == 'install' ) {
			return self::single_install( $upgrader, $options );
		} else {
			return self::single_upgrade( $upgrader, $options );
		}
	}

	public function dictionary() {
		return array(
			self::ACTION_DEACTIVATED => esc_html__( "deaktiviert", cp_defender()->domain ),
			self::ACTION_UPGRADED    => esc_html__( "aktualisiert", cp_defender()->domain ),
			self::ACTION_ACTIVATED   => esc_html__( "aktiviert", cp_defender()->domain ),
			self::ACTION_INSTALLED   => esc_html__( "installiert", cp_defender()->domain ),
			self::CONTEXT_THEME      => esc_html__( "Theme", cp_defender()->domain ),
			self::CONTEXT_PLUGIN     => esc_html__( "Plugin", cp_defender()->domain ),
			self::CONTEXT_CORE       => esc_html__( "WordPress", cp_defender()->domain ),
			'file_added'             => esc_html__( "Datei hinzugefügt", cp_defender()->domain ),
			'file_modified'          => esc_html__( "Datei geändert", cp_defender()->domain )
		);
	}

	public static function get_plugin_abs_path( $slug ) {
		if ( ! is_file( $slug ) ) {
			$slug = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug;
		}

		return $slug;
	}
}