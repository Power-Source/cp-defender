<div class="wrap">
    <h2><?php _e( "Sicherheit", 'cpsec' ) ?></h2>
    <table class="form-table">
        <tbody>
        <tr class="user-sessions-wrap hide-if-no-js">
            <th><?php _e( "Zwei-Faktor-Authentifizierung", 'cpsec' ) ?></th>
            <td aria-live="assertive">
                <div class="def-notification">
					<?php _e( "Zwei-Faktor-Authentifizierung ist aktiv.", 'cpsec' ) ?>
                </div>
                <p class="description">
					<?php
					$activeMethod = isset( $authMethod ) ? $authMethod : 'app';
					if ( $activeMethod === 'email' ) {
						echo esc_html__( 'Aktive Methode: E-Mail-Code', 'cpsec' );
					} else {
						echo esc_html__( 'Aktive Methode: Authenticator-App', 'cpsec' );
					}
					?>
                </p>
                <button type="button" class="button" id="disableOTP">
					<?php _e( "Deaktivieren", 'cpsec' ) ?>
                </button>
            </td>
        </tr>
        <tr class="user-sessions-wrap hide-if-no-js">
            <th><?php _e( "E-Mail-Adresse", 'cpsec' ) ?></th>
            <td aria-live="assertive">
                <input type="text" class="regular-text" name="def_backup_email" value="<?php echo $email ?>"/>
                <p class="description">
					<?php
					if ( isset( $authMethod ) && $authMethod === 'email' ) {
						echo esc_html__( 'An diese Adresse werden deine Anmeldecodes gesendet.', 'cpsec' );
					} else {
						echo esc_html__( 'Wenn du dein Gerät verlierst, kannst du einen Ersatz-Passcode an diese E-Mail-Adresse senden.', 'cpsec' );
					}
					?>
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