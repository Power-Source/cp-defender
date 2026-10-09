<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Probleme", 'cpsec' ) ?>
			<?php if ( $controller->getCount( 'issues' ) ): ?>
            <span class="def-tag tag-yellow count-issues"><?php echo $controller->getCount( 'issues' ) ?></span>
			<?php endif; ?>
        </h3>
    </div>
    <div class="box-content">
        <div class="box-content">
            <div class="line">
				<?php _e( "Es gibt eine Reihe von Sicherheitseinstellungen, die du an deiner Webseite vornehmen kannst, um sie gegen schädliche Hacker und Bots zu stärken, die versuchen einzubrechen. Wir empfehlen, so viele Einstellungen wie möglich umzusetzen.", 'cpsec' ) ?>
            </div>
            <div class="rules">
				<?php
				$setting = \CP_Defender\Module\Hardener\Model\Settings::instance();
				$issues  = $setting->getIssues();
				if ( count( $issues ) == 0 ) {
					?>
                    <div class="well well-green with-cap">
                        <i class="def-icon icon-tick" aria-hidden="true"></i>
						<?php _e( "Du hast alle verfügbaren Sicherheitseinstellungen umgesetzt. Gute Arbeit!", 'cpsec' ) ?>
                    </div>
					<?php
				} else {
					foreach ( $setting->getIssues() as $rule ): ?>
						<?php
						$rule->getDescription();
						?>
					<?php endforeach;
				}
				?>
            </div>
        </div>
    </div>
</div>