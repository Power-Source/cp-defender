<div class="rule closed" id="php_version">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Aktualisiere PHP auf die neueste Version.", 'cpsec' ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", 'cpsec' ) ?></h3>
        <div class="line">
            <p>
				<?php _e( "Für diese Sicherheitsregel ist PHP 8.5.12 die empfohlene Mindestversion. Aus Sicherheits- und Stabilitätsgründen solltest du eine ältere PHP-Version aktualisieren.", 'cpsec' ) ?>
            </p>
            <p>
				<?php printf( esc_html__( "Mehr Informationen: %s", 'cpsec' ), '<a target="_blank" href="https://www.php.net/supported-versions.php">https://www.php.net/supported-versions.php</a>' ) ?>
            </p>
        </div>
        <div class="columns version-col">
            <div class="column">
                <strong><?php _e( "Aktuelle Version", 'cpsec' ) ?></strong>
				<?php $class = $controller->check() ? 'def-tag tag-success' : 'def-tag tag-error' ?>
                <span class="<?php echo $class ?>">
                    <?php echo \CP_Defender\Behavior\Utils::instance()->getPHPVersion() ?>
                </span>
            </div>
            <div class="column">
                <strong><?php _e( "Empfohlene Version", 'cpsec' ) ?></strong>
                <span><?php echo esc_html( $controller->getService()->getRecommendedVersion() ); ?></span>
            </div>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", 'cpsec' ) ?>
        </h3>
        <div class="well mline">
			<?php if ( $controller->check() ): ?>
				<?php _e( "Deine PHP-Version wird derzeit unterstützt.", 'cpsec' ) ?>
			<?php else: ?>
				<?php _e( "Deine PHP-Version kann von deinem Hosting-Anbieter oder Systemadministrator aktualisiert werden. Bitte kontaktiere sie für Unterstützung.", 'cpsec' ) ?>
			<?php endif; ?>
            <div class="clear"></div>
        </div>
	    <?php $controller->showIgnoreForm() ?>
        <div class="clear"></div>
    </div>
</div>