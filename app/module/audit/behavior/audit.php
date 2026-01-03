<?php


namespace CP_Defender\Module\Audit\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Behavior\Utils;
use CP_Defender\Module\Audit\Model\Settings;

class Audit extends Behavior {
	public function renderAuditWidget() {
		$this->_renderAuditSample();
	}

	private function _renderAuditSample() {
		?>
        <div class="dev-box">
            <div class="box-title">
                <span class="span-icon icon-audit"></span>
                <h3><?php _e( "AUDIT-LOGGING", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content auditing">
				<?php if ( Settings::instance()->enabled ): ?>
                    <form method="post" class="audit-frm audit-widget">
                        <input type="hidden" name="action" value="dashboardSummary"/>
						<?php wp_nonce_field( 'dashboardSummary' ) ?>
                    </form>
                    <div class="">
						<?php _e( "Bitte habe etwas Geduld, PS Security wird die Audit-Informationen in Kürze aktualisieren...", cp_defender()->domain ) ?>
                    </div>
                    <div class="wd-overlay">
                        <i class="wdv-icon wdv-icon-fw wdv-icon-refresh spin"></i>
                    </div>
				<?php else: ?>
                    <div class="line">
						<?php _e( "Verfolge und protokolliere Ereignisse, wenn Änderungen an deiner Webseite vorgenommen werden, und erhalte so volle Transparenz darüber, was hinter den Kulissen passiert.", cp_defender()->domain ) ?>
                    </div>
                    <form method="post" class="audit-frm active-audit">
                        <input type="hidden" name="action" value="activeAudit"/>
						<?php wp_nonce_field( 'activeAudit' ) ?>
                        <button type="submit" class="button button-small"><?php _e( "Aktivieren", cp_defender()->domain ) ?></button>
                    </form>
				<?php endif; ?>
            </div>
        </div>
		<?php
	}
}