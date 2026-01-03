<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "EVENT LOGS", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content tc">
        <div class="line">
			<?php _e( "Verfolge und protokolliere jedes einzelne Ereignis, wenn Änderungen an deiner Webseite vorgenommen werden, und erhalte
detaillierte Berichte über die Vorgänge im Hintergrund, einschließlich etwaiger Hacking-Versuche auf
deine Webseite.", cp_defender()->domain ) ?>
        </div>
        <form method="post" class="audit-frm active-audit">
            <input type="hidden" name="action" value="activeAudit"/>
			<?php wp_nonce_field( 'activeAudit' ) ?>
            <button type="submit" class="button"><?php _e( "Aktivieren", cp_defender()->domain ) ?></button>
        </form>
    </div>
</div>