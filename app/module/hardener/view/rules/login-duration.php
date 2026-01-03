<div class="rule closed" id="login-duration">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php echo $controller->getTitle() ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "Standardmäßig bleiben Benutzer, die die Option 'Angemeldet bleiben' auswählen, 14 Tage lang angemeldet", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
            <?php
                $setting = \CP_Defender\Module\Hardener\Model\Settings::instance();

                if ( $controller->check() ):
                    ?>
                    <p class="line"><?php esc_attr_e( sprintf( __('Die Anmeldedauer ist festgelegt. Die aktuelle Dauer beträgt %d Tage', cp_defender()->domain ), $controller->getService()->getDuration() ) ); ?></p>
                    <form method="post" class="hardener-frm rule-process">
                        <?php $controller->createNonceField(); ?>
                        <input type="hidden" name="action" value="processRevert"/>
                        <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                        <button class="button button-small button-grey" type="submit"><?php _e( "Zurücksetzen", cp_defender()->domain ) ?></button>
                    </form>
                    <?php
                else:
                    ?>
                        <div class="line">
                            <p><?php _e( "Bitte ändere die Anzahl der Tage, die ein Benutzer angemeldet bleiben kann", cp_defender()->domain ) ?></p>
                        </div>
                        <form method="post" class="hardener-frm rule-process">
                            <?php $controller->createNonceField(); ?>
                            <input type="hidden" name="action" value="processHardener"/>
                            <input type="text" placeholder="<?php esc_attr_e( "Gib die Anzahl der Tage ein", cp_defender()->domain ) ?>"
                                name="duration" class="block defender-login-duration" />
                            <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                            <button class="button float-r"
                                    type="submit"><?php _e( "Aktualisieren", cp_defender()->domain ) ?></button>
                        </form>
                        <?php $controller->showIgnoreForm() ?>
                        <div class="clear"></div>
                    <?php
                endif;
            ?>
        </div>
    </div>
</div>