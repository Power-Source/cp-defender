<?php

use CP_Defender\Module\Hardener\Component\XML_RPC_Protection_Service;

$mode = XML_RPC_Protection_Service::getNetworkMode();
$allow_site_overrides = XML_RPC_Protection_Service::isSiteOverrideAllowed();
$is_network = \Hammer\Helper\WP_Helper::is_network_activate( cp_defender()->plugin_slug );
$action = $controller->check() ? 'updateHardener' : 'processHardener';
?>
<div class="rule closed" id="xmlrpc-protection">
	<div class="rule-title">
		<i class="def-icon <?php echo $controller->check() ? 'icon-tick' : 'icon-warning'; ?>" aria-hidden="true"></i>
		<?php echo $controller->getTitle(); ?>
	</div>
	<div class="rule-content">
		<h3><?php esc_html_e( 'Übersicht', 'cpsec' ); ?></h3>
		<div class="line end">
			<p><?php esc_html_e( 'XML-RPC wird von älteren Anwendungen und Integrationen benötigt, kann aber für gebündelte Brute-Force-Versuche und Pingback-Missbrauch verwendet werden.', 'cpsec' ); ?></p>
		</div>
		<h3><?php esc_html_e( 'Einstellungen', 'cpsec' ); ?></h3>
		<div class="well">
			<form method="post" class="hardener-frm rule-process">
				<?php $controller->createNonceField(); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>"/>
				<input type="hidden" name="slug" value="<?php echo esc_attr( $controller::$slug ); ?>"/>
				<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_OFF ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_OFF ); ?> /> <?php esc_html_e( 'Deaktiviert – XML-RPC unverändert zulassen', 'cpsec' ); ?></label></p>
				<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_MULTICALL ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_MULTICALL ); ?> /> <?php esc_html_e( 'Brute-Force-Schutz – system.multicall blockieren', 'cpsec' ); ?></label></p>
				<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_MULTICALL_PINGBACK ); ?> /> <?php esc_html_e( 'Brute-Force- und Pingback-Schutz – system.multicall und pingback.ping blockieren', 'cpsec' ); ?></label></p>
				<p><label><input type="radio" name="xmlrpc_protection_mode" value="<?php echo esc_attr( XML_RPC_Protection_Service::MODE_DISABLED ); ?>" <?php checked( $mode, XML_RPC_Protection_Service::MODE_DISABLED ); ?> /> <?php esc_html_e( 'XML-RPC vollständig deaktivieren', 'cpsec' ); ?></label></p>
				<?php if ( $is_network ): ?>
					<p><label><input type="checkbox" name="allow_site_overrides" value="1" <?php checked( $allow_site_overrides ); ?> /> <?php esc_html_e( 'Subsite-Administratoren dürfen unter Werkzeuge eine abweichende Schutzstufe wählen.', 'cpsec' ); ?></label></p>
				<?php endif; ?>
				<button class="button float-r" type="submit"><?php esc_html_e( 'Einstellungen speichern', 'cpsec' ); ?></button>
				<div class="clear"></div>
			</form>
			<?php if ( ! $controller->check() ): ?>
				<?php $controller->showIgnoreForm(); ?>
			<?php else: ?>
				<form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
					<input type="hidden" name="action" value="processRevert"/>
					<input type="hidden" name="slug" value="<?php echo esc_attr( $controller::$slug ); ?>"/>
					<button class="button button-small button-grey" type="submit"><?php esc_html_e( 'Zurücksetzen', 'cpsec' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
