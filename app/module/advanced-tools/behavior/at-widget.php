<?php
/**
 * Author: Hoang Ngo
 */

namespace CP_Defender\Module\Advanced_Tools\Behavior;

use Hammer\Base\Behavior;
use CP_Defender\Module\Advanced_Tools\Model\Auth_Settings;

class AT_Widget extends Behavior {
	public function renderATWidget() {
		?>
        <div class="dev-box advanced-tools">
            <div class="box-title">
                <span class="span-icon icon-scan"></span>
                <h3><?php _e( "Erweiterte Werkzeuge", cp_defender()->domain ) ?>
                </h3>

            </div>
            <div class="box-content">
                <p class="line end">
					<?php _e( "Aktiviere erweiterte Werkzeuge für einen verbesserten Schutz gegen selbst die aggressivsten Hacker und Bots.", cp_defender()->domain ) ?>
                </p>
                <div class="at-line">
                    <strong>
						<?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?>
                    </strong>
                    <span>
						<?php
						_e( "Füge WordPress-Konten eine zusätzliche Sicherheitsebene hinzu, um sicherzustellen, dass nur User sich anmelden können, selbst wenn jemand anderes deren Passwort kennt", cp_defender()->domain )
						?>
                    </span>
					<?php
					$settings = Auth_Settings::instance();
					if ( $settings->enabled ):
						$enabledRoles = $settings->userRoles;
						if ( count( $enabledRoles ) ):
							?>
                            <div class="well well-small well-green with-cap">
                                <i class="def-icon icon-tick"></i>
                                <span>
                                <?php printf( __( "<strong>Zwei-Faktor-Authentifizierung ist jetzt aktiv.</strong> Um diese Funktion zu aktivieren, gehe zu <a href='%s'>Ihrem Profil</a>, um die Einrichtung abzuschließen und die gewählten Konten mit der Authenticator-App zu synchronisieren.", cp_defender()->domain ),
	                                admin_url( 'profile.php' ) ) ?>
                            </span>
                            </div>
						<?php else: ?>
                            <div class="well well-small well-yellow with-cap">
                                <i class="def-icon icon-warning"></i>
                                <span>
                                    <?php _e( "Zwei-Faktor-Authentifizierung ist derzeit inaktiv. Konfiguriere und speichere deine Einstellungen, um die Einrichtung abzuschließen.", cp_defender()->domain ) ?>
                                </span>
                                <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-advanced-tools' ) ?>"><?php _e( "Einrichtung abschließen", cp_defender()->domain ) ?></a>
                            </div>
						<?php endif; ?>
                        <p>
                            <span>
                            <?php _e( "Hinweis: Jeder Benutzer auf Deiner Webseite muss die Zwei-Faktor-Authentifizierung individuell über sein Benutzerprofil aktivieren, um diese Sicherheitsfunktion zu aktivieren und zu verwenden.", cp_defender()->domain ) ?>
                        </span>
                        </p>
					<?php else: ?>
                        <form method="post" id="advanced-settings-frm" class="advanced-settings-frm">
                            <input type="hidden" name="action" value="saveAdvancedSettings"/>
							<?php wp_nonce_field( 'saveAdvancedSettings' ) ?>
                            <input type="hidden" name="enabled" value="1"/>
                            <button type="submit" class="button button-primary button-small">
								<?php _e( "Aktivieren", cp_defender()->domain ) ?>
                            </button>
                        </form>
					<?php endif; ?>
                </div>
            </div>
        </div>
		<?php
	}
}