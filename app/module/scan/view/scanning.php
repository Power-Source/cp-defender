<div class="wrap">
    <div class="wpmud">
        <div class="cp-defender">
            <div class="wdf-scanning">
                <h2 class="title">
				    <?php _e( "File Scanning", cp_defender()->domain ) ?>
                    <span><?php echo $lastScanDate == null ? null : sprintf( __( "Letzter Scan: %s", cp_defender()->domain ), $lastScanDate ) ?>
                        <form id="start-a-scan" method="post" class="scan-frm">
						<?php
						wp_nonce_field( 'startAScan' );
						?>
                            <input type="hidden" name="action" value="startAScan"/>
                        <button type="submit"
                                class="button button-small"><?php _e( "Neuer Scan", cp_defender()->domain ) ?></button>
                </form>
                </span>
                </h2>
            </div>
        </div>
    </div>
</div>
<dialog id="scanning">
    <div class="line">
		<?php _e( "PS Security scannt Deine Dateien nach bösartigem Code. Dies dauert je nach Größe Deiner Webseite einige Minuten.", cp_defender()->domain ) ?>
    </div>
    <div class="well mline">
        <div class="scan-progress">
            <div class="scan-progress-text">
                <img aria-hidden="true" src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/loading.gif" width="18"
                     height="18"/>
                <span><?php echo $percent ?>%</span>
            </div>
            <div class="scan-progress-bar">
                <span style="width: <?php echo $percent ?>%"></span>
            </div>
        </div>
    </div>
    <p class="tc sub status-text scan-status"><?php echo $model->statusText ?></p>
    
    <!-- Erweiterte Scan-Informationen -->
    <div class="scan-details" style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 4px; text-align: left; font-size: 12px;">
        <div class="scan-current-file" style="margin-bottom: 8px;">
            <strong><?php _e( "Aktuelle Datei:", cp_defender()->domain ) ?></strong>
            <span class="current-file-name" style="color: #666; display: block; margin-top: 3px; font-family: monospace; font-size: 11px; word-break: break-all;">—</span>
        </div>
        <div class="scan-stats" style="display: flex; justify-content: space-between; gap: 15px;">
            <div>
                <strong><?php _e( "Verdächtige Funde:", cp_defender()->domain ) ?></strong>
                <span class="suspicious-count" style="color: #d63638; font-weight: bold;">0</span>
            </div>
            <div>
                <strong><?php _e( "Übersprungen:", cp_defender()->domain ) ?></strong>
                <span class="skipped-count" style="color: #999;">0</span>
            </div>
        </div>
    </div>
    
    <form method="post" id="process-scan" class="scan-frm">
        <input type="hidden" name="action" value="processScan"/>
		<?php
		wp_nonce_field( 'processScan' );
		?>
    </form>

</dialog>