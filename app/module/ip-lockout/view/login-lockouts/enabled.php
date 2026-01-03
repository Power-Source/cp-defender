<div class="dev-box">
    <form method="post" id="settings-frm" class="ip-frm">
        <div class="box-title">
            <h3><?php _e( "Anmeldeschutz", cp_defender()->domain ) ?></h3>
            <div class="side float-r">
                <div>
                    <span tooltip="<?php esc_attr_e( "Anmeldeschutz deaktivieren", cp_defender()->domain ) ?>"
                          class="toggle">
                        <input type="hidden" name="login_protection" value="0"/>
                        <input type="checkbox" checked="checked" name="login_protection" value="1"
                               class="toggle-checkbox" id="toggle_login_protect"/>
                        <label class="toggle-label" for="toggle_login_protect"></label>
                    </span>
                </div>
            </div>
        </div>
        <div class="box-content">
			<?php if ( ( $count = ( \CP_Defender\Module\IP_Lockout\Component\Login_Protection_Api::getLoginLockouts( strtotime( '-24 hours', current_time( 'timestamp' ) ) ) ) ) > 0 ): ?>
                <div class="well well-yellow">
					<?php echo sprintf( __( "In den letzten 24 Stunden gab es %d Aussperrungen. <a href=\"%s\"><strong>Protokoll anzeigen</strong></a>.", cp_defender()->domain ), $count, \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ) ?>
                </div>
			<?php else: ?>
                <div class="well well-blue">
					<?php esc_html_e( "Der Anmeldeschutz ist aktiviert. Es sind noch keine Aussperrungen protokolliert.", cp_defender()->domain ) ?>
                </div>
			<?php endif; ?>
            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_login_attempt">
						<?php esc_html_e( "Aussperrungsschwelle", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Gib an, wie viele fehlgeschlagene Anmeldeversuche innerhalb eines bestimmten Zeitraums eine Aussperrung auslösen.", cp_defender()->domain ) ?>
						</span>
                </div>
                <div class="column">
                    <input size="8" value="<?php echo $settings->login_protection_login_attempt ?>" type="text"
                           class="inline" id="login_protection_login_attempt"
                           name="login_protection_login_attempt"/>
                    <span><?php esc_html_e( "fehlgeschlagene Anmeldungen innerhalb von", cp_defender()->domain ) ?></span>&nbsp;
                    <input size="8" value="<?php echo $settings->login_protection_lockout_timeframe ?>"
                           id="login_lockout_timeframe"
                           name="login_protection_lockout_timeframe" type="text" class="inline">
                    <span><?php esc_html_e( "Sekunden", cp_defender()->domain ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_lockout_timeframe">
						<?php esc_html_e( "Aussperrungszeit", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Wähle, wie lange der ausgesperrte Benutzer gesperrt bleiben soll.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input value="<?php echo $settings->login_protection_lockout_duration ?>" size="8"
                           name="login_protection_lockout_duration"
                           id="login_protection_lockout_duration" type="text" class="inline"/>
                    <span class=""><?php esc_html_e( "Sekunden", cp_defender()->domain ) ?></span>
                    <div class="clearfix"></div>
                    <input type="hidden" name="login_protection_lockout_ban" value="0"/>
                    <input
                            id="login_protection_lockout_ban" <?php checked( 1, $settings->login_protection_lockout_ban ) ?>
                            type="checkbox"
                            name="login_protection_lockout_ban" value="1">
                    <label for="login_protection_lockout_ban"
                           class="inline form-help is-marginless"><?php esc_html_e( 'Permanente Aussperrung bei Anmeldeschutz.', cp_defender()->domain ) ?></label>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_lockout_message">
						<?php esc_html_e( "Aussperrungsnachricht", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Passe die Nachricht an, die ausgesperrte Benutzer sehen werden.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
						<textarea name="login_protection_lockout_message"
                                  id="login_protection_lockout_message"><?php echo $settings->login_protection_lockout_message ?></textarea>
                    <span class="form-help">
                                        <?php echo sprintf( __( "Diese Nachricht wird während der Aussperrungszeit auf Ihrer Website angezeigt. Siehe eine schnelle Vorschau <a href=\"%s\">hier</a>.", cp_defender()->domain ), add_query_arg( array(
	                                        'def-lockout-demo' => 1,
	                                        'type'             => 'login'
                                        ), network_site_url() ) ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="username_blacklist">
						<?php esc_html_e( "Automatisch Benutzernamen sperren", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wir empfehlen, den Standardbenutzernamen \"admin\" zu vermeiden. Defender wird automatisch alle Benutzer aussperren, die versuchen, sich mit den hier aufgeführten Benutzernamen anzumelden.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <textarea placeholder="<?php esc_attr_e( "Gib die Benutzernamen ein, einen pro Zeile.", cp_defender()->domain ) ?>"
                              id="username_blacklist" name="username_blacklist"
                              rows="8"><?php echo $settings->username_blacklist ?></textarea>
                    <span class="sub">
						<?php
						$host = parse_url( get_site_url(), PHP_URL_HOST );
						$host = str_replace( 'www.', '', $host );
						$host = explode( '.', $host );
						if ( is_array( $host ) ) {
							$host = array_shift( $host );
						} else {
							$host = null;
						}
						printf( __( "Wir empfehlen, die Benutzernamen <strong>admin</strong>, <strong>administrator</strong> und Ihren Hostnamen <strong>%s</strong> hinzuzufügen, da diese häufig von Bots verwendet werden, um sich anzumelden. Ein Benutzername pro Zeile", cp_defender()->domain ), $host ) ?>
                    </span>
                </div>
            </div>
            <div class="clear line"></div>
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <button type="submit" class="button button-primary float-r">
				<?php esc_html_e( "EINSTELLUNGEN AKTUALISIEREN", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </div>
    </form>
</div>