<div class="rule closed" id="php_version">
    <div class="rule-title">
		<?php if ( $controller->check() == false ): ?>
            <i class="def-icon icon-warning" aria-hidden="true"></i>
		<?php else: ?>
            <i class="def-icon icon-tick" aria-hidden="true"></i>
		<?php endif; ?>
		<?php _e( "Aktualisiere PHP auf die neueste Version.", cp_defender()->domain ) ?>
    </div>
    <div class="rule-content">
        <h3><?php _e( "Übersicht", cp_defender()->domain ) ?></h3>
        <div class="line">
            <p>
				<?php _e( "PHP-Versionen vor 7.1 werden nicht mehr unterstützt. Aus Sicherheits- und Stabilitätsgründen empfehlen wir dir dringend, deine PHP-Version so bald wie möglich auf Version 7.1 oder höher zu aktualisieren.", cp_defender()->domain ) ?>
            </p>
            <p>
				<?php printf( esc_html__( "Mehr Informationen: %s", cp_defender()->domain ), '<a target="_blank" href="http://php.net/supported-versions.php">http://php.net/supported-versions.php</a>' ) ?>
            </p>
        </div>
        <div class="columns version-col">
            <div class="column">
                <strong><?php _e( "Aktuelle Version", cp_defender()->domain ) ?></strong>
				<?php $class = $controller->check() ? 'def-tag tag-success' : 'def-tag tag-error' ?>
                <span class="<?php echo $class ?>">
                    <?php echo \CP_Defender\Behavior\Utils::instance()->getPHPVersion() ?>
                </span>
            </div>
            <div class="column">
                <strong><?php _e( "Empfohlene Version", cp_defender()->domain ) ?></strong>
                <span><?php echo '7.1' ?></span>
            </div>
        </div>
        <h3>
			<?php _e( "Wie man es behebt", cp_defender()->domain ) ?>
        </h3>
        <div class="well mline">
			<?php if ( $controller->check() ): ?>
				<?php _e( "Deine PHP-Version ist in Ordnung.", cp_defender()->domain ) ?>
			<?php else: ?>
				<?php _e( "Deine PHP-Version kann von deinem Hosting-Anbieter oder Systemadministrator aktualisiert werden. Bitte kontaktiere sie für Unterstützung.", cp_defender()->domain ) ?>
			<?php endif; ?>
            <div class="clear"></div>
        </div>
	    <?php $controller->showIgnoreForm() ?>
        <div class="clear"></div>
    </div>
</div>