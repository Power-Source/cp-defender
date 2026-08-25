<?php


namespace CP_Defender\Module\Audit\Component;

use Hammer\Base\Component;
use Hammer\Helper\Log_Helper;
use Hammer\Helper\WP_Helper;
use CP_Defender\Behavior\Utils;
use CP_Defender\Component\Error_Code;
use CP_Defender\Module\Audit\Model\Settings;
use CP_Defender\Module\IP_Lockout\Model\Log_Model;

class Audit_API extends Component {
	const ACTION_ADDED = 'added', ACTION_UPDATED = 'updated', ACTION_DELETED = 'deleted', ACTION_TRASHED = 'trashed',
		ACTION_RESTORED = 'restored';
	const LOCAL_LOG_OPTION = 'wd_audit_local_logs';
	const LOCAL_LOGS_LIMIT = 2000;
	const PER_PAGE = 20;
	public static $end_point = 'audit.wpmudev.org';

	/**
	 * @param array $filter
	 * @param string $order_by
	 * @param string $order
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function pullLogs( $filter = array(), $order_by = 'timestamp', $order = 'desc', $nopaging = false ) {
		$logs = self::get_local_logs();
		$logs = array_merge( $logs, self::get_lockout_logs() );

		$logs = array_values( array_filter( $logs, function ( $row ) use ( $filter ) {
			if ( isset( $filter['date_from'] ) && strtotime( $filter['date_from'] ) > (int) $row['timestamp'] ) {
				return false;
			}

			if ( isset( $filter['date_to'] ) && strtotime( $filter['date_to'] ) < (int) $row['timestamp'] ) {
				return false;
			}

			if ( ! empty( $filter['user_id'] ) && (int) $row['user_id'] !== (int) $filter['user_id'] ) {
				return false;
			}

			if ( ! empty( $filter['ip'] ) && $row['ip'] !== $filter['ip'] ) {
				return false;
			}

			if ( ! empty( $filter['context'] ) && $row['context'] !== $filter['context'] ) {
				return false;
			}

			if ( ! empty( $filter['action_type'] ) && $row['action_type'] !== $filter['action_type'] ) {
				return false;
			}

			if ( ! empty( $filter['event_type'] ) ) {
				$event_types = is_array( $filter['event_type'] ) ? $filter['event_type'] : array( $filter['event_type'] );
				if ( ! in_array( $row['event_type'], $event_types ) ) {
					return false;
				}
			}

			return true;
		} ) );

		usort( $logs, function ( $a, $b ) use ( $order ) {
			$cmp = (int) $b['timestamp'] <=> (int) $a['timestamp'];

			return strtolower( $order ) === 'asc' ? -$cmp : $cmp;
		} );

		$total_items = count( $logs );
		if ( $nopaging ) {
			return array(
				'data'        => $logs,
				'total_items' => $total_items,
				'total_pages' => 1,
			);
		}

		$paged      = max( 1, (int) ( $filter['paged'] ?? 1 ) );
		$per_page   = self::PER_PAGE;
		$offset     = ( $paged - 1 ) * $per_page;
		$total_page = max( 1, (int) ceil( $total_items / $per_page ) );

		return array(
			'data'        => array_slice( $logs, $offset, $per_page ),
			'total_items' => $total_items,
			'total_pages' => $total_page,
		);
	}

	/**
	 * @param array $filter
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function pullLogsSummary( $filter = array() ) {
		$last_24_hours = self::pullLogs( array(
			'date_from' => date( 'Y-m-d H:i:s', strtotime( '-24 hours', current_time( 'timestamp' ) ) ),
			'date_to'   => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
		), 'timestamp', 'desc', true );
		$count        = is_array( $last_24_hours ) ? count( $last_24_hours['data'] ) : 0;

		return array(
			'total_items' => $count,
			'attempts'    => $count,
			'blocks'      => $count,
			'count'       => $count,
		);
	}

	private static function get_local_logs() {
		$logs = get_site_option( self::LOCAL_LOG_OPTION, array() );

		if ( ! is_array( $logs ) ) {
			return array();
		}

		return array_values( array_filter( $logs, function ( $row ) {
			return is_array( $row )
			       && isset( $row['timestamp'] )
			       && isset( $row['msg'] )
			       && isset( $row['event_type'] )
			       && isset( $row['action_type'] );
		} ) );
	}

	private static function get_lockout_logs() {
		$logs = Log_Model::findAll( array(
			'type' => array(
				Log_Model::AUTH_LOCK,
				Log_Model::LOCKOUT_404,
			),
		), 'id', 'DESC', '0,1000' );

		if ( ! is_array( $logs ) || empty( $logs ) ) {
			return array();
		}

		$mapped = array();
		foreach ( $logs as $log ) {
			if ( ! $log instanceof Log_Model ) {
				continue;
			}

			$action_type = $log->type === Log_Model::AUTH_LOCK ? 'login_lockout' : '404_lockout';
			$mapped[]    = array(
				'timestamp'   => (int) $log->date,
				'event_type'  => 'lockout',
				'action_type' => $action_type,
				'site_url'    => network_site_url(),
				'user_id'     => 0,
				'context'     => 'ip_lockout',
				'ip'          => (string) $log->ip,
				'msg'         => (string) $log->log,
				'blog_id'     => (int) $log->blog_id,
			);
		}

		return $mapped;
	}

	private static function persist_local_logs( $data ) {
		$events = self::get_local_logs();

		foreach ( $data as $row ) {
			if ( ! is_array( $row ) || empty( $row['msg'] ) ) {
				continue;
			}

			$events[] = array(
				'timestamp'   => isset( $row['timestamp'] ) ? (int) $row['timestamp'] : time(),
				'event_type'  => sanitize_text_field( $row['event_type'] ?? 'generic' ),
				'action_type' => sanitize_text_field( $row['action_type'] ?? self::ACTION_UPDATED ),
				'site_url'    => esc_url_raw( $row['site_url'] ?? network_site_url() ),
				'user_id'     => isset( $row['user_id'] ) ? (int) $row['user_id'] : 0,
				'context'     => sanitize_text_field( $row['context'] ?? '' ),
				'ip'          => sanitize_text_field( $row['ip'] ?? '' ),
				'msg'         => sanitize_text_field( $row['msg'] ?? '' ),
				'blog_id'     => isset( $row['blog_id'] ) ? (int) $row['blog_id'] : 1,
			);
		}

		if ( count( $events ) > self::LOCAL_LOGS_LIMIT ) {
			$events = array_slice( $events, - self::LOCAL_LOGS_LIMIT );
		}

		update_site_option( self::LOCAL_LOG_OPTION, $events );
	}

	/**
	 * Open a socket to DEV api
	 */
	public static function openSocket() {
		if ( ! isset( cp_defender()->global['sockets'] ) ) {
			$fp = @stream_socket_client( 'ssl://' . self::$end_point . ':443', $errno, $errstr,
				3, // timeout should be ignored when ASYNC
				STREAM_CLIENT_ASYNC_CONNECT );

			if ( is_resource( $fp ) ) {
				//socket_set_nonblock( $fp );
				cp_defender()->global['sockets'][] = $fp;
			}
		}
	}

