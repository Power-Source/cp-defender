<?php

namespace CP_Defender\Module\Anti_Spam\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Module\Anti_Spam\Model\Blog_Log;

class Widget extends Behavior {
	
	/**
	 * Rendert das Anti-Spam Widget für das Dashboard
	 */
	public function renderAntiSpamWidget() {
		// Nur für Multisite anzeigen
		if ( ! is_multisite() ) {
			return;
		}
		
		$counts = Blog_Log::get_counts();
		$suspicious = $counts['suspicious'];
		?>
        <div class="dev-box antispam-widget">
            <div class="box-title">
                <span class="span-icon" style="background: #4CAF50;" aria-hidden="true">🛡️</span>
                <h3><?php _e( "Anti-Spam", cp_defender()->domain ) ?>
					<?php if ( $suspicious > 0 ): ?>
                        <span class="def-tag tag-yellow"
                              tooltip="<?php esc_attr_e( sprintf( __('%d verdächtige Blog-Registrierung(en) benötigen Aufmerksamkeit.', cp_defender()->domain ), $suspicious ) ); ?>">
                            <?php echo $suspicious; ?>
                        </span>
					<?php endif; ?>
                </h3>
            </div>
            <div class="box-content">
                <div class="line <?php echo $suspicious ? 'end' : ''; ?>">
					<?php _e( "Das Anti-Spam-Modul schützt deine Multisite-Installation vor Spam-Blog-Registrierungen durch Pattern-Matching, IP-Reputation-Tracking und Rate-Limiting.", cp_defender()->domain ); ?>
                </div>
				
				<?php if ( $suspicious > 0 ): ?>
                    <ul class="dev-list end">
                        <li>
                            <div>
                                <a href="<?php echo network_admin_url( 'admin.php?page=cp-defender-antispam-moderation' ); ?>">
                                    <span class="list-label">
                                        <i class="def-icon icon-h-warning"></i>
                                        <?php echo sprintf( __( '%d verdächtige Blog-Registrierung(en)', cp_defender()->domain ), $suspicious ); ?>
                                    </span>
                                </a>
                            </div>
                        </li>
						<?php if ( $counts['spam'] > 0 ): ?>
                        <li>
                            <div>
                                <span class="list-label">
                                    <?php echo sprintf( __( '%d als Spam markiert', cp_defender()->domain ), $counts['spam'] ); ?>
                                </span>
                            </div>
                        </li>
						<?php endif; ?>
                    </ul>
				<?php else: ?>
                    <div class="well well-green with-cap mline">
                        <i class="def-icon icon-tick"></i>
						<?php _e( "Keine verdächtigen Blog-Registrierungen gefunden. Gute Arbeit!", cp_defender()->domain ); ?>
                    </div>
				<?php endif; ?>
				
                <div class="row" style="margin-top: 15px;">
                    <div class="col-third tl">
                        <a href="<?php echo network_admin_url( 'admin.php?page=cp-defender-antispam-settings' ); ?>"
                           class="button button-small button-secondary">
							<?php _e( "EINSTELLUNGEN", cp_defender()->domain ); ?>
                        </a>
                    </div>
                    <div class="col-third tl">
                        <a href="<?php echo network_admin_url( 'admin.php?page=cp-defender-antispam-stats' ); ?>"
                           class="button button-small button-secondary">
							<?php _e( "STATISTIKEN", cp_defender()->domain ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}
