<div class="wrap">
    <h2><?php _e( "Sicherheit", cp_defender()->domain ) ?></h2>
    <table class="form-table">
        <tbody>
        <tr class="user-sessions-wrap hide-if-no-js">
            <th><?php _e( "Zwei-Faktor-Authentifizierung", cp_defender()->domain ) ?></th>
            <td aria-live="assertive">
                <div id="def2">
                    <div class="destroy-sessions">
                        <button type="button" class="button" id="show2AuthActivator">
							<?php _e( "Aktivieren", cp_defender()->domain ) ?>
                        </button>
                    </div>
                    <p class="description">
						<?php _e( "Verwende die Google Authenticator App, um dich mit einem separaten Passcode anzumelden.", cp_defender()->domain ) ?>
                    </p>
                </div>
                <div id="def2qr">
                    <button type="button" id="hide2AuthActivator"
                            class="button"><?php _e( "Abbrechen", cp_defender()->domain ) ?></button>
                    <p><?php _e( "Verwende die Google Authenticator App, um dich mit einem separaten Passcode anzumelden.", cp_defender()->domain ) ?></p>
                    <div class="card">
                        <p>
                            <strong><?php _e( "1. Installiere die Verifizierungs-App", cp_defender()->domain ) ?></strong>
                        </p>
                        <p>
							<?php _e( "Lade die Google Authenticator App auf dein Gerät herunter und installiere sie über die untenstehenden Links.", cp_defender()->domain ) ?>
                        </p>
                        <a href="https://itunes.apple.com/vn/app/google-authenticator/id388497605?mt=8">
                            <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/ios-download.svg' ?>"/>
                        </a>
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">
                            <img src="<?php echo cp_defender()->getPluginUrl() . 'assets/img/android-download.svg' ?>"/>
                        </a>
                        <div class="line"></div>
                        <p><strong><?php _e( "2. Scanne den Barcode", cp_defender()->domain ) ?></strong></p>
                        <p><?php _e( "Öffne die Google Authenticator App, die du gerade heruntergeladen hast, tippe auf das „+“-Symbol und verwende dann die Kamera deines Telefons, um den untenstehenden Barcode zu scannen.", cp_defender()->domain ) ?></p>
                        <img class="barcode"
                             src="<?php echo \CP_Defender\Module\Advanced_Tools\Component\Auth_API::generateQRCode( get_site_url(), $secretKey, 149, 149, 'cp-defender' ) ?>"/>
                        <div class="line"></div>
                        <p><strong><?php _e( "3. Gib den Passcode ein", cp_defender()->domain ) ?></strong></p>
                        <p>
							<?php _e( "Gib den 6-stelligen Passcode, der auf deinem Gerät angezeigt wird, in das untenstehende Eingabefeld ein und klicke auf „Verifizieren“.", cp_defender()->domain ) ?>
                        </p>
                        <div class="well">
                            <p class="error"></p>
                            <input type="text" id="otpCode" class="def-small-text">
                            <button type="button" class="button button-primary" id="verifyOTP">
								<?php _e( "Verifizieren", cp_defender()->domain ) ?>
                            </button>
                            <input type="hidden" id="defNonce" value="<?php echo wp_create_nonce( 'defVerifyOTP' ) ?>"/>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $('#def2qr').hide();
        $('body').on('click', '#show2AuthActivator', function () {
            $('#def2').hide();
            $('#def2qr').show();
        });
        $('body').on('click', '#hide2AuthActivator', function () {
            $('#def2qr').hide();
            $('#def2').show();
        })
        $('body').on('click', '#verifyOTP', function () {
            var data = {
                action: 'defVerifyOTP',
                otp: $('#otpCode').val(),
                nonce: $('#defNonce').val()
            }
            var that = $(this);
            var parent = that.closest('.well');
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
                        parent.find('.error').text(data.data.message);
                    }
                }
            })
        })
    })
</script>