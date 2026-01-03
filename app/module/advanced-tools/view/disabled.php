<div class="dev-box">
    <div class="box-title">
        <h3 class="def-issues-title">
			<?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?>
        </h3>
    </div>
    <div class="box-content issues-box-content tc">
        <p>
			<?php _e( "Stärke die Sicherheit Deiner Webseite mit der Zwei-Faktor-Authentifizierung. Füge einen zusätzlichen Schritt im Anmeldeprozess hinzu, sodass Benutzer sowohl ein Passwort als auch einen app-generierten Passcode auf ihrem Telefon eingeben müssen – der beste Schutz gegen Brute-Force-Angriffe.", cp_defender()->domain ) ?>
        </p>
        <form method="post" id="advanced-settings-frm" class="advanced-settings-frm">

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