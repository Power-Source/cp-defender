<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Ignoriert", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
		<?php $table = new \CP_Defender\Module\Scan\Component\Result_Table();
		$table->type = \CP_Defender\Module\Scan\Model\Result_Item::STATUS_IGNORED;
		$table->prepare_items();
		if ( $table->get_pagination_arg( 'total_items' ) ) {
			?>
            <p class="line"><?php _e( "Hier ist eine Liste der verdächtigen Dateien, die Du zum Ignorieren ausgewählt hast.", cp_defender()->domain ) ?></p>
			<?php
			$table->display();
		} else {
			?>
            <div class="well well-blue with-cap">
                <i class="def-icon icon-warning"  aria-hidden="true"></i>
				<?php _e( "Du hast noch keine verdächtigen Dateien zum Ignorieren ausgewählt. Ignorierte Dateien erscheinen hier und können jederzeit wiederhergestellt werden.", cp_defender()->domain ) ?>
            </div>
			<?php
		}
		?>
    </div>
</div>