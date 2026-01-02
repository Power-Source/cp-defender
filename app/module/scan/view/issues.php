<div class="dev-box">
    <div class="box-title">
        <h3 class="def-issues-title">
			<?php _e( "Issues", cp_defender()->domain ) ?>
			<?php $issues = $model->countAll( \CP_Defender\Module\Scan\Model\Result_Item::STATUS_ISSUE );
			if ( $issues ) {
				?>
                <span class="def-tag tag-error def-issues def-issues-summary"><?php echo $issues ?></span>
				<?php
			}
			?>
        </h3>
        <!--        <div>-->
        <!--            <span>--><?php //_e( "Type", cp_defender()->domain ) ?><!--</span>-->
        <!--            <select>-->
        <!--                <option value="all">--><?php //_e( "All", cp_defender()->domain ) ?><!--</option>-->
        <!--                <option value="core">--><?php //_e( "Core", cp_defender()->domain ) ?><!--</option>-->
        <!--                <option value="plugins">-->
		<?php //_e( "Plugins & Themes", cp_defender()->domain ) ?><!--</option>-->
        <!--                <option value="suspicious">-->
		<?php //_e( "Suspicious", cp_defender()->domain ) ?><!--</option>-->
        <!--            </select>-->
        <!--        </div>-->
    </div>
    <div class="box-content issues-box-content">
		<?php $table = new \CP_Defender\Module\Scan\Component\Result_Table();
		$table->prepare_items();
		if ( $table->get_pagination_arg( 'total_items' ) > 0 ) {
			?>
            <p class="line"><?php _e( "PS Security hat potenziell schädliche Dateien auf Deiner Webseite gefunden. In vielen Fällen erkennt der Sicherheitsscan harmlose Dateien, in manchen Fällen möchtest Du jedoch möglicherweise die unten aufgeführten, verdächtigen Dateien entfernen.", cp_defender()->domain ) ?></p>
			<?php
			$table->display();
		} else {
			?>
            <div class="well well-green with-cap">
                <i class="def-icon icon-tick" aria-hidden="true"></i>
				<?php _e( "Dein Code ist derzeit sauber! Während des letzten Scans wurden keine Probleme gefunden, aber Du kannst jederzeit einen neuen Scan durchführen.", cp_defender()->domain ) ?>
            </div>
			<?php
		}
		?>
    </div>
</div>