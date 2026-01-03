<div class="rule closed" id="change_admin">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Standard-Administratorkonto ändern", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "Wenn du den Standard-Admin-Benutzernamen verwendest, gibst du Hackern ein wichtiges Puzzlestück preis, das sie benötigen, um deine Website zu übernehmen. Ein Standard-Admin-Benutzerkonto ist eine schlechte Praxis, aber leicht zu beheben. Stelle sicher, dass du vor der Auswahl eines neuen Benutzernamens ein Backup deiner Datenbank erstellst.", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well has-input">
			<?php if ( $controller->check() ): ?>
				<?php _e( "Du hast keinen Benutzer mit dem Benutzernamen admin.", cp_defender()->domain ) ?>
			<?php else: ?>
                <div class="line">
                    <p><?php _e( "Bitte ändere den Benutzernamen von admin zu etwas Einzigartigem.", cp_defender()->domain ) ?></p>
                </div>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="text" placeholder="<?php esc_attr_e( "Neuen Benutzernamen eingeben", cp_defender()->domain ) ?>"
                           name="username" class="block" />
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button float-r"
                            type="submit"><?php _e( "Aktualisieren", cp_defender()->domain ) ?></button>
                </form>
				<?php $controller->showIgnoreForm() ?>
                <div class="clear"></div>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>