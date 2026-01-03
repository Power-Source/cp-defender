<dialog id="activator">
    <div class="activate-picker">
        <div class="line end">
			<?php _e( "Willkommen bei PS Security! Wir richten schnell die wichtigsten Sicherheitsfunktionen ein, die Du später individuell anpassen kannst. Unsere Empfehlungen sind standardmäßig aktiviert.", cp_defender()->domain ) ?>
        </div>
        <form method="post">
            <input type="hidden" value="activateModule" name="action"/>
			<?php wp_nonce_field( 'activateModule' ) ?>
            <div class="columns">
                <div class="column is-10">
                    <strong><?php _e( "Dateiscannen", cp_defender()->domain ); ?></strong>
                    <p class="sub">
						<?php _e( "Scanne deine Webseite nach Dateiänderungen, Schwachstellen und eingeschleustem Code und erhalte Benachrichtigungen über verdächtige Aktivitäten.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <div class="column is-2">
               <span class="toggle float-r">
                    <input type="checkbox"
                           name="activator[]" checked
                           class="toggle-checkbox" id="active_scan"
                           value="activate_scan"/>
                    <label class="toggle-label" for="active_scan"></label>
                </span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-10">
                    <strong><?php _e( "Audit-Protokollierung", cp_defender()->domain ) ?></strong>
                    <p class="sub">
						<?php _e( "Verfolge und protokolliere Ereignisse, wenn Änderungen an deiner Webseite vorgenommen werden, und erhalte vollständige Transparenz darüber, was hinter den Kulissen passiert.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <div class="column is-2">
               <span class="toggle float-r">
                    <input type="checkbox"
                           name="activator[]" checked
                           class="toggle-checkbox" id="active_audit" value="activate_audit"/>
                    <label class="toggle-label" for="active_audit"></label>
                </span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-10">
                    <strong><?php _e( "IP-Sperren", cp_defender()->domain ) ?></strong>
                    <p class="sub">
						<?php _e( "Schütze deinen Login-Bereich und erlaube PS Security, automatisch verdächtiges Verhalten zu sperren.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <div class="column is-2">
               <span class="toggle float-r">
                    <input type="checkbox" checked
                           name="activator[]" class="toggle-checkbox" id="activate_lockout" value="activate_lockout"/>
                    <label class="toggle-label" for="activate_lockout"></label>
                </span>
                </div>
            </div>
            <div class="columns last">
                <div class="column is-9">
                    <p class="sub">
						<?php _e( "Diese Dienste werden mit empfohlenen Einstellungen konfiguriert. Du kannst diese jederzeit ändern.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <div class="column is-3 tr">
                    <button type="submit" class="button"><?php _e( "Loslegen", cp_defender()->domain ) ?></button>
                </div>
            </div>
        </form>
    </div>
    <div class="activate-progress wd-hide">
        <div class="line">
	        <?php _e( "Einen Moment bitte, während PS Security diese Dienste für dich aktiviert...", cp_defender()->domain ) ?>
        </div>
        <div class="well mline">
            <div class="scan-progress">
                <div class="scan-progress-text">
                    <img src="<?php echo cp_defender()->getPluginUrl() ?>assets/img/loading.gif" width="18"
                         height="18"/>
                    <span>0%</span>
                </div>
                <div class="scan-progress-bar">
                    <span style="width: 0%"></span>
                </div>
            </div>
        </div>
        <p class="tc sub status-text"></p>
    </div>
</dialog>
<script type="text/javascript">
    jQuery(function ($) {
        //hack to fix the dialog toggle
        setTimeout(function () {
            $('.wd-activator label').each(function () {
                var parent = $(this).closest('div');
                var input = parent.find('#' + $(this).attr('for'));
                $(this).on('click', function () {
                    if (input.prop('checked') == false) {
                        input.prop('checked', true);
                    } else {
                        input.prop('checked', false);
                    }
                })
            })
        }, 500)
    })
</script>