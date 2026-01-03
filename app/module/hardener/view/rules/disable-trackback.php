<div class="rule closed" id="disable_trackback">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Trackbacks und Pingbacks deaktivieren", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line end">
			<?php _e( "Pingbacks benachrichtigen eine Website, wenn sie von einer anderen Webseite erwähnt wurde, ähnlich einer Höflichkeitskommunikation. Diese Benachrichtigungen können jedoch an jede Website gesendet werden, die bereit ist, sie zu empfangen, was dich für DDoS-Angriffe öffnet, die deine Website in Sekunden lahmlegen und deine Beiträge mit Spam-Kommentaren füllen können.", cp_defender()->domain ) ?>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
                <p class="mline"><?php _e( "Trackbacks und Pingbacks sind deaktiviert.", cp_defender()->domain ) ?></p>
                <form method="post" class="hardener-frm rule-process">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processRevert"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button button-small button-grey"
                            type="submit"><?php _e( "Zurücksetzen", cp_defender()->domain ) ?></button>
                </form>
			<?php else: ?>
                <div class="line">
                    <p><?php _e( "Wir werden Trackbacks und Pingbacks in deinen WordPress-Einstellungen deaktivieren.", cp_defender()->domain ) ?></p>
                </div>
                <label>
					<?php if ( is_multisite() ) : ?>
						<?php _e( "Deaktiviere Pingbacks für alle vorhandenen Beiträge in allen Seiten", cp_defender()->domain ); ?>
					<?php else: ?>
						<?php _e( "Deaktiviere Pingbacks für alle vorhandenen Beiträge", cp_defender()->domain ); ?>
					<?php endif; ?>
					<span class="toggle float-r">
						<input type="checkbox" name="update_posts" value="1" class="toggle-checkbox trackback-toggle-update-posts" id="toggle_update_posts"/>
						<label class="toggle-label" for="toggle_update_posts"></label>
					</span>
				</label>
				<div class="clear mline"></div>
                <form method="post" class="hardener-frm rule-process hardener-frm-process-trackback">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="hidden" name="updatePosts" value="no"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <button class="button float-r"
                            type="submit"><?php _e( "Pingbacks deaktivieren", cp_defender()->domain ) ?></button>
                </form>
				<?php $controller->showIgnoreForm() ?>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>