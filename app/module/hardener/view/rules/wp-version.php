<div class="rule closed" id="wp-version">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Aktualisiere WordPress auf die neueste Version", 'cpsec' ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", 'cpsec' ) ?></h3>
        <div class="line">
			<?php _e( "WordPress ist eine äußerst beliebte Plattform, und mit dieser Beliebtheit kommen Hacker, die zunehmend versuchen, WordPress-basierte Websites auszunutzen. Wenn du deine WordPress-Installation nicht auf dem neuesten Stand hältst, ist das fast eine Garantie dafür, gehackt zu werden!", 'cpsec' ) ?>
        </div>
        <div class="columns version-col">
            <div class="column">
                <strong><?php _e( "Aktuelle Version", 'cpsec' ) ?></strong>
			    <?php $class = $controller->check() ? 'def-tag tag-success' : 'def-tag tag-error' ?>
                <span class="<?php echo $class ?>">
                    <?php echo \CP_Defender\Behavior\Utils::instance()->getWPVersion() ?>
                </span>
            </div>
            <div class="column">
                <strong><?php _e( "Empfohlene Version", 'cpsec' ) ?></strong>
                <span><?php echo $controller->getService()->getLatestVersion() ?></span>
            </div>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", 'cpsec' ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
				<?php echo function_exists('classicpress_version') ? __( "Du hast die neueste ClassicPress-Version installiert.", 'cpsec' ) : __( "Du hast die neueste WordPress-Version installiert.", 'cpsec' ) ?>
			<?php else: ?>
                <form method="post" class="hardener-frm">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <a href="<?php echo network_admin_url('update-core.php') ?>" class="button float-r">
						<?php echo function_exists('classicpress_version') ? esc_html__( "Aktualisiere ClassicPress", 'cpsec' ) : esc_html__( "Aktualisiere WordPress", 'cpsec' ) ?>
                    </a>
                </form>
				<?php $controller->showIgnoreForm() ?>
                <div class="clear"></div>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>