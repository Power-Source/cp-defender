<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "BENACHRICHTIGUNGEN", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" class="audit-frm audit-settings">
            <div class="columns">
                <div class="column is-one-third">
                    <strong><?php _e( "Audit Bericht", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "PS Security kann automatisch einen E-Mail-Bericht versenden, der die Ereignisse auf deiner Webseite zusammenfasst, sodass du die Protokolle im Blick behalten kannst, ohne hier erneut nachsehen zu müssen.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="notification" value="0"/>
                        <input type="checkbox" class="toggle-checkbox" name="notification" value="1"
                               id="chk1" <?php checked( 1, $setting->notification ) ?>/>
                        <label class="toggle-label" for="chk1"></label>
                    </span>
                    <label><?php _e( "Regelmäßige Berichte ausführen", cp_defender()->domain ) ?></label>
                    <div class="clear mline"></div>
                    <div class="well well-white schedule-box">
                        <strong><?php _e( "Zeitplan", cp_defender()->domain ) ?></strong>
                        <label><?php _e( "Häufigkeit", cp_defender()->domain ) ?></label>
                        <select name="frequency">
                            <option <?php selected( 1, $setting->frequency ) ?>
                                    value="1"><?php _e( "Täglich", cp_defender()->domain ) ?></option>
                            <option <?php selected( 7, $setting->frequency ) ?>
                                    value="7"><?php _e( "Wöchentlich", cp_defender()->domain ) ?></option>
                            <option <?php selected( 30, $setting->frequency ) ?>
                                    value="30"><?php _e( "Monatlich", cp_defender()->domain ) ?></option>
                        </select>
                        <div class="days-container">
                            <label><?php _e( "Tag der Woche", cp_defender()->domain ) ?></label>
                            <select name="day">
								<?php foreach ( \CP_Defender\Behavior\Utils::instance()->getDaysOfWeek() as $day ): ?>
                                    <option <?php selected( $day, $setting->day ) ?>
                                            value="<?php echo $day ?>"><?php echo ucfirst( $day ) ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                        <label><?php _e( "Uhrzeit", cp_defender()->domain ) ?></label>
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
                    <strong><?php _e( "E-Mail-Empfänger", cp_defender()->domain ) ?></strong>
                    <span class="sub">
                        <?php _e( "Wähle aus, welche Benutzer deiner Webseite die Scan-Berichtsergebnisse in ihrem E-Mail-Posteingang erhalten sollen.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
					<?php $email->renderInput() ?>
                </div>
            </div>
            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveAuditSettings"/>
			<?php wp_nonce_field( 'saveAuditSettings' ) ?>
            <button class="button float-r"><?php _e( "Einstellungen aktualisieren", cp_defender()->domain ) ?></button>
            <div class="clear"></div>
        </form>
    </div>
</div>