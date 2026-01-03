<div class="rule closed" id="db_prefix">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Standard-Datenbankpräfix ändern", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "Wenn du WordPress zum ersten Mal auf einer neuen Datenbank installierst, beginnen die Standardeinstellungen mit wp_ als Präfix für alles, was in den Tabellen gespeichert wird. Dies erleichtert es Hackern, SQL-Injektionsangriffe durchzuführen, wenn sie eine Code-Schwachstelle finden. Es ist eine gute Praxis, ein einzigartiges Präfix zu verwenden, um dich davor zu schützen. Bitte sichere deine Datenbank, bevor du das Präfix änderst.", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well has-input">
			<?php if ( $controller->check() ): ?>
				<?php
				global $wpdb;
				printf( __( "Dein Präfix ist <strong>%s</strong> und ist einzigartig.", cp_defender()->domain ), $wpdb->prefix ) ?>
			<?php else: ?>
                <div class="line">
                    <p>
						<?php esc_html_e( "Wir empfehlen, ein anderes Präfix zu verwenden, um deine Datenbank zu schützen. Stelle sicher, dass du deine Datenbank sicherst, bevor du das Präfix änderst.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="text"
                           placeholder="<?php esc_attr_e( "Neues Datenbankpräfix eingeben", cp_defender()->domain ) ?>"
                           name="dbprefix" class="block">
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