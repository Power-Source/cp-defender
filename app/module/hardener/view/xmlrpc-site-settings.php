<?php

use CP_Defender\Module\Hardener\Component\XML_RPC_Protection_Service;
?>
<div class="wrap">
	<div id="cp-defender" class="cp-defender">
		<h2 class="title"><?php esc_html_e( 'XML-RPC-Schutz', 'cpsec' ); ?></h2>
		<div class="hardener">
			<div class="dev-box">
				<div class="box-title"><h3><?php esc_html_e( 'Einstellungen dieser Website', 'cpsec' ); ?></h3></div>
				<div class="box-content">
					<?php if ( isset( $_GET['updated'] ) ): ?>
						<div class="well well-green"><p><?php esc_html_e( 'Die XML-RPC-Einstellungen wurden gespeichert.', 'cpsec' ); ?></p></div>
					<?php endif; ?>
					<p><?php esc_html_e( 'Der Netzwerkadministrator hat lokale XML-RPC-Einstellungen für diese Website freigegeben.', 'cpsec' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'cp_defender_save_xmlrpc_site_settings' ); ?>
						<input type="hidden" name="action" value="cp_defender_save_xmlrpc_site_settings"/>
						<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_INHERIT ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_INHERIT ); ?> /> <?php printf( esc_html__( 'Netzwerk-Vorgabe verwenden (%s)', 'cpsec' ), esc_html( $network_mode ) ); ?></label></p>
						<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_OFF ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_OFF ); ?> /> <?php esc_html_e( 'Deaktiviert – XML-RPC unverändert zulassen', 'cpsec' ); ?></label></p>
						<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_MULTICALL ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_MULTICALL ); ?> /> <?php esc_html_e( 'Brute-Force-Schutz – system.multicall blockieren', 'cpsec' ); ?></label></p>
						<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK ); ?> /> <?php esc_html_e( 'Brute-Force- und Pingback-Schutz – system.multicall und pingback.ping blockieren', 'cpsec' ); ?></label></p>
						<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_DISABLED ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_DISABLED ); ?> /> <?php esc_html_e( 'XML-RPC vollständig deaktivieren', 'cpsec' ); ?></label></p>
						<button class="button float-r" type="submit"><?php esc_html_e( 'Einstellungen speichern', 'cpsec' ); ?></button>
						<div class="clear"></div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
