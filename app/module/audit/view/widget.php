<div class="dev-box">
    <div class="box-title">
        <span class="span-icon icon-audit"></span>
        <h3><?php _e( "AUDIT LOGGING", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
        <div class="line end">
			<?php printf( __( "In den letzten 24 Stunden wurden <strong>%d Ereignisse</strong> protokolliert.", cp_defender()->domain ), $eventDay ) ?>
        </div>
        <ul class="dev-list bold end">
            <li>
                <div>
                    <span class="list-label"><?php _e( "Letztes protokolliertes Ereignis", cp_defender()->domain ) ?></span>
                    <span class="list-detail"><?php echo $lastEvent ?></span>
                </div>
            </li>
            <li>
                <div>
                    <span class="list-label"><?php _e( "In diesem Monat protokollierte Ereignisse", cp_defender()->domain ) ?></span>
                    <span class="list-detail"><?php echo $eventMonth ?></span>
                </div>
            </li>
        </ul>
        <div class="row">
            <div class="col-third tl">
                <a href="<?php echo \CP_Defender\Behavior\Utils::instance()->getAdminPageUrl( 'wdf-logging' ) ?>"
                   class="button button-small button-secondary"><?php _e( "VIEW LOGS", cp_defender()->domain ) ?></a>
            </div>
            <div class="col-two-third tr">
                <p class="status-text"><?php
					if ( \CP_Defender\Module\Audit\Model\Settings::instance()->notification ) {
						_e( "Audit-Protokollberichte sind aktiviert", cp_defender()->domain );
					} else {
						_e( "Audit-Protokollberichte sind deaktiviert", cp_defender()->domain );
					}
					?></p>
            </div>
        </div>
    </div>
</div>