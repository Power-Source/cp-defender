<div class="dev-box">
    <form method="post" id="settings-frm" class="ip-frm">
        <div class="box-title">
            <h3><?php esc_html_e( "404-ERKENNUNG", cp_defender()->domain ) ?></h3>
            <div class="side float-r">
                <div>
                    <span tooltip="<?php esc_attr_e( "404-Erkennung deaktivieren", cp_defender()->domain ) ?>" class="toggle">
                        <input type="hidden" name="detect_404" value="0"/>
                        <input type="checkbox" checked="checked" class="toggle-checkbox"
                               id="toggle_404_detection" name="detect_404" value="1"/>
                        <label class="toggle-label" for="toggle_404_detection"></label>
                    </span>
                </div>
            </div>
        </div>
        <div class="box-content">
			<?php if ( ( $count = ( \CP_Defender\Module\IP_Lockout\Component\Login_Protection_Api::get404Lockouts( strtotime( '-24 hours', current_time( 'timestamp' ) ) ) ) ) > 0 ): ?>
                <div class="well well-yellow">
					<?php echo sprintf( __( "Es gab %d Sperrungen in den letzten 24 Stunden. <a href=\"%s\"><strong>Protokoll anzeigen</strong></a>.", cp_defender()->domain ), $count, \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'logs' ) ) ) ?>
                </div>
			<?php else: ?>
                <div class="well well-blue">
					<?php esc_html_e( "404-Erkennung ist aktiviert. Es sind noch keine Sperrungen protokolliert.", cp_defender()->domain ) ?>
                </div>
			<?php endif; ?>
            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_threshold">
						<?php esc_html_e( "Sperrschwelle", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Gib an, wie viele 404-Fehler innerhalb eines bestimmten Zeitraums eine Sperrung auslösen.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input size="8" value="<?php echo $settings->detect_404_threshold ?>" id="detect_404_threshold"
                           name="detect_404_threshold" type="text" class="inline">
                    <span class=""><?php esc_html_e( "404 Fehler innerhalb von", cp_defender()->domain ) ?></span>&nbsp;
                    <input size="8" value="<?php echo $settings->detect_404_timeframe ?>" id="detect_404_timeframe"
                           name="detect_404_timeframe" type="text" class="inline">
                    <span class=""><?php esc_html_e( "Sekunden", cp_defender()->domain ) ?></span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_lockout_timeframe">
						<?php esc_html_e( "Sperrzeit", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Wähle, wie lange der gesperrte Benutzer gesperrt bleiben soll.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input value="<?php echo $settings->detect_404_lockout_duration ?>" size="8"
                           name="detect_404_lockout_duration"
                           id="detect_404_lockout_duration" type="text" class="inline"/>
                    <span class=""><?php esc_html_e( "Sekunden", cp_defender()->domain ) ?></span>
                    <div class="clearfix"></div>
                    <input type="hidden" name="detect_404_lockout_ban" value="0"/>
                    <input id="detect_404_lockout_ban" <?php checked( 1, $settings->detect_404_lockout_ban ) ?>
                           type="checkbox"
                           name="detect_404_lockout_ban" value="1">
                    <label for="detect_404_lockout_ban"
                           class="inline form-help is-marginless"><?php esc_html_e( 'Sperre 404-Sperrungen dauerhaft.', cp_defender()->domain ) ?></label>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_lockout_message">
						<?php esc_html_e( "Sperrnachricht", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Passe die Nachricht an, die gesperrte Benutzer sehen werden.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
						<textarea name="detect_404_lockout_message"
                                  id="detect_404_lockout_message"><?php echo $settings->detect_404_lockout_message ?></textarea>
                    <span class="form-help">
                                        <?php echo sprintf( __( "Diese Nachricht wird während der Sperrzeit auf deiner Website angezeigt. Siehe eine schnelle Vorschau <a href=\"%s\">hier</a>.", cp_defender()->domain ), add_query_arg( array(
	                                        'def-lockout-demo' => 1,
	                                        'type'             => '404'
                                        ), network_site_url() ) ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_whitelist">
						<?php esc_html_e( "Whitelist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Wenn du weißt, dass eine häufig verwendete Datei auf deiner Website fehlt, kannst du sie hier eintragen, damit sie nicht zu einer Sperrung führt.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
					<textarea id="detect_404_whitelist" name="detect_404_whitelist"
                              rows="8"><?php echo $settings->detect_404_whitelist ?></textarea>
                    <span class="form-help">
                                        <?php esc_html_e( "Du musst den vollständigen Pfad beginnend mit einem / angeben.", cp_defender()->domain ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="detect_404_ignored_filetypes">
						<?php esc_html_e( "Ignoriere Dateitypen", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Wähle aus, für welche Dateitypen du Fehler protokollieren, aber keine Sperrung auslösen möchtest.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
					<textarea id="detect_404_ignored_filetypes" name="detect_404_ignored_filetypes"
                              rows="8"><?php echo $settings->detect_404_ignored_filetypes ?></textarea>
                    <span class="form-help">
                                        <?php esc_html_e( "PS Security protokolliert den 404-Fehler, sperrt den Benutzer jedoch für diese Dateitypen nicht.", cp_defender()->domain ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label>
						<?php esc_html_e( "Ausnahmen", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
                                        <?php esc_html_e( "Standardmäßig überwacht PS Security alle Interaktionen mit deiner Website, aber du kannst die 404-Erkennung für bestimmte Bereiche deiner Website deaktivieren.", cp_defender()->domain ) ?>
                                    </span>
                </div>
                <div class="column">
                    <input type="hidden" name="detect_404_logged" value="0"/>
                    <input id="detect_404_logged" <?php checked( 1, $settings->detect_404_logged ) ?>
                           type="checkbox"
                           name="detect_404_logged" value="1">
                    <label for="detect_404_logged"
                           class="inline form-help is-marginless"><?php esc_html_e( 'Überwache 404s von angemeldeten Benutzern', cp_defender()->domain ) ?></label>
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