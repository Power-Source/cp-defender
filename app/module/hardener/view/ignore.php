<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "IGNORIERT", cp_defender()->domain ) ?>
			<?php if ( $controller->getCount( 'ignore' ) ): ?>
                <span class="def-tag tag-generic count-ignored">
                <?php echo $controller->getCount( 'ignore' ) ?>
            </span>
			<?php endif; ?>
        </h3>
    </div>
    <div class="box-content">
        <div class="box-content">
			<?php if ( count( \CP_Defender\Module\Hardener\Model\Settings::instance()->ignore ) > 0 ): ?>
                <div class="line">
					<?php _e( "Du hast dich entschieden, diese Korrekturen zu ignorieren. Du kannst sie jederzeit wiederherstellen und ausführen.", cp_defender()->domain ) ?>
                </div>
                <div class="rules ignored">
					<?php foreach ( \CP_Defender\Module\Hardener\Model\Settings::instance()->getIgnore() as $rule ): ?>
						<?php
						$rule->showRestoreForm();
						?>
					<?php endforeach; ?>
                </div>
			<?php else: ?>
                <div class="well well-blue with-cap">
                    <i class="def-icon icon-warning" aria-hidden="true"></i>
					<?php _e( "Du hast noch keine Probleme ignoriert. Du kannst alle Sicherheitseinstellungen, vor denen du nicht gewarnt werden möchtest, ignorieren, indem du im Problembeschreibung auf 'Ignorieren' klickst.", cp_defender()->domain ) ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>