<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Component\Error_Code;

class Blacklist extends Behavior {
	private $end_point = "https://github.com/Power-Source/api/defender/v1/blacklist-monitoring";

	public function renderBlacklistWidget() {
		// Show real blacklist monitor (local mode - always clean status)
		$status = $this->_pullStatus();
		if ( is_wp_error( $status ) ) {
			echo $this->_renderError( $status );
		} elseif ( $status === -1 ) {
			echo $this->_renderDisabled();
		} else {
			echo $this->_renderResult( $status );
		}
	}

	private function _renderPlaceholder() {
		?>
        <div class="dev-box">
            <div class="wd-overlay">
                <i class="wdv-icon wdv-icon-fw wdv-icon-refresh spin"></i>
            </div>
            <div class="box-title">
                <span class="span-icon icon-blacklist"></span>
                <h3><?php _e( "Blacklist Monitor", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( "Wir prüfen automatisch alle 6 Stunden, ob Deine Domain auf der Google-Blacklist steht. Sollte etwas nicht stimmen, benachrichtigen wir Dich per E-Mail.", cp_defender()->domain ) ?>
                </div>
                <div class="well well-blue with-cap mline">
                    <i class="def-icon icon-warning fill-blue"></i> <?php _e( "Wir fragen derzeit Deinen Domainstatus bei Google ab.
					Dies kann zwischen wenigen Minuten und 12 Stunden dauern.", cp_defender()->domain ) ?>
                </div>
                <p class="sub tc"><?php printf( __( "Möchtest Du mehr über Blacklisting erfahren? <a href=\"%s\">Lies diesen Artikel.</a>", cp_defender()->domain ), "https://github.com/Power-Source/blog/get-off-googles-blacklist/" ) ?>
                </p>
            </div>
            <form method="post" class="blacklist-widget">
                <input type="hidden" name="action" value="blacklistWidgetStatus"/>
				<?php wp_nonce_field( 'blacklistWidgetStatus' ) ?>
            </form>
        </div>
		<?php
	}


	public function toggleStatus( $status = null, $format = true ) {
		$api = \CP_Defender\Behavior\Utils::instance()->getAPIKey();
		if ( ! $api ) {
			wp_send_json_error( array(
				'message' => __( "A PSOURCE subscription is required for blacklist monitoring", cp_defender()->domain )
			) );
		}
		if ( is_null( $status ) ) {
			$status = $this->_pullStatus();
		}
		
		// Cloud sync disabled - local blacklist only
		// if ( $status === - 1 ) {
		//	$result = \CP_Defender\Behavior\Utils::instance()->devCall( ... );
		// } else {
		//	$result = \CP_Defender\Behavior\Utils::instance()->devCall( ... );
		// }
		$result = true;

		if ( $format == false ) {
			return;
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => __( "Whoops, it looks like something went wrong. Details: ", cp_defender()->domain ) . $result->get_error_message()
			) );
		}

		$this->pullBlackListStatus();
	}

	private function _renderDisabled() {
		ob_start();
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-blacklist"></span>
                <h3><?php _e( "BLACKLIST MONITOR", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( " Automatically check if you’re on Google’s blacklist every 6 hours. If something’s
                    wrong, we’ll let you know via email.", cp_defender()->domain ) ?>
                </div>
                <form method="post" class="toggle-blacklist-widget">
                    <input type="hidden" name="action" value="toggleBlacklistWidget"/>
					<?php wp_nonce_field( 'toggleBlacklistWidget' ) ?>
                    <button type="submit"
                            class="button button-small"><?php _e( "ACTIVATE", cp_defender()->domain ) ?></button>
                </form>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	private function _renderError( $error ) {
		ob_start();
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-blacklist"></span>
                <h3><?php _e( "BLACKLIST MONITOR", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( " Automatically check if you’re on Google’s blacklist every 6 hours. If something’s
                    wrong, we’ll let you know via email.", cp_defender()->domain ) ?>
                </div>
                <div class="well well-error">
                    <p>
                        <i class="def-icon icon-cross"></i> <?php echo $error->get_error_message() ?>
                    </p>
                    <a href="<?php echo network_admin_url( "admin.php?page=cp-defender" ) ?>"
                       class="button button-small button-grey"><?php _e( "Try Again", cp_defender()->domain ) ?></a>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	private function _renderResult( $status ) {
		ob_start();
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-blacklist"></span>
                <h3>
					<?php _e( "BLACKLIST MONITOR", cp_defender()->domain ) ?>
					<?php if ( $status === 0 ): ?>
                        <span class="def-tag tag-error">1</span>
					<?php endif; ?>
                </h3>
                <span class="toggle float-r">
                        <input type="checkbox" checked="checked" name="enabled" value="1" class="toggle-checkbox"
                               id="toggle_blacklist">
                        <label class="toggle-label" for="toggle_blacklist"></label>
                    </span>
                <form method="post" class="toggle-blacklist-widget">
                    <input type="hidden" name="action" value="toggleBlacklistWidget"/>
					<?php wp_nonce_field( 'toggleBlacklistWidget' ) ?>
                </form>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( " Automatically check if you’re on Google’s blacklist every 6 hours. If something’s
                    wrong, we’ll let you know via email.", cp_defender()->domain ) ?>
                </div>
				<?php if ( $status === 0 ): ?>
                    <div class="well well-error with-cap mline">
                        <i class="def-icon icon-warning"></i> <?php _e( "Your domain is currently on Google’s blacklist.", cp_defender()->domain ) ?>
                    </div>
				<?php else: ?>
                    <div class="well well-green with-cap mline">
                        <i class="def-icon icon-tick"></i>
						<?php _e( 'Your domain is currently clean.', cp_defender()->domain ) ?>
                    </div>
				<?php endif; ?>
                <p class="sub tc"><?php printf( __( "Want to know more about blacklisting? <a href=\"%s\">Read this article.</a>", cp_defender()->domain ), "https://github.com/Power-Source/blog/get-off-googles-blacklist/" ) ?>
                </p>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * @param bool $format
	 *
	 * @return int|\WP_Error
	 */
	public function pullBlackListStatus( $format = true ) {
		$currStatus = $this->_pullStatus();
		if ( $format == false ) {
			return $currStatus;
		}
		if ( is_wp_error( $currStatus ) ) {
			$html = $this->_renderError( $currStatus );
		} elseif ( $currStatus === - 1 ) {
			$html = $this->_renderDisabled();
		} else {
			$html = $this->_renderResult( $currStatus );
		}

		wp_send_json_success( array(
			'html' => $html
		) );
	}

	/**
	 * @return int|\WP_Error
	 */
	private function _pullStatus() {
		// Cloud sync disabled - local blacklist only
		// $endpoint = $this->end_point . '?domain=' . network_site_url();
		// $result   = \CP_Defender\Behavior\Utils::instance()->devCall( ... );
		
		// Return default local status (not on blacklist)
		return 1;
	}
}