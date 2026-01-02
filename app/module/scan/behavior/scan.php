<?php
namespace CP_Defender\Module\Scan\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Behavior\Utils;
use CP_Defender\Module\Scan\Component\Scan_Api;
use CP_Defender\Module\Scan\Model\Result_Item;
use CP_Defender\Module\Scan\Model\Settings;

class Scan extends Behavior {
	private $lastScan;
	private $activeScan;
	private $settled = false;
	private $countAll;

	private function pullStatus() {
		if ( $this->settled == false ) {
			$this->activeScan = Scan_Api::getActiveScan();
			$this->lastScan   = Scan_Api::getLastScan();
			$this->countAll   = is_object( $this->lastScan ) ? $this->lastScan->countAll( Result_Item::STATUS_ISSUE ) : 0;
			$this->settled    = true;
		}
	}

	public function renderScanWidget() {
		$this->pullStatus();

		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-scan"></span>
                <h3><?php _e( "DATEI-ÜBERPRÜFUNG", cp_defender()->domain ) ?>
                <?php
                 if($this->countAll > 0):
                ?>
                <span class="def-tag tag-error" tooltip="<?php printf(esc_attr__("Du hast %s verdächtige Dateien, die Deine Aufmerksamkeit erfordern.",cp_defender()->domain),$this->countAll) ?>"><?php echo $this->countAll ?></span>
                <?php endif; ?>
                </h3>

            </div>
            <div class="box-content">
				<?php
				$activeScan = $this->activeScan;
				$lastScan   = $this->lastScan;
				if ( ! is_object( $activeScan ) && ! is_object( $lastScan ) ) {
					echo $this->_renderNewScan();
				} elseif ( is_object( $activeScan ) && $activeScan->status != \CP_Defender\Module\Scan\Model\Scan::STATUS_ERROR ) {
					echo $this->_renderScanning( $activeScan );
				} elseif ( is_object( $activeScan ) && $activeScan->status == \CP_Defender\Module\Scan\Model\Scan::STATUS_ERROR ) {

				} else {
					echo $this->_renderResult( $lastScan );
				}
				?>
            </div>
        </div>
		<?php
	}

	public function renderScanStatusText() {
		$this->pullStatus();
		$activeScan = $this->activeScan;
		$lastScan   = $this->lastScan;
		if ( ! is_object( $activeScan ) && ! is_object( $lastScan ) ) {
			?>
            <form id="start-a-scan" method="post" class="scan-frm">
				<?php
				wp_nonce_field( 'startAScan' );
				?>
                <input type="hidden" name="action" value="startAScan"/>
                <button type="submit"
                        class="button button-small"><?php _e( "SCAN STARTEN", cp_defender()->domain ) ?></button>
            </form>
			<?php
		} elseif ( is_object( $activeScan ) && $activeScan->status != \CP_Defender\Module\Scan\Model\Scan::STATUS_ERROR ) {
			?>
            <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/loading.gif" width="18"
                 height="18"/> <?php _e( "Scanning…", cp_defender()->domain ) ?>
			<?php
		} elseif ( is_object( $activeScan ) && $activeScan->status == \CP_Defender\Module\Scan\Model\Scan::STATUS_ERROR ) {
			echo $this->activeScan->statusText;
		} else {
			?>
				<?php
				if ( $this->countAll == 0 ): ?>
					<span class="def-tag tag-success">
				<?php else:
					?>
					<span class="def-tag tag-error">
				<?php endif;
				echo $this->countAll;
				?>
				</span>
			<?php
		}
	}