	/**
	 * @return array
	 */
	public static function dictionary() {
		return array(
			self::ACTION_TRASHED  => esc_html__( "zerstört", cp_defender()->domain ),
			self::ACTION_UPDATED  => esc_html__( "aktualisiert", cp_defender()->domain ),
			self::ACTION_DELETED  => esc_html__( "gelöscht", cp_defender()->domain ),
			self::ACTION_ADDED    => esc_html__( "erstellt", cp_defender()->domain ),
			self::ACTION_RESTORED => esc_html__( "wiederhergestellt", cp_defender()->domain ),
			'login_lockout'       => esc_html__( "Login-Sperre", cp_defender()->domain ),
			'404_lockout'         => esc_html__( "404-Sperre", cp_defender()->domain ),
			'ip_lockout'          => esc_html__( "IP-Sperre", cp_defender()->domain ),
			'lockout'             => esc_html__( "Sperre", cp_defender()->domain ),
		);
	}

	public static function liveable_audit_log( $text ) {
		//first need to get the site id
		$site_id = 1;
		//rip out any html if any
		$text = esc_html( $text );

		$text = str_replace( '; ', '<br/>', $text );

		/**
		 * we continue to check anything with ID, usually it will be
		 * comment ID
		 * file URL
		 */

		return $text;
		//we got the site ID.
	}

	public static function get_event_type() {
		return WP_Helper::getArrayCache()->get( 'event_types', array() );
	}

	/**
	 * @param $slug
	 *
	 * @return mixed
	 */
	public static function get_action_text( $slug ) {
		$dic = WP_Helper::getArrayCache()->get( 'dictionary', array() );

		return isset( $dic[ $slug ] ) ? $dic[ $slug ] : $slug;
	}

	public static function time_since( $since ) {
		$since = time() - $since;
		if ( $since < 0 ) {
			$since = 0;
		}
		$chunks = array(
			array( 60 * 60 * 24 * 365, esc_html__( "Jahr" ) ),
			array( 60 * 60 * 24 * 30, esc_html__( "Monat" ) ),
			array( 60 * 60 * 24 * 7, esc_html__( "Woche" ) ),
			array( 60 * 60 * 24, esc_html__( 'Tag' ) ),
			array( 60 * 60, esc_html__( "Stunde" ) ),
			array( 60, esc_html__( "Minute" ) ),
			array( 1, esc_html__( "Sekunde" ) )
		);

		for ( $i = 0, $j = count( $chunks ); $i < $j; $i ++ ) {
			$seconds = $chunks[ $i ][0];
			$name    = $chunks[ $i ][1];
			if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
				break;
			}
		}

		$print = ( $count == 1 ) ? '1 ' . $name : "$count {$name}s";

		return $print;
	}

	/**
	 * Queue event data prepare for submitting
	 *
	 * @param $data
	 * since 1.1
	 */
	public static function queueEventsData( $data ) {
		$events   = WP_Helper::getArrayCache()->get( 'events_queue', array() );
		$events[] = $data;
		WP_Helper::getArrayCache()->set( 'events_queue', $events );
	}

	/**
	 * @param $data
	 */
	public static function curlToAPI( $data ) {
		// Cloud sync disabled - logs stored locally only
		// Utils::instance()->devCall( 'http://' . self::$end_point . '/logs/add_multiple', ... );
	}

	/**
	 * @return bool
	 */
	public static function isActive() {
		return Settings::instance()->enabled;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	public static function socketToAPI( $data ) {
		//$sockets = WP_Helper::getArrayCache()->get( 'sockets', array() );
		$sockets = isset( cp_defender()->global['sockets'] ) ? cp_defender()->global['sockets'] : array();
		//we will need to wait a little bit
		if ( count( $sockets ) == 0 ) {
			//fall back
			return false;
		}
		$start_time = microtime( true );
		$sks        = $sockets;
		$r          = null;
		$e          = null;
		if ( ( $socket_ready = stream_select( $r, $sks, $e, 1 ) ) === false ) {
			//this case error happen

			return false;
		}

		$fp = array_shift( $sockets );

		$uri  = '/logs/add_multiple';
		$vars = http_build_query( $data );

		fwrite( $fp, "POST " . $uri . "  HTTP/1.1\r\n" );
		fwrite( $fp, "Host: " . self::$end_point . "\r\n" );
		fwrite( $fp, "Content-Type: application/x-www-form-urlencoded\r\n" );
		fwrite( $fp, "Content-Length: " . strlen( $vars ) . "\r\n" );
		fwrite( $fp, "apikey:" . Utils::instance()->getAPIKey() . "\r\n" );
		fwrite( $fp, "Connection: close\r\n" );
		fwrite( $fp, "\r\n" );
		fwrite( $fp, $vars );
		stream_set_timeout( $fp, 5 );
		$res = '';
		while ( ! feof( $fp ) ) {
			$res      .= fgets( $fp, 1024 );
			$end_time = microtime( true );
			if ( $end_time - $start_time > 3 ) {
				fclose( $fp );
				break;
			}
			//Log_Helper::logger( fgets( $fp, 1024 ) );
		}

		return true;
	}

	/**
	 * @param bool $clearCron
	 *
	 * @return false|int
	 * @deprecated
	 */
	public static function getReportTime( $clearCron = true ) {
		if ( $clearCron ) {
			wp_clear_scheduled_hook( 'auditReportCron' );
		}
		$settings = Settings::instance();
		switch ( $settings->frequency ) {
			case '1':
				//check if the time is over or not, then send the date
				$timeString     = date( 'Y-m-d' ) . ' ' . $settings->time . ':00';
				$nextTimeString = date( 'Y-m-d', strtotime( 'tomorrow' ) ) . ' ' . $settings->time . ':00';
				break;
			case '7':
			default:
				$timeString     = date( 'Y-m-d', strtotime( $settings->day . ' this week' ) ) . ' ' . $settings->time . ':00';
				$nextTimeString = date( 'Y-m-d', strtotime( $settings->day . ' next week' ) ) . ' ' . $settings->time . ':00';
				break;
			case '30':
				$timeString     = date( 'Y-m-d', strtotime( $settings->day . ' this month' ) ) . ' ' . $settings->time . ':00';
				$nextTimeString = date( 'Y-m-d', strtotime( $settings->day . ' next month' ) ) . ' ' . $settings->time . ':00';
				break;
		}

		$toUTC = Utils::instance()->localToUtc( $timeString );
		if ( $toUTC <= time() ) {
			//already passed
			return Utils::instance()->localToUtc( $nextTimeString );
		} else {
			return $toUTC;
		}
	}

	/**
	 * We get all the hooks from internal component and add it to wp hook system on wp_load time
	 */
	public static function setupEvents() {
		//we only queue for
		if ( defined( 'DOING_CRON' ) && constant( 'DOING_CRON' ) == true ) {
			//this is cron, we only queue the core audit to catch auto update
			$events_class = array(
				new Core_Audit()
			);
		} else {
			$events_class = array(
				new Comment_Audit(),
				new Core_Audit(),
				new Media_Audit(),
				new Options_Audit(),
				new Post_Audit(),
				new Users_Audit()
			);
		}

		//we will build up the dictionary here
		$dictionary  = self::dictionary();
		$event_types = array();

		foreach ( $events_class as $class ) {
			$hooks      = $class->get_hooks();
			$dictionary = array_merge( $class->dictionary(), $dictionary );
			foreach ( $hooks as $key => $hook ) {
				$func = function () use ( $key, $hook, $class ) {
					//this is argurements of the hook
					$args = func_get_args();
					//this is hook data, defined in each events class
					$class->build_log_data( $key, $args, $hook );
				};
				add_action( $key, $func, 11, count( $hook['args'] ) );
				$event_types[] = $hook['event_type'];
			}
		}

		WP_Helper::getArrayCache()->set( 'event_types', array_unique( $event_types ) );
		WP_Helper::getArrayCache()->set( 'dictionary', $dictionary );
	}

	/**
	 * Store all events to WPMUDEV cloud
	 */
	public static function onCloud( $data ) {
		self::persist_local_logs( $data );

		if ( count( $data ) ) {
			self::openSocket();
			/**
			 * if data is more than one, means various event happened at once, we will need to group each by type
			 * and submit at bulk
			 */
			if ( count( $data ) > 1 ) {
				$groups     = array();
				$socket_log = array();
				foreach ( $data as $k => $val ) {
					if ( ! isset( $groups[ $val['event_type'] ] ) ) {
						$groups[ $val['event_type'] ] = array();
					}
					$groups[ $val['event_type'] ][] = $val;
				}
				//now regroup, and start to submit
				$new_data = array();
				foreach ( $groups as $k => $val ) {
					$tmp = array();
					foreach ( $val as $v ) {
						$tmp[] = $v['msg'];
					}
					$first        = array_shift( $val );
					$first['msg'] = implode( '; ', $tmp );
					$socket_log[] = $first['msg'];
					$new_data[]   = $first;
				}

				$data = $new_data;
			} elseif ( count( $data ) == 1 ) {
				$socket_log[] = $data[0]['msg'];
			}

			if ( self::socketToAPI( $data ) == false ) {
				self::curlToAPI( $data );
			}
		}
	}
}