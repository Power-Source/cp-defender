<div class="rule closed" id="change_admin">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Standard-Administratorkonto ändern", 'cpsec' ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", 'cpsec' ) ?></h3>
        <div class="line end">
			<?php _e( "Wenn du den Standard-Admin-Benutzernamen verwendest, gibst du Hackern ein wichtiges Puzzlestück preis, das sie benötigen, um deine Website zu übernehmen. Ein Standard-Admin-Benutzerkonto ist eine schlechte Praxis, aber leicht zu beheben. Stelle sicher, dass du vor der Auswahl eines neuen Benutzernamens ein Backup deiner Datenbank erstellst.", 'cpsec' ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", 'cpsec' ) ?>
        </h3>
        <div class="well has-input">
			<?php if ( $controller->check() ): ?>
				<?php _e( "Du hast keinen Benutzer mit dem Benutzernamen admin.", 'cpsec' ) ?>
			<?php else: ?>
                <div class="line">
                    <p><?php _e( "Bitte ändere den Benutzernamen von admin zu etwas Einzigartigem.", 'cpsec' ) ?></p>
                </div>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="text" placeholder="<?php esc_attr_e( "Neuen Benutzernamen eingeben", 'cpsec' ) ?>"
                           name="username" class="block" />
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button float-r"
                            type="submit"><?php _e( "Aktualisieren", 'cpsec' ) ?></button>
                </form>
				<?php $controller->showIgnoreForm() ?>
                <div class="clear"></div>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>