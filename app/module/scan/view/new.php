<div class="wrap">
    <div class="cp-defender">
        <div class="wdf-scanning">
            <div class="dev-box">
                <div class="box-title">
                    <h3><?php _e( "LOS GEHT'S", cp_defender()->domain ) ?></h3>
                </div>
                <div class="box-content tc">
                    <div class="line max-600">
						<?php _e( "Scanne Deine Webseite nach Dateiänderungen, Schwachstellen und eingeschleustem Code und lasse Dich über verdächtige Aktivitäten informieren. Defender behält Deinen Code im Auge, ohne dass Du Dir Sorgen machen musst.", cp_defender()->domain ) ?>
                    </div>
                    <form id="start-a-scan" method="post" class="scan-frm">
						<?php
						wp_nonce_field( 'startAScan' );
						?>
                        <input type="hidden" name="action" value="startAScan"/>
                        <button type="submit" class="button"><?php _e( "SCAN STARTEN", cp_defender()->domain ) ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>