<?php


namespace CP_Defender\Module\IP_Lockout\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Behavior\Utils;
use CP_Defender\Module\IP_Lockout\Model\Settings;

class Widget extends Behavior {
	public function renderLockoutWidget() {
		?>
        <div class="dev-box" id="lockoutSummary">
			<?php if ( Settings::instance()->detect_404 || Settings::instance()->login_protection ): ?>
                <div class="wd-overlay">
                    <i class="wdv-icon wdv-icon-fw wdv-icon-refresh spin"></i>
                </div>
                <input type="hidden" id="summaryNonce" value="<?php echo wp_create_nonce( 'lockoutSummaryData' ) ?>"/>
			<?php endif; ?>
            <div class="box-title">
                <span class="span-icon icon-lockout"></span>
                <h3><?php _e( "IP-SPERREN", cp_defender()->domain ) ?></h3>
            </div>
            <div class="box-content advanced-tools">
                <div class="line">
					<?php _e( "Schütze deinen Anmeldebereich, indem du automatisch verdächtiges Verhalten sperrst.", cp_defender()->domain ) ?>
                </div>
				<?php if ( ! Settings::instance()->detect_404 && ! Settings::instance()->login_protection ): ?>
                    <form method="post" id="settings-frm" class="ip-frm">
						<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
                        <input type="hidden" name="action" value="saveLockoutSettings"/>
                        <input type="hidden" name="login_protection" value="1"/>
                        <input type="hidden" name="detect_404" value="1"/>
                        <button type="submit" class="button button-primary button-small">
							<?php esc_html_e( "Aktivieren", cp_defender()->domain ) ?>
                        </button>
                    </form>
				<?php else: ?>
                    <div class="end"></div>
                    <ul class="dev-list bold end">
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "Letzte Sperre", cp_defender()->domain ) ?></span>
                                <span class="list-detail lastLockout">.</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "Login-Sperren in dieser Woche", cp_defender()->domain ) ?></span>
                                <span class="list-detail loginLockoutThisWeek">.</span>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span class="list-label"><?php _e( "404-Sperren in dieser Woche", cp_defender()->domain ) ?></span>
                                <span class="list-detail lockout404ThisWeek">.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-third tl">
                            <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ?>"
                               class="button button-small button-secondary">
								<?php _e( "Protokolle anzeigen", cp_defender()->domain ) ?></a>
                        </div>
                        <div class="col-two-third tr">
                            <p class="status-text"><?php if ( Settings::instance()->ip_lockout_notification && Settings::instance()->login_lockout_notification ) {
									echo _e( "Sperrbenachrichtigungen sind aktiviert", cp_defender()->domain );
								} else {
									echo _e( "Sperrbenachrichtigungen sind deaktiviert", cp_defender()->domain );
								}
								?></p>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
        </div>
		<?php
	}
}