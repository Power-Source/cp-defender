<div class="dev-box">
	<div class="box-title">
		<h3><?php esc_html_e( "Anmeldeschutz", 'cpsec' ) ?></h3>
	</div>
	<div class="box-content tc">
		<img
			src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/lockout-man.svg"
			class="line"/>
		<p class="line max-600">
			<?php esc_html_e( "Beobachte und schütze Deinen Anmeldebereich vor Angreifern, die versuchen, zufällig Anmeldedaten für Ihre Website zu erraten. Defender wird sie nach einer festgelegten Anzahl fehlgeschlagener Versuche aussperren.", 'cpsec' ) ?>
				details for your site. Defender will lock them out after a set number of failed attempts.", 'cpsec' ) ?>
		</p>
		<form method="post" id="settings-frm" class="ip-frm">
			<?php wp_nonce_field( 'saveLockoutSettings' ) ?>
            <input type="hidden" name="action" value="saveLockoutSettings"/>
			<input type="hidden" name="login_protection" value="1"/>
			<button type="submit" class="button button-primary">
				<?php esc_html_e( "Aktivieren", 'cpsec' ) ?>
			</button>
		</form>
	</div>
</div>