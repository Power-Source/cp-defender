<div class="rule closed" id="disable-file-editor">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Fehlerberichterstattung ausblenden", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "Zusätzlich zum Ausblenden von Fehlerprotokollen verwenden Entwickler häufig die integrierte Front-End-PHP- und Skriptfehler-Debugging-Funktion, die Codefehler im Front-End anzeigt. Dies bietet Hackern eine weitere Möglichkeit, Sicherheitslücken auf deiner Website zu finden.", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
                <p class=""><?php _e( "Alle PHP-Fehler sind ausgeblendet.", cp_defender()->domain ) ?></p>
			<?php else: ?>
				<?php
				//if WP debug == true, we will display a form to turn it off
				if ( WP_DEBUG == true && ( ! defined( 'WP_DEBUG_DISPLAY' ) || WP_DEBUG_DISPLAY != false ) ): ?>
                    <div class="line">
                        <p><?php _e( "Wir werden den notwendigen Code hinzufügen, um zu verhindern, dass diese Fehler angezeigt werden.", cp_defender()->domain ) ?></p>
                    </div>
                    <form method="post" class="hardener-frm rule-process">
						<?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processHardener"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button float-r"
                                type="submit"><?php _e( "Fehler-Debugging deaktivieren", cp_defender()->domain ) ?></button>
                    </form>
					<?php $controller->showIgnoreForm() ?>
					<?php
				//php debug is turn off, however the error still dsplay, need to show user about this
				else: ?>
                    <p class="line">
						<?php _e( "Wir haben versucht, die Einstellung display_errors zu deaktivieren, um die Anzeige von Codefehlern zu verhindern. Diese Einstellung wird jedoch von Deiner Serverkonfiguration überschrieben. Bitte kontaktiere deinen Hosting-Anbieter und bitte ihn, display_errors auf false zu ​​setzen.", cp_defender()->domain ) ?>
                    </p>
					<?php $controller->showIgnoreForm() ?>
				<?php endif; ?>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>