	private function _renderResult( \CP_Defender\Module\Scan\Model\Scan $model ) {
		ob_start();
		?>
        <div class="line">
			<?php _e( "Scanne Deine Webseite nach Dateiänderungen, Schwachstellen und eingeschleustem Code und lasse Dich über verdächtige Aktivitäten informieren.", cp_defender()->domain ) ?>
        </div>
		<?php
		if ( $this->countAll == 0 ) {
			?>
            <div class="well well-green with-cap mline">
                <i class="def-icon icon-tick"></i>
				<?php _e( "Dein Code ist sauber. Alles in Ordnung.", cp_defender()->domain ) ?>
            </div>
			<?php
		} else {
			?>
            <div class="end"></div>
            <ul class="dev-list bold end">
                <li>
                    <div>
                        <span class="list-label"><?php _e( "WordPress Core", cp_defender()->domain ) ?></span>
                        <span class="list-detail">
                            <?php echo $model->getCount( 'core' ) == 0 ? ' <i class="def-icon icon-tick"></i>' : '<span class="def-tag tag-error">' . $model->getCount( 'core' ) . '</span>' ?>
                        </span>
                    </div>
                </li>
                <li>
                    <div>
                        <span class="list-label"><?php _e( "Plugins & Themes", cp_defender()->domain ) ?></span>
                        <span class="list-detail">
                            <?php if ( \CP_Defender\Behavior\Utils::instance()->getAPIKey() ): ?>
	                            <?php echo $model->getCount( 'vuln' ) == 0 ? ' <i class="def-icon icon-tick"></i>' : '<span class="def-tag tag-error">' . $model->getCount( 'vuln' ) . '</span>' ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </li>
                <li>
                    <div>
                        <span class="list-label"><?php _e( "Verdächtiger Code", cp_defender()->domain ) ?></span>
                        <span class="list-detail">
                            <?php echo $model->getCount( 'content' ) == 0 ? ' <i class="def-icon icon-tick"></i>' : '<span class="def-tag tag-error">' . $model->getCount( 'content' ) . '</span>' ?>
                        </span>
                    </div>
                </li>
            </ul>
			<?php
		}
		?>
        <div class="row">
            <div class="col-third tl">
                <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-scan' ) ?>"
                   class="button button-small button-secondary"><?php _e( "BERICHT ANSEHEN", cp_defender()->domain ) ?></a>
            </div>
            <div class="col-two-third tr">
                <p class="status-text">
					<?php
					if ( !empty( Settings::instance()->notification ) ) {
						switch ( Settings::instance()->frequency ) {
							case '1':
								_e( "Automatische Scans werden täglich ausgeführt", cp_defender()->domain );
								break;
							case '7':
								_e( "Automatische Scans werden wöchentlich ausgeführt", cp_defender()->domain );
								break;
							case '30':
								_e( "Automatische Scans werden monatlich ausgeführt", cp_defender()->domain );
								break;
						}
					}
					?>
                </p>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	private function _renderNewScan() {
		ob_start();
		?>
        <div class="line">
			<?php _e( "Scanne Deine Webseite nach Dateiänderungen, Schwachstellen und eingeschleustem Code und lasse Dich über verdächtige Aktivitäten informieren.", cp_defender()->domain ) ?>
        </div>
        <form id="start-a-scan" method="post" class="scan-frm">
			<?php
			wp_nonce_field( 'startAScan' );
			?>
            <input type="hidden" name="action" value="startAScan"/>
            <button type="submit" class="button button-small"><?php _e( "SCAN STARTEN", cp_defender()->domain ) ?></button>
        </form>
		<?php
		return ob_get_clean();
	}

	private function _renderScanning( $model ) {
		ob_start();
		$percent = Scan_Api::getScanProgress();
		?>
        <div class="wdf-scanning"></div>
        <div class="line">
			<?php _e( "Defender scannt Deine Dateien nach bösartigem Code. Dies dauert je nach Größe Deiner Webseite einige Minuten.", cp_defender()->domain ) ?>
        </div>
        <div class="well mline">
            <div class="scan-progress">
                <div class="scan-progress-text">
                    <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/loading.gif" width="18"
                         height="18"/>
                    <span><?php echo $percent ?>%</span>
                </div>
                <div class="scan-progress-bar">
                    <span style="width: <?php echo $percent ?>%"></span>
                </div>
            </div>
        </div>
        <p class="tc sub status-text scan-status"><?php echo $model->statusText ?></p>
        <form method="post" id="process-scan" class="scan-frm">
            <input type="hidden" name="action" value="processScan"/>
			<?php
			wp_nonce_field( 'processScan' );
			?>
        </form>
		<?php
		return ob_get_clean();
	}
}