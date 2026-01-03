<div class="dev-box">
    <div class="box-title">
        <h3 class="def-issues-title">
			<?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?>
        </h3>
    </div>
    <div class="box-content issues-box-content">
        <form method="post" id="advanced-settings-frm" class="advanced-settings-frm">
			<?php
			$class = 'line';
			$enabledRoles = $settings->userRoles;

			?>
            <p class="<?php echo $class ?>"><?php _e( "Konfiguriere deine Einstellungen zur Zwei-Faktor-Authentifizierung. Unsere Empfehlungen sind standardmäßig aktiviert.", cp_defender()->domain ) ?></p>
			<?php if ( isset( cp_defender()->global['compatibility'] ) ): ?>
                <div class="well well-error with-cap mline">
                    <i class="def-icon icon-warning icon-yellow "></i>
					<?php echo implode( '<br/>', cp_defender()->global['compatibility'] ); ?>
                </div>
			<?php endif; ?>
			<?php
			if ( count( $enabledRoles ) ):
				?>
                <div class="well well-green with-cap">
                    <i class="def-icon icon-tick"></i>
					<?php
					printf( __( "<strong>Zwei-Faktor-Authentifizierung ist jetzt aktiv.</strong> Benutzerrollen mit aktivierter Funktion müssen ihre <a href='%s'>Profilseite</a> besuchen, um die Einrichtung abzuschließen und ihr Konto mit der Authenticator-App zu synchronisieren.", cp_defender()->domain ),
						admin_url( 'profile.php' ) );
					?>
                </div>
			<?php else: ?>
                <div class="well well-yellow with-cap">
                    <i class="def-icon icon-warning"></i>
					<?php
					_e( "<strong>Zwei-Faktor-Authentifizierung ist derzeit inaktiv.</strong> Konfiguriere und speichere deine Einstellungen, um die Einrichtung abzuschließen.", cp_defender()->domain )
					?>
                </div>
			<?php endif; ?>
            <div class="columns">
                <div class="column is-one-third">
                    <label><?php _e( "Benutzerrollen", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Wähle die Benutzerrollen aus, für die du die Zwei-Faktor-Authentifizierung aktivieren möchtest. Benutzer mit diesen Rollen müssen dann die Google Authenticator-App zur Anmeldung verwenden.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <ul class="dev-list marginless">
                        <li class="list-header">
                            <div>
                                <span class="list-label"><?php _e( "Benutzerrolle", cp_defender()->domain ) ?></span>
                            </div>
                        </li>
						<?php
						$enabledRoles = $settings->userRoles;
						$allRoles     = get_editable_roles();
						foreach ( $allRoles as $role => $detail ):
							?>
                            <li>
                                <div>
                                    <span class="list-label">
                                        <?php echo $detail['name'] ?>
                                    </span>
                                    <div class="list-detail">
                                    <span class="toggle">
                                        <input type="checkbox" <?php echo in_array( $role, $enabledRoles ) ? 'checked="checked"' : null ?>
                                               name="userRoles[]"
                                               value="<?php echo esc_attr( $role ) ?>"
                                               class="toggle-checkbox"
                                               id="toggle_<?php echo esc_attr( $role ) ?>_role"/>
                                        <label class="toggle-label"
                                               for="toggle_<?php echo esc_attr( $role ) ?>_role"></label>
                                    </span>
                                    </div>
                                </div>
                            </li>
						<?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label><?php _e( "Telefon verloren", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Wenn ein Benutzer keinen Zugriff auf sein Telefon hat, kannst du eine Option aktivieren, um das Einmalpasswort an seine registrierte E-Mail-Adresse zu senden.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <span class="toggle">
                        <input type="hidden" name="lostPhone" value="0"/>
                        <input type="checkbox" checked="checked" name="lostPhone" value="1"
                               class="toggle-checkbox" id="toggle_lost_phone"/>
                        <label class="toggle-label" for="toggle_lost_phone"></label>
                    </span>&nbsp;
                    <span><?php _e( "Verlorenes Telefon aktivieren", cp_defender()->domain ) ?></span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label><?php _e( "App-Download", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Benötigst du die App? Hier sind Links zu den offiziellen Google Authenticator-Apps.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <a href="https://itunes.apple.com/vn/app/google-authenticator/id388497605?mt=8">
                        <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/ios-download.svg' ?>"/>
                    </a>
                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">
                        <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/android-download.svg' ?>"/>
                    </a>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <label><?php _e( "Aktive Benutzer", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Hier ist ein schneller Link, um zu sehen, welche deiner Benutzer die Zwei-Faktor-Authentifizierung aktiviert haben.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
					<?php printf( __( "<a href=\"%s\">Benutzer anzeigen</a>, die diese Funktion aktiviert haben.", cp_defender()->domain ), network_admin_url( 'users.php' ) ) ?>
                </div>
            </div>
            <div class="columns mline">
                <div class="column is-one-third">
                    <label><?php _e( "Deaktivieren", cp_defender()->domain ) ?></label>
                    <span class="sub">
                        <?php _e( "Deaktiviere die Zwei-Faktor-Authentifizierung auf deiner Webseite.", cp_defender()->domain ) ?>
                    </span>
                </div>
                <div class="column">
                    <button type="button" class="button button-secondary deactivate-2factor">
						<?php _e( "Deaktivieren", cp_defender()->domain ) ?>
                    </button>
                </div>
            </div>
            <div class="clear line"></div>
            <input type="hidden" name="action" value="saveAdvancedSettings"/>
			<?php wp_nonce_field( 'saveAdvancedSettings' ) ?>
            <button type="submit" class="button button-primary float-r">
				<?php _e( "EINSTELLUNGEN SPEICHERN", cp_defender()->domain ) ?>
            </button>
            <div class="clear"></div>
        </form>
    </div>
</div>