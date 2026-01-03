<?php

namespace CP_Defender\Module\Hardener\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Module\Hardener\Model\Settings;
use CP_Defender\Behavior\Utils;

class Widget extends Behavior {
	public function renderHardenerWidget() {
		$issues = Settings::instance()->getIssues();
		$issues = array_slice( $issues, 0, 3 );
		?>
        <div class="dev-box hardener-widget">
            <div class="box-title">
                <span class="span-icon hardener-icon" aria-hidden="true"></span>
                <h3><?php _e( "Sicherheitsoptimierungen", cp_defender()->domain ) ?>
					<?php
                    $hardener_issues = count( Settings::instance()->issues );
                    if ( $hardener_issues ): ?>
                        <span class="def-tag tag-yellow"
                        tooltip="<?php esc_attr_e( sprintf( __('Du hast %d Sicherheitseinstellungen, die Aufmerksamkeit erfordern.', cp_defender()->domain ), $hardener_issues ) ); ?>">
                        <?php
                        echo $hardener_issues ?>
                    </span>
					<?php endif; ?>
                </h3>
            </div>
            <div class="box-content">
				<?php $count = count( $issues ); ?>
                <div class="line <?php echo $count ? 'end' : null ?>">
					<?php _e( "PS Security überprüft Sicherheitseinstellungen, die du vornehmen kannst, um die Verteidigung deiner Website gegen Hacker und Bots zu verbessern.", cp_defender()->domain ) ?>
                </div>
				<?php if ( $count ): ?>
                    <ul class="dev-list end">
						<?php
						foreach ( $issues as $issue ):
							?>
                            <li>
                                <div>
                                    <a target="_blank"
                                       href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-hardener' ) . '#' . $issue::$slug; ?>">
                                        <span class="list-label"><i
                                                    class="def-icon icon-h-warning"></i><?php echo $issue->getTitle(); ?></span>
                                    </a>
                                </div>
                            </li>
						<?php endforeach;
						?>
                    </ul>
				<?php else: ?>
                    <div class="well well-green with-cap mline">
                        <i class="def-icon icon-tick"></i>
						<?php _e( "Du hast alle verfügbaren Sicherheitseinstellungen vorgenommen. Gute Arbeit!", cp_defender()->domain ) ?>
                    </div>
				<?php endif; ?>
                <div class="row">
                    <div class="col-third tl">
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-hardener' ) ?>"
                           class="button button-small button-secondary"><?php _e( "ALLE ANZEIGEN", cp_defender()->domain ) ?></a>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	private function _renderNew() {

	}

	private function _render() {

	}
}