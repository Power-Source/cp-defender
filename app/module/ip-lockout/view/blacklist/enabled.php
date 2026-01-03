<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "IP-Sperrung", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <p class="intro">
				<?php _e( "Wähle die IP-Adressen aus, die du dauerhaft vom Zugriff auf deine Webseite ausschließen möchtest.", cp_defender()->domain ) ?>
            </p>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="ip_blacklist">
						<?php _e( "Blacklist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Alle IP-Adressen, die du hier auflistest, werden vollständig vom Zugriff auf unsere Website ausgeschlossen, einschließlich Administratoren.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<textarea name="ip_blacklist" id="ip_blacklist"
                              rows="8"><?php echo $settings->ip_blacklist ?></textarea>
                    <span class="form-help">
						<?php _e( "Eine IP-Adresse pro Zeile und nur im IPv4-Format. IP-Bereiche werden im Format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx akzeptiert", cp_defender()->domain ) ?>
					</span>
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
						<textarea name="ip_lockout_message"
                                  id="ip_lockout_message"><?php echo $settings->ip_lockout_message ?></textarea>
                    <span class="form-help">
                                         <?php echo sprintf( __( "Diese Nachricht wird auf deiner Website für jede IP angezeigt, die mit deiner Blacklist übereinstimmt. Siehe eine schnelle Vorschau <a href=\"%s\">hier</a>.", cp_defender()->domain ), add_query_arg( array(
	                                         'def-lockout-demo' => 1,
	                                         'type'             => 'blacklist'
                                         ), network_site_url() ) ) ?>
                                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="ip_whitelist">
						<?php _e( "Whitelist", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Alle IP-Adressen, die du hier auflistest, sind von den Optionen ausgenommen, die du für Login-Schutz und 404-Erkennung ausgewählt hast.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
					<textarea name="ip_whitelist" id="ip_whitelist"
                              rows="8"><?php echo $settings->ip_whitelist ?></textarea>
                    <span class="form-help">
						<?php _e( "Eine IP-Adresse pro Zeile und nur im IPv4-Format. IP-Bereiche werden im Format xxx.xxx.xxx.xxx-xxx.xxx.xxx.xxx akzeptiert", cp_defender()->domain ) ?>
					</span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="import">
						<?php _e( "Import", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Importiere deine Blacklist und Whitelist von einer anderen Website (CSV-Datei).", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <div class="upload-input">
                        <input disabled="disabled" type="text" id="import">
                        <input type="hidden" name="file_import" id="file_import">
                        <button type="button" class="button button-light file-picker">
							<?php _e( "Auswählen", cp_defender()->domain ) ?></button>
                        <button type="button" class="button button-grey btn-import-ip">
							<?php _e( "Importieren", cp_defender()->domain ) ?>
                        </button>
                    </div>
                    <span class="form-help">
                        <?php _e( "Lade deine exportierte Blacklist hoch. Hinweis: Bestehende IP-Adressen werden nicht entfernt, nur neue IP-Adressen hinzugefügt.", cp_defender()->domain ) ?>
                    </span>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">
                    <label for="import">
						<?php _e( "Exportieren", cp_defender()->domain ) ?>
                    </label>
                    <span class="sub">
						<?php _e( "Exportiere sowohl deine Blacklist als auch deine Whitelist als CSV-Datei, um sie auf einer anderen Website zu verwenden.", cp_defender()->domain ) ?>
					</span>
                </div>
                <div class="column">
                    <p>
                        <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-ip-lockout', array( 'view' => 'export', '_wpnonce' => wp_create_nonce( 'defipexport' ) ) ) ?>"
                           class="button button-secondary export">
							<?php _e( "Exportieren", cp_defender()->domain ) ?></a>
                    </p>
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