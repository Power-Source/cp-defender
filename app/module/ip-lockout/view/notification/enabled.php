<div class="dev-box">
    <div class="box-title">
        <h3><?php esc_html_e( "BENACHRICHTIGUNGEN", 'cpsec' ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "E-Mail-Benachrichtigungen senden", 'cpsec' ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wähle aus, über welche Aussperrungsbenachrichtigungen Du informiert werden möchtest. Diese werden sofort gesendet.", 'cpsec' ) ?>
					</span>
                </div>
                <div class="column">
                    <span
                            tooltip="<?php echo esc_attr( __( "Anmeldeschutz aktivieren", 'cpsec' ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="login_lockout_notification" value="0"/>
                            <input type="checkbox"
                                   name="login_lockout_notification" <?php checked( 1, $settings->login_lockout_notification ) ?>
                                   value="1" class="toggle-checkbox"
                                   id="toggle_login_protection"/>
                            <label class="toggle-label" for="toggle_login_protection"></label>
                        </span>
                    <label><?php esc_html_e( "Anmeldeschutz Aussperrung", 'cpsec' ) ?></label>
                    <span class="sub inpos">
                        <?php esc_html_e( "Wenn ein Benutzer oder eine IP ausgesperrt wird, weil versucht wurde, auf Ihren Anmeldebereich zuzugreifen.", 'cpsec' ) ?>
                    </span>
                    <div class="clear mline"></div>
                    <span
                            tooltip="<?php echo esc_attr( __( "404-Erkennung aktivieren", 'cpsec' ) ) ?>"
                            class="toggle float-l">
                            <input type="hidden" name="ip_lockout_notification" value="0"/>
                            <input type="checkbox" name="ip_lockout_notification"
                                   value="1" <?php checked( 1, $settings->ip_lockout_notification ) ?>
                                   class="toggle-checkbox" id="toggle_404_detection"/>
                            <label class="toggle-label" for="toggle_404_detection"></label>
                        </span>
                    <label>
						<?php esc_html_e( "404 Erkennungssperre", 'cpsec' ) ?>
                    </label>
                    <span class="sub inpos"><?php esc_html_e( "Wenn ein Benutzer oder eine IP ausgesperrt wird, weil wiederholt auf nicht vorhandene Dateien zugegriffen wurde.", 'cpsec' ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "-Mail-Empfänger", 'cpsec' ) ?>
                    </label>
                    <span class="sub">
						<?php esc_html_e( "Wähle aus, welche Nutzer Deiner Webseite die Scan-Berichtsergebnisse per E-Mail erhalten sollen.", 'cpsec' ) ?>
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
						<?php esc_html_e( "Wiederholte Aussperrungen", 'cpsec' ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wenn Du zu viele E-Mails von IPs erhältst, die wiederholt ausgesperrt werden, kannst Du sie für einen bestimmten Zeitraum ausschalten.", 'cpsec' ) ?>
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
                    <label><?php _e( "E-Mail-Benachrichtigungen für wiederholte Aussperrungen begrenzen", 'cpsec' ) ?></label>
                    <div class="well well-white schedule-box">
                        <label><strong><?php _e( "Schwelle", 'cpsec' ) ?></strong>
                            - <?php _e( "Die Anzahl der Aussperrungen, bevor wir die E-Mails ausschalten", 'cpsec' ) ?>
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
                        <label><strong><?php _e( "Abkühlungszeitraum", 'cpsec' ) ?></strong>
                            - <?php _e( "Wie lange sollen wir sie ausschalten?", 'cpsec' ) ?>
                        </label>
                        <select name="cooldown_period" class="mline">
                            <option <?php selected( '1', $settings->cooldown_period ) ?>
                                    value="1"><?php _e( "1 Stunde", 'cpsec' ) ?></option>
                            <option <?php selected( '2', $settings->cooldown_period ) ?>
                                    value="2"><?php _e( "2 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '6', $settings->cooldown_period ) ?>
                                    value="6"><?php _e( "6 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '12', $settings->cooldown_period ) ?>
                                    value="12"><?php _e( "12 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '24', $settings->cooldown_period ) ?>
                                    value="24"><?php _e( "24 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '36', $settings->cooldown_period ) ?>
                                    value="36"><?php _e( "36 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '48', $settings->cooldown_period ) ?>
                                    value="48"><?php _e( "48 Stunden", 'cpsec' ) ?></option>
                            <option <?php selected( '168', $settings->cooldown_period ) ?>
                                    value="168"><?php _e( "7 Tage", 'cpsec' ) ?></option>
                            <option <?php selected( '720', $settings->cooldown_period ) ?>
                                    value="720"><?php _e( "30 Tage", 'cpsec' ) ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clear line"></div>
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <button type="submit" class="button button-primary float-r">
				<?php esc_html_e( "EINSTELLUNGEN AKTUALISIEREN", 'cpsec' ) ?>
            </button>
            <div class="clear"></div>
        </form>
    </div>
</div>