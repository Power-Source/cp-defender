<div class="rule closed" id="security_key">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Alte Sicherheitsschlüssel aktualisieren", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
            <p><?php _e( "Wir empfehlen, deine Sicherheitsschlüssel alle 60 Tage zu ändern", cp_defender()->domain ) ?></p>
            <div class="security-reminder">
				<?php esc_html_e( "Erinnere mich daran, meine Sicherheitsschlüssel alle", cp_defender()->domain ) ?>
                <form method="post" class="hardener-frm" id="reminder-date">
                    <select name="remind_date">
                        <option
                                value="30 days" <?php selected( '30 days', $interval ) ?>><?php esc_html_e( '30 Tage', cp_defender()->domain ) ?></option>
                        <option
                                value="60 days" <?php selected( '60 days', $interval ) ?>><?php esc_html_e( '60 Tage', cp_defender()->domain ) ?></option>
                        <option
                                value="90 days" <?php selected( '90 days', $interval ) ?>><?php esc_html_e( '90 Tage', cp_defender()->domain ) ?></option>
                        <option
                                value="6 months" <?php selected( '6 months', $interval ) ?>><?php esc_html_e( '6 Monate', cp_defender()->domain ) ?></option>
                        <option
                                value="1 year" <?php selected( '1 year', $interval ) ?>><?php esc_html_e( '1 Jahr', cp_defender()->domain ) ?></option>
                    </select>
                    <input type="hidden" name="action" value="updateSecurityReminder"/>
                    <button type="submit" class="button">
						<?php _e( "Aktualisieren", cp_defender()->domain ) ?></button>
                </form>
            </div>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
				<?php printf( esc_html__( "Deine Sicherheitsschlüssel sind %d Tage alt. Du bist vorerst in Ordnung.", cp_defender()->domain ), $daysAgo ) ?>
			<?php else: ?>
                <div class="line">
                    <p><?php _e( "Wir können deine Sicherheitsschlüssel sofort für dich neu generieren und sie sind dann weitere <span class=\"expiry-days\">60 Tage</span> gültig. Beachte, dass dadurch alle Benutzer von deiner Seite abgemeldet werden.", cp_defender()->domain ) ?></p>
                </div>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button float-r"
                            type="submit"><?php _e( "Sicherheitsschlüssel neu generieren", cp_defender()->domain ) ?></button>
                </form>
				<?php $controller->showIgnoreForm() ?>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>