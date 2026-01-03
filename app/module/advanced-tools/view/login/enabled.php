<div class="wrap">
    <h2><?php _e( "Sicherheit", cp_defender()->domain ) ?></h2>
    <table class="form-table">
        <tbody>
        <tr class="user-sessions-wrap hide-if-no-js">
            <th><?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?></th>
            <td aria-live="assertive">
                <div class="def-notification">
					<?php _e( "Zwei-Faktor-Authentifizierung ist aktiv.", cp_defender()->domain ) ?>
                </div>
                <button type="button" class="button" id="disableOTP">
					<?php _e( "Deaktivieren", cp_defender()->domain ) ?>
                </button>
            </td>
        </tr>
        <tr class="user-sessions-wrap hide-if-no-js">
            <th><?php _e( "Fallback-E-Mail-Adresse", cp_defender()->domain ) ?></th>
            <td aria-live="assertive">
                <input type="text" class="regular-text" name="def_backup_email" value="<?php echo $email ?>"/>
                <p class="description">
					<?php _e( "Wenn du dein Gerät verlierst, kannst du einen Ersatz-Passcode an diese E-Mail-Adresse senden.", cp_defender()->domain ) ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $('body').on('click', '#disableOTP', function () {
            var data = {
                action: 'defDisableOTP'
            }
            var that = $(this);
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                beforeSend: function () {
                    that.attr('disabled', 'disabled');
                },
                success: function (data) {
                    if (data.success == true) {
                        location.reload();
                    } else {
                        that.removeAttr('disabled');
                    }
                }
            })
        })
    })
</script>