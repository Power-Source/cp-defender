<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module\Scan\Behavior\Pro;

use Hammer\Base\Behavior;
use Hammer\Helper\Log_Helper;
use CP_Defender\Module\Scan\Component\Scan_Api;
use CP_Defender\Module\Scan\Model\Result_Item;

class Vuln_Scan extends Behavior {
	protected $endPoint = "https://github.com/Power-Source/api/defender/v1/vulnerabilities";
	protected $model;

	public function processItemInternal( $args, $current ) {
		$model       = $args['model'];
		$this->model = $model;

		// Show progress in UI during vulnerability phase
		$model->currentFile = __( 'Prüfe bekannte Schwachstellen…', cp_defender()->domain );
		$this->scan();

		return true;
	}

	public function scan( $wp_version = null, $plugins = array(), $themes = array() ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_null( $wp_version ) ) {
			global $wp_version;
		}

		if ( empty( $plugins ) ) {
			//get all the plugins, even activate or not, as in network
			foreach ( get_plugins() as $slug => $plugin ) {
				$base_slug             = explode( '/', $slug ); //DIRECTORY_SEPARATOR wont work on windows
				$base_slug             = array_shift( $base_slug );
				$plugins[ $base_slug ] = $plugin['Version'];
			}
		}

		if ( empty( $themes ) ) {
			foreach ( wp_get_themes() as $theme ) {
				$themes[ $theme->get_template() ] = $theme->Version;
			}
		}

		// Check if WPScan API token is configured
		$settings = \CP_Defender\Module\Scan\Model\Settings::instance();
		$api_token = trim( $settings->wpscan_api_token );
		
		if ( empty( $api_token ) ) {
			// Limited Mode: perform basic version/outdated checks so scan does not stall
			$this->model->currentFile = __( 'Prüfe Versionsstände…', cp_defender()->domain );
			$this->runBasicVersionCheck( $wp_version, $plugins, $themes );
			return true;
		}

		// Use WPScan API for vulnerability checking
		$response = $this->checkWPScanAPI( $wp_version, $plugins, $themes, $api_token );

		if ( is_array( $response ) ) {
			if ( isset( $response['wordpress'] ) ) {
				$this->processWordPressVuln( $response['wordpress'] );
			}
			if ( isset( $response['plugins'] ) ) {
				$this->processPluginsVuln( $response['plugins'] );
			}
			if ( isset( $response['themes'] ) ) {
				$this->processThemesVuln( $response['themes'] );
			}
		}

		return true;
	}

	/**
	 * Check vulnerabilities via WPScan API
	 */
	private function checkWPScanAPI( $wp_version, $plugins, $themes, $api_token ) {
		$result = array(
			'wordpress' => array(),
			'plugins' => array(),
			'themes' => array()
		);

		// Check WordPress core
		$wp_response = wp_remote_get( 'https://wpscan.com/api/v3/wordpresses/' . $wp_version, array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Token token=' . $api_token
			)
		) );

		if ( ! is_wp_error( $wp_response ) && 200 == wp_remote_retrieve_response_code( $wp_response ) ) {
			$wp_data = json_decode( wp_remote_retrieve_body( $wp_response ), true );
			if ( isset( $wp_data[ $wp_version ]['vulnerabilities'] ) ) {
				$result['wordpress'] = $wp_data[ $wp_version ]['vulnerabilities'];
			}
		}

		// Check plugins
		foreach ( $plugins as $slug => $version ) {
			$plugin_response = wp_remote_get( 'https://wpscan.com/api/v3/plugins/' . $slug, array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Token token=' . $api_token
				)
			) );

			if ( ! is_wp_error( $plugin_response ) && 200 == wp_remote_retrieve_response_code( $plugin_response ) ) {
				$plugin_data = json_decode( wp_remote_retrieve_body( $plugin_response ), true );
				if ( isset( $plugin_data[ $slug ]['vulnerabilities'] ) ) {
					foreach ( $plugin_data[ $slug ]['vulnerabilities'] as $vuln ) {
						// Check if current version is vulnerable
						if ( $this->isVersionVulnerable( $version, $vuln ) ) {
							if ( ! isset( $result['plugins'][ $slug ] ) ) {
								$result['plugins'][ $slug ] = array();
							}
							$result['plugins'][ $slug ][] = $vuln;
						}
					}
				}
			}
		}

		// Check themes
		foreach ( $themes as $slug => $version ) {
			$theme_response = wp_remote_get( 'https://wpscan.com/api/v3/themes/' . $slug, array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Token token=' . $api_token
				)
			) );

			if ( ! is_wp_error( $theme_response ) && 200 == wp_remote_retrieve_response_code( $theme_response ) ) {
				$theme_data = json_decode( wp_remote_retrieve_body( $theme_response ), true );
				if ( isset( $theme_data[ $slug ]['vulnerabilities'] ) ) {
					foreach ( $theme_data[ $slug ]['vulnerabilities'] as $vuln ) {
						if ( $this->isVersionVulnerable( $version, $vuln ) ) {
							if ( ! isset( $result['themes'][ $slug ] ) ) {
								$result['themes'][ $slug ] = array();
							}
							$result['themes'][ $slug ][] = $vuln;
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Limited mode fallback: mark outdated plugins/themes/core as issues when no WPScan token exists.
	 */
	private function runBasicVersionCheck( $wp_version, $plugins, $themes ) {
		// Core updates
		if ( function_exists( 'get_core_updates' ) ) {
			$core_updates = get_core_updates( array( 'dismissed' => false ) );
			if ( is_array( $core_updates ) ) {
				foreach ( $core_updates as $update ) {
					if ( isset( $update->response ) && $update->response === 'upgrade' && isset( $update->current ) && isset( $update->version ) ) {
						$this->createOutdatedItem( 'wordpress', 'wordpress', $update->current, $update->version );
					}
				}
			}
		}

		// Plugin updates
		$plugin_updates = get_site_transient( 'update_plugins' );
		if ( is_object( $plugin_updates ) && isset( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
			foreach ( $plugin_updates->response as $plugin_file => $info ) {
				$slug = dirname( $plugin_file );
				if ( isset( $plugins[ $slug ] ) && isset( $info->new_version ) ) {
					$current_version = $plugins[ $slug ];
					$new_version     = $info->new_version;
					if ( version_compare( $current_version, $new_version, '<' ) ) {
						$this->createOutdatedItem( 'plugin', $slug, $current_version, $new_version );
					}
				}
			}
		}

		// Theme updates
		$theme_updates = get_site_transient( 'update_themes' );
		if ( is_object( $theme_updates ) && isset( $theme_updates->response ) && is_array( $theme_updates->response ) ) {
			foreach ( $theme_updates->response as $slug => $info ) {
				if ( isset( $themes[ $slug ] ) && isset( $info['new_version'] ) ) {
					$current_version = $themes[ $slug ];
					$new_version     = $info['new_version'];
					if ( version_compare( $current_version, $new_version, '<' ) ) {
						$this->createOutdatedItem( 'theme', $slug, $current_version, $new_version );
					}
				}
			}
		}

		return true;
	}

	/**
	 * Create a Result_Item entry for an outdated asset (plugin/theme/core).
	 */
	private function createOutdatedItem( $type, $slug, $current_version, $new_version ) {
		$item           = new Result_Item();
		$item->type     = 'vuln';
		$item->parentId = $this->model->id;
		$item->status   = Result_Item::STATUS_ISSUE;
		$item->raw      = array(
			'type' => $type,
			'slug' => $slug,
			'bugs' => array(
				array(
					'vuln_type' => __( 'Veraltete Version', cp_defender()->domain ),
					'title'     => sprintf( __( 'Update empfohlen: %s → %s', cp_defender()->domain ), $current_version, $new_version ),
					'fixed_in'  => $new_version,
				),
			),
		);
		$item->save();
	}

	/**
	 * Check if version is vulnerable based on WPScan data
	 */
	private function isVersionVulnerable( $version, $vuln ) {
		// WPScan provides fixed_in version
		if ( isset( $vuln['fixed_in'] ) && ! empty( $vuln['fixed_in'] ) ) {
			return version_compare( $version, $vuln['fixed_in'], '<' );
		}
		return true; // If no fix info, assume vulnerable
	}


	/**
	 * @param $issues
	 */
	private function processWordPressVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}
		$model           = new Result_Item();
		$model->type     = 'vuln';
		$model->parentId = $this->model->id;
		$model->status   = Result_Item::STATUS_ISSUE;
		$model->raw      = array(
			'type' => 'wordpress',
			'slug' => 'wordpress',
			'bugs' => array()
		);
		foreach ( $issues as $issue ) {
			$model->raw['bugs'][] = array(
				'vuln_type' => $issue['vuln_type'],
				'title'     => $issue['title'],
				'ref'       => $issue['references'],
				'fixed_in'  => $issue['fixed_in']
			);
		}

		$model->save();
	}

	/**
	 * @param $issues
	 */
	private function processThemesVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}

		foreach ( $issues as $slug => $bugs ) {
			if ( ( $id = Scan_Api::isIgnored( $slug ) ) ) {
				$status = Result_Item::STATUS_IGNORED;
				$model  = Result_Item::findByID( $id );
			} else {
				$status = Result_Item::STATUS_ISSUE;
				$model  = new Result_Item();
			}
			$model->parentId = $this->model->id;
			$model->type     = 'vuln';
			$model->status   = $status;
			$model->raw      = array(
				'type' => 'theme',
				'slug' => $slug,
				'bugs' => array()
			);
			if ( is_array( $bugs['confirmed'] ) ) {
				foreach ( $bugs['confirmed'] as $bug ) {
					$model->raw['bugs'][] = array(
						'vuln_type' => $bug['vuln_type'],
						'title'     => $bug['title'],
						'ref'       => $bug['references'],
						'fixed_in'  => $bug['fixed_in'],
					);
				}
			}
			if ( count( $model->raw['bugs'] ) ) {
				$model->save();
			}
		}
	}

	/**
	 * @param $issues
	 */
	private function processPluginsVuln( $issues ) {
		if ( empty( $issues ) ) {
			return;
		}
		foreach ( $issues as $slug => $bugs ) {
			if ( ( $id = Scan_Api::isIgnored( $slug ) ) ) {
				$status = Result_Item::STATUS_IGNORED;
				$model  = Result_Item::findByID( $id );
			} else {
				$status = Result_Item::STATUS_ISSUE;
				$model  = new Result_Item();
			}
			$model->parentId = $this->model->id;
			$model->type     = 'vuln';
			$model->status   = $status;
			$model->raw      = array(
				'type' => 'plugin',
				'slug' => $slug,
				'bugs' => array()
			);
			if ( is_array( $bugs['confirmed'] ) ) {
				foreach ( $bugs['confirmed'] as $bug ) {
					$model->raw['bugs'][] = array(
						'vuln_type' => $bug['vuln_type'],
						'title'     => $bug['title'],
						'ref'       => $bug['references'],
						'fixed_in'  => $bug['fixed_in'],
					);
				}
			}
			$model->save();
		}
	}

	/**
	 * @return array
	 */
	public function behaviors() {
		return array(
			'utils' => 'CP_Defender\Behavior\Utils'
		);
	}
}