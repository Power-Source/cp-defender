<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "BENACHRICHTIGUNGEN", 'cpsec' ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" class="audit-frm audit-settings">
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Audit Bericht", 'cpsec' ) ?></strong>
                    <span class="sub">
                        <?php _e( "PS Security kann automatisch einen E-Mail-Bericht versenden, der die Ereignisse auf deiner Webseite zusammenfasst, sodass du die Protokolle im Blick behalten kannst, ohne hier erneut nachsehen zu müssen.", 'cpsec' ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="notification" value="0"/>
                        <input type="checkbox" class="toggle-checkbox" name="notification" value="1"
                               id="chk1" <?php checked( 1, $setting->notification ) ?>/>
                        <label class="toggle-label" for="chk1"></label>
                    </span>
                    <label><?php _e( "Regelmäßige Berichte ausführen", 'cpsec' ) ?></label>
                    <div class="clear mline"></div>
                    <div class="well well-white schedule-box">
                        <strong><?php _e( "Zeitplan", 'cpsec' ) ?></strong>
                        <label><?php _e( "Häufigkeit", 'cpsec' ) ?></label>
                        <select name="frequency">
                            <option <?php selected( 1, $setting->frequency ) ?>
                                    value="1"><?php _e( "Täglich", 'cpsec' ) ?></option>
                            <option <?php selected( 7, $setting->frequency ) ?>
                                    value="7"><?php _e( "Wöchentlich", 'cpsec' ) ?></option>
                            <option <?php selected( 30, $setting->frequency ) ?>
                                    value="30"><?php _e( "Monatlich", 'cpsec' ) ?></option>
                        </select>
                        <div class="days-container">
                            <label><?php _e( "Tag der Woche", 'cpsec' ) ?></label>
                            <select name="day">
								<?php foreach ( \CP_Defender\Behavior\Utils::instance()->getDaysOfWeek() as $day ): ?>
                                    <option <?php selected( $day, $setting->day ) ?>
                                            value="<?php echo $day ?>"><?php echo ucfirst( $day ) ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <label><?php _e( "Uhrzeit", 'cpsec' ) ?></label>
                        <select name="time">
							<?php foreach ( \CP_Defender\Behavior\Utils::instance()->getTimes() as $time ): ?>
                                <option <?php selected( $time, $setting->time ) ?>
                                        value="<?php echo $time ?>"><?php echo strftime( '%I:%M %p', strtotime( $time ) ) ?></option>
							<?php endforeach;; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "E-Mail-Empfänger", 'cpsec' ) ?></strong>
                    <span class="sub">
                        <?php _e( "Wähle aus, welche Benutzer deiner Webseite die Scan-Berichtsergebnisse in ihrem E-Mail-Posteingang erhalten sollen.", 'cpsec' ) ?>
                    </span>
                </div>
                <div class="column">
					<?php $email->renderInput() ?>
                </div>
            </div>
            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveAuditSettings"/>
			<?php wp_nonce_field( 'saveAuditSettings' ) ?>
            <button class="button float-r"><?php _e( "Einstellungen aktualisieren", 'cpsec' ) ?></button>
            <div class="clear"></div>
        </form>
    </div>
</div>