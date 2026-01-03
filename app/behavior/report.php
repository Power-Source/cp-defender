<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Module\Scan\Model\Settings;

class Report extends Behavior {
	public function renderReportWidget() {
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-report"></span>
                <h3><?php _e( "REPORTING", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content">
                <div class="line">
					<?php _e( "Get tailored security reports delivered to your inbox so you don’t have to worry
                    about checking in.", cp_defender()->domain ) ?>
                </div>
                <div class="row">
                    <div class="col-half">
						<?php $this->getScanReport() ?>
                    </div>
                    <div class="col-half">
						<?php $this->getAuditReport(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-half">
						<?php $this->getIpLockoutReport(); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function getIpLockoutReport() {
		$settings = \CP_Defender\Module\IP_Lockout\Model\Settings::instance();
		$class    = null;
		if ( $settings->report == false ) {
			$class = 'feature-disabled with-corner';
		}
		?>
        <div <?php echo $this->getLockoutTooltips() ?>
                class="report-status <?php echo $class ?>">
            <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'reporting' ) ) ?>">
                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/lockout-pre.svg">
                <strong><?php _e( "IP LOCKOUTS", cp_defender()->domain ) ?></strong>
				<?php if ( \CP_Defender\Module\IP_Lockout\Model\Settings::instance()->report ): ?>
                    <span class="def-tag tag-active">
                               <i class="def-icon icon-tick"></i>
						<?php
						switch ( \CP_Defender\Module\IP_Lockout\Model\Settings::instance()->report_frequency ) {
							case '1':
								_e( "Daily", cp_defender()->domain );
								break;
							case '7':
								_e( "Weekly", cp_defender()->domain );
								break;
							case '30':
								_e( "Monthly", cp_defender()->domain );
								break;
						}
						?>
                                </span>
					<?php
				else:?>
                    <span class="def-tag tag-inactive">
                                        <?php _e( "Inactive", cp_defender()->domain ) ?>
                                    </span>
                    <div tooltip="<?php esc_attr_e( "Get a daily, weekly or monthly summary of lockouts that have occurred within the report period." ) ?>"
                         class="corner">
                        <i class="def-icon icon-warning"></i>
                    </div>
				<?php endif; ?>
            </a>
        </div>
		<?php
	}

	public function getAuditReport() {
		$class = null;
		if ( \CP_Defender\Module\Audit\Model\Settings::instance()->enabled == false ) {
			$class = 'with-corner feature-disabled';
		} elseif ( \CP_Defender\Module\Audit\Model\Settings::instance()->notification == false ) {
			$class = 'feature-disabled';
		}
		?>
        <div <?php echo $this->getAuditToolTip() ?>
                class="report-status <?php echo $class ?>">
            <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-logging', array( 'view' => 'report' ) ) ?>">
                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/audit-pre.svg">
                <strong><?php _e( "AUDIT LOGGING", cp_defender()->domain ) ?></strong>
				<?php if ( \CP_Defender\Module\Audit\Model\Settings::instance()->enabled == false ): ?>
                    <div tooltip="<?php esc_attr_e( "To activate this report you must first enable the Audit Logging module." ) ?>"
                         class="corner">
                        <i class="def-icon icon-warning"></i>
                    </div>
				<?php elseif ( \CP_Defender\Module\Audit\Model\Settings::instance()->notification ): ?>
                    <span class="def-tag tag-active">
                                            <i class="def-icon icon-tick"></i>
						<?php
						switch ( \CP_Defender\Module\Audit\Model\Settings::instance()->frequency ) {
							case '1':
								_e( "Daily", cp_defender()->domain );
								break;
							case '7':
								_e( "Weekly", cp_defender()->domain );
								break;
							case '30':
								_e( "Monthly", cp_defender()->domain );
								break;
						}
						?>
                                </span>
					<?php
				else:?>
                    <span class="def-tag tag-inactive">
                        <?php _e( "Inactive", cp_defender()->domain ) ?>
                    </span>
				<?php endif; ?>
            </a>
        </div>
		<?php
	}

	private function getScanReport() {
		$class    = Settings::instance()->notification == false ? 'feature-disabled' : null;
		$tooltips = $this->getScanToolTip();
		?>
        <div <?php echo $tooltips ?>
                class="report-status <?php echo $class ?>">
            <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan', array( 'view' => 'reporting' ) ) ?>">
                <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/scanning-pre.svg">
                <strong><?php _e( "FILE SCANNING", cp_defender()->domain ) ?></strong>
				<?php if ( Settings::instance()->notification ): ?>
                    <span class="def-tag tag-active">
                                        <i class="def-icon icon-tick"></i>
						<?php
						switch ( Settings::instance()->frequency ) {
							case '1':
								_e( "Daily", cp_defender()->domain );
								break;
							case '7':
								_e( "Weekly", cp_defender()->domain );
								break;
							case '30':
								_e( "Monthly", cp_defender()->domain );
								break;
						}
						?>
                                        </span>
					<?php
				else:?>
                    <span class="def-tag tag-inactive">
                                            <?php _e( "Inactive", cp_defender()->domain ) ?>
                                        </span>
				<?php endif; ?>
            </a>
        </div>
		<?php
	}

	/**
	 * @return null|string
	 */
	private function getScanToolTip() {
		$isPre    = \CP_Defender\Behavior\Utils::instance()->getAPIKey();
		$settings = Settings::instance();
		$active   = $settings->notification;
		if ( ! $isPre || ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Scan reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->frequency == 1 ? $this->frequencyToText( $settings->frequency ) . '/' . $this->formatTime( $settings->time ) : $this->frequencyToText( $settings->frequency ) . '/' . $settings->day . '/' . $this->formatTime( $settings->time ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	private function getAuditToolTip() {
		$settings = \CP_Defender\Module\Audit\Model\Settings::instance();
		$active   = $settings->notification && $settings->enabled;
		if ( ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Audit reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->frequency == 1 ? $this->frequencyToText( $settings->frequency ) . '/' . $this->formatTime( $settings->time ) : $this->frequencyToText( $settings->frequency ) . '/' . $settings->day . '/' . $this->formatTime( $settings->time ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	private function getLockoutTooltips() {
		$settings = \CP_Defender\Module\IP_Lockout\Model\Settings::instance();
		$active   = $settings->report && ( $settings->detect_404 || $settings->login_protection );
		if ( ! $active ) {
			return null;
		}

		$toolstip = sprintf( __( "Lockout reports are active scheduled to send %s", cp_defender()->domain ),
			$settings->report_frequency == 1 ? $this->frequencyToText( $settings->report_frequency ) . '/' . $this->formatTime( $settings->report_time ) : $this->frequencyToText( $settings->report_frequency ) . '/' . $settings->report_day . '/' . $this->formatTime( $settings->report_time ) );
		$toolstip = strlen( $toolstip ) ? ' tooltip="' . esc_attr( $toolstip ) . '" ' : null;

		return $toolstip;
	}

	/**
	 * @param $freq
	 *
	 * @return string
	 */
	private function frequencyToText( $freq ) {
		$text = '';
		switch ( $freq ) {
			case 1:
				$text = __( "daily", cp_defender()->domain );
				break;
			case 7:
				$text = __( "weekly", cp_defender()->domain );
				break;
			case 30:
				$text = __( "monthly", cp_defender()->domain );
				break;
		}

		return $text;
	}

	/**
	 * Format time using WordPress localization; avoids deprecated strftime().
	 */
	private function formatTime( $time ) {
		$timestamp = strtotime( $time );
		if ( $timestamp === false ) {
			return $time;
		}

		return date_i18n( 'g:i A', $timestamp );
	}
}