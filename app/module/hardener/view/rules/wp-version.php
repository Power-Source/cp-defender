<div class="rule closed" id="wp-version">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Aktualisiere WordPress auf die neueste Version", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line">
			<?php _e( "WordPress ist eine äußerst beliebte Plattform, und mit dieser Beliebtheit kommen Hacker, die zunehmend versuchen, WordPress-basierte Websites auszunutzen. Wenn du deine WordPress-Installation nicht auf dem neuesten Stand hältst, ist das fast eine Garantie dafür, gehackt zu werden!", cp_defender()->domain ) ?>
        </div>
        <div class="columns version-col">
            <div class="column">
                <strong><?php _e( "Aktuelle Version", cp_defender()->domain ) ?></strong>
			    <?php $class = $controller->check() ? 'def-tag tag-success' : 'def-tag tag-error' ?>
                <span class="<?php echo $class ?>">
                    <?php echo \CP_Defender\Behavior\Utils::instance()->getWPVersion() ?>
                </span>
            </div>
            <div class="column">
                <strong><?php _e( "Empfohlene Version", cp_defender()->domain ) ?></strong>
                <span><?php echo $controller->getService()->getLatestVersion() ?></span>
            </div>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well">
			<?php if ( $controller->check() ): ?>
				<?php echo function_exists('classicpress_version') ? __( "Du hast die neueste ClassicPress-Version installiert.", cp_defender()->domain ) : __( "Du hast die neueste WordPress-Version installiert.", cp_defender()->domain ) ?>
			<?php else: ?>
                <form method="post" class="hardener-frm">
					<?php $controller->createNonceField(); ?>
                    <input type="hidden" name="action" value="processHardener"/>
                    <input type="hidden" name="slug" value="<?php echo $controller::$slug ?>"/>
                    <a href="<?php echo network_admin_url('update-core.php') ?>" class="button float-r">
						<?php echo function_exists('classicpress_version') ? esc_html__( "Aktualisiere ClassicPress", cp_defender()->domain ) : esc_html__( "Aktualisiere WordPress", cp_defender()->domain ) ?>
                    </a>
                </form>
				<?php $controller->showIgnoreForm() ?>
                <div class="clear"></div>
			<?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
</div>