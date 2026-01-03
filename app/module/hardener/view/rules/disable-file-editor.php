<div class="rule closed" id="disable_file_editor">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Deaktiviere den Dateieditor.", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "WordPress verfügt über einen integrierten Dateieditor. Das bedeutet, dass jeder mit Zugang zu deinen Anmeldedaten deine Plugin- und Theme-Dateien bearbeiten kann. Wir empfehlen, den Editor zu deaktivieren.", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
				<p class="line"><?php _e( "Der Dateieditor ist deaktiviert.", cp_defender()->domain ) ?></p>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processRevert"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button button-small button-grey"
                            type="submit"><?php _e( "Zurücksetzen", cp_defender()->domain ) ?></button>
                </form>
			<?php else: ?>
                <div class="line">
                    <p><?php _e( "Wir werden den Zugriff auf den Dateieditor für dich deaktivieren. Du kannst ihn jederzeit wieder aktivieren.", cp_defender()->domain ) ?></p>
                </div>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button float-r"
                            type="submit"><?php _e( "Dateieditor deaktivieren", cp_defender()->domain ) ?></button>
                </form>
				<?php $controller->showIgnoreForm() ?>
                <div class="clear"></div>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>