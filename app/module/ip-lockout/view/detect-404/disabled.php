<div class="dev-box">
    <div class="box-title">
        <h3><?php esc_html_e( "404-ERKENNUNG", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content tc">
        <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/lockout-man.svg"
             class="intro line"/>
        <p class="intro max-600 line">
			<?php esc_html_e( "Mit aktivierter 404-Erkennung behält Defender IP-Adressen im Auge, die wiederholt Seiten auf deiner Website anfordern, die nicht existieren, und blockiert sie dann vorübergehend am Zugriff auf deine Website.", cp_defender()->domain ) ?>
        </p>
        <form method="post" id="settings-frm" class="ip-frm">
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
            <input type="hidden" name="detect_404" value="1"/>
            <button type="submit" class="button button-primary">
				<?php esc_html_e( "Aktivieren", cp_defender()->domain ) ?>
            </button>
        </form>
    </div>
</div>