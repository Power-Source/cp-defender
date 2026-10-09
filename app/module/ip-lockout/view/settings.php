<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Einstellungen", 'cpsec' ) ?></h3>
    </div>
    <div class="box-content">
        <form method="post" id="settings-frm" class="ip-frm">
            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_login_attempt">
						<?php esc_html_e( "Speicher", 'cpsec' ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Ereignisprotokolle werden auf Deinem lokalen Server zwischengespeichert, um die Ladezeiten zu verkürzen. Du kannst festlegen, wie viele Tage die Protokolle aufbewahrt werden sollen, bevor sie gelöscht werden.", 'cpsec' ) ?>
						</span>
                </div>
                <div class="column">
                    <input size="8" value="<?php echo $settings->storage_days ?>" type="text"
                           class="inline" id="storage_days"
                           name="storage_days"/>
                    <span><?php esc_html_e( "Tage", 'cpsec' ) ?></span>&nbsp;
                    <span class="form-help"><?php _e( "Wähle aus, wie viele Tage der Ereignisprotokolle Du lokal speichern möchtest.", 'cpsec' ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label for="login_protection_login_attempt">
						<?php esc_html_e( "Protokolle löschen", 'cpsec' ) ?>
                    </label>
                    <span class="sub">
                        <?php esc_html_e( "Wenn Du Deine aktuellen Protokolle löschen möchtest, klicke einfach auf Löschen und alle Protokolle werden bereinigt.", 'cpsec' ) ?>
						</span>
                </div>
                <div class="column">
                    <button type="button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'lockoutEmptyLogs' ) ) ?>"
                            class="button button-secondary empty-logs"><?php _e( "Protokolle löschen", 'cpsec' ) ?></button>
                    <span class="delete-status"></span>
                    <span class="form-help"><?php _e( "Hinweis: Defender entfernt sofort alle vergangenen Ereignisprotokolle, Du kannst sie nicht wiederherstellen.", 'cpsec' ) ?></span>
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