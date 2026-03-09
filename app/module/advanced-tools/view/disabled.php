<div class="dev-box">
    <div class="box-title">
        <h3 class="def-issues-title">
			<?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?>
        </h3>
    </div>
    <div class="box-content issues-box-content tc">
        <p>
            <?php _e( "Stärke die Sicherheit Deiner Webseite mit der Zwei-Faktor-Authentifizierung. Füge einen zusätzlichen Schritt im Anmeldeprozess hinzu, sodass Benutzer sowohl ein Passwort als auch einen zusätzlichen Code eingeben müssen (Authenticator-App oder optional E-Mail-Code).", cp_defender()->domain ) ?>
        </p>
        <p>
			<strong><?php _e( "Standard-Methode:", cp_defender()->domain ) ?></strong>
			<?php _e( "Authenticator-App (TOTP)", cp_defender()->domain ) ?>
        </p>
        <p>
            <a href="https://itunes.apple.com/vn/app/google-authenticator/id388497605?mt=8">
                <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/ios-download.svg' ?>"/>
            </a>
            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">
                <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/android-download.svg' ?>"/>
            </a>
        </p>
        <form method="post" id="advanced-settings-frm" class="advanced-settings-frm">
            <div class="columns" style="text-align:left; max-width:560px; margin:0 auto;">
                <div class="column is-one-third">
                    <label><?php _e( "App-Verifizierung", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Erlaube Benutzern, die Authenticator-App (TOTP) als Zwei-Faktor-Methode zu verwenden.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="allowAppAuth" value="0"/>
                        <input type="checkbox" <?php checked( ! empty( $settings->allowAppAuth ) ); ?> name="allowAppAuth" value="1" class="toggle-checkbox" id="toggle_allow_app_auth_disabled"/>
                        <label class="toggle-label" for="toggle_allow_app_auth_disabled"></label>
                    </span>&nbsp;
                    <span><?php _e( "Authenticator-App als 2FA-Methode erlauben", cp_defender()->domain ) ?></span>
                </div>
            </div>
            <div class="columns" style="text-align:left; max-width:560px; margin:0 auto;">
                <div class="column is-one-third">
                    <label><?php _e( "E-Mail-Verifizierung", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Erlaube Benutzern, die Zwei-Faktor-Authentifizierung per E-Mail-Code im Profil auszuwählen.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="allowEmailAuth" value="0"/>
                        <input type="checkbox" <?php checked( ! empty( $settings->allowEmailAuth ) ); ?> name="allowEmailAuth" value="1" class="toggle-checkbox" id="toggle_allow_email_auth_disabled"/>
                        <label class="toggle-label" for="toggle_allow_email_auth_disabled"></label>
                    </span>&nbsp;
                    <span><?php _e( "E-Mail-Code als 2FA-Methode erlauben", cp_defender()->domain ) ?></span>
                </div>
            </div>

            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveAdvancedSettings"/>
			<?php wp_nonce_field( 'saveAdvancedSettings' ) ?>
            <input type="hidden" name="enabled" value="1"/>
            <button type="submit" class="button button-primary">
				<?php _e( "Aktivieren", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </form>
    </div>
</div>