<div class="dev-box">
    <div class="box-title">
        <h3><?php esc_html_e( "BENACHRICHTIGUNGEN", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "E-Mail-Benachrichtigungen senden", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wähle aus, über welche Aussperrungsbenachrichtigungen Du informiert werden möchtest. Diese werden sofort gesendet.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <span
                            tooltip="<?php echo esc_attr( __( "Anmeldeschutz aktivieren", cp_defender()->domain ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="login_lockout_notification" value="0"/>
                            <input type="checkbox"
                                   name="login_lockout_notification" <?php checked( 1, $settings->login_lockout_notification ) ?>
                                   value="1" class="toggle-checkbox"
                                   id="toggle_login_protection"/>
                            <label class="toggle-label" for="toggle_login_protection"></label>
                        </span>
                    <label><?php esc_html_e( "Anmeldeschutz Aussperrung", cp_defender()->domain ) ?></label>
                    <span class="sub inpos">
                        <?php esc_html_e( "Wenn ein Benutzer oder eine IP ausgesperrt wird, weil versucht wurde, auf Ihren Anmeldebereich zuzugreifen.", cp_defender()->domain ) ?>
                    </span>
                    <div class="clear mline"></div>
                    <span
                            tooltip="<?php echo esc_attr( __( "404-Erkennung aktivieren", cp_defender()->domain ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="ip_lockout_notification" value="0"/>
                            <input type="checkbox" name="ip_lockout_notification"
                                   value="1" <?php checked( 1, $settings->ip_lockout_notification ) ?>
                                   class="toggle-checkbox" id="toggle_404_detection"/>
                            <label class="toggle-label" for="toggle_404_detection"></label>
                        </span>
                    <label>
						<?php esc_html_e( "404 Erkennungssperre", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub inpos"><?php esc_html_e( "Wenn ein Benutzer oder eine IP ausgesperrt wird, weil wiederholt auf nicht vorhandene Dateien zugegriffen wurde.", cp_defender()->domain ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "-Mail-Empfänger", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php esc_html_e( "Wähle aus, welche Nutzer Deiner Webseite die Scan-Berichtsergebnisse per E-Mail erhalten sollen.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<?php
					$email_search->renderInput() ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Wiederholte Aussperrungen", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wenn Du zu viele E-Mails von IPs erhältst, die wiederholt ausgesperrt werden, kannst Du sie für einen bestimmten Zeitraum ausschalten.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <span class="toggle float-l">
                            <input type="hidden" name="cooldown_enabled" value="0"/>
                            <input type="checkbox"
                                   name="cooldown_enabled" <?php checked( 1, $settings->cooldown_enabled ) ?>
                                   value="1" class="toggle-checkbox"
                                   id="cooldown_enabled"/>
                            <label class="toggle-label" for="cooldown_enabled"></label>
                        </span>
                    <label><?php _e( "E-Mail-Benachrichtigungen für wiederholte Aussperrungen begrenzen", cp_defender()->domain ) ?></label>
                    <div class="well well-white schedule-box">
                        <label><strong><?php _e( "Schwelle", cp_defender()->domain ) ?></strong>
                            - <?php _e( "Die Anzahl der Aussperrungen, bevor wir die E-Mails ausschalten", cp_defender()->domain ) ?>
                        </label>
                        <select name="cooldown_number_lockout">
                            <option <?php selected( '1', $settings->cooldown_number_lockout ) ?> value="1">1
                            </option>
                            <option <?php selected( '3', $settings->cooldown_number_lockout ) ?> value="3">3
                            </option>
                            <option <?php selected( '5', $settings->cooldown_number_lockout ) ?> value="5">5
                            </option>
                            <option <?php selected( '10', $settings->cooldown_number_lockout ) ?> value="10">10
                            </option>
                        </select>
                        <label><strong><?php _e( "Abkühlungszeitraum", cp_defender()->domain ) ?></strong>
                            - <?php _e( "Wie lange sollen wir sie ausschalten?", cp_defender()->domain ) ?>
                        </label>
                        <select name="cooldown_period" class="mline">
                            <option <?php selected( '1', $settings->cooldown_period ) ?>
                                    value="1"><?php _e( "1 Stunde", cp_defender()->domain ) ?></option>
                            <option <?php selected( '2', $settings->cooldown_period ) ?>
                                    value="2"><?php _e( "2 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '6', $settings->cooldown_period ) ?>
                                    value="6"><?php _e( "6 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '12', $settings->cooldown_period ) ?>
                                    value="12"><?php _e( "12 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '24', $settings->cooldown_period ) ?>
                                    value="24"><?php _e( "24 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '36', $settings->cooldown_period ) ?>
                                    value="36"><?php _e( "36 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '48', $settings->cooldown_period ) ?>
                                    value="48"><?php _e( "48 Stunden", cp_defender()->domain ) ?></option>
                            <option <?php selected( '168', $settings->cooldown_period ) ?>
                                    value="168"><?php _e( "7 Tage", cp_defender()->domain ) ?></option>
                            <option <?php selected( '720', $settings->cooldown_period ) ?>
                                    value="720"><?php _e( "30 Tage", cp_defender()->domain ) ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clear line"></div>
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <button type="submit" class="button button-primary float-r">
				<?php esc_html_e( "EINSTELLUNGEN AKTUALISIEREN", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </form>
    </div>
</div>