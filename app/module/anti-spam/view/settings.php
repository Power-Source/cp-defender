<?php
/**
 * Anti-Spam Settings View
 * 
 * @package CP_Defender\Module\Anti_Spam
 */

use CP_Defender\Module\Anti_Spam\Model\Settings;
use CP_Defender\Module\Anti_Spam\Behavior\Migration;
use CP_Defender\Module\Anti_Spam\Behavior\Disposable_Email;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Migration durchführen
if ( isset( $_GET['migrate'] ) && check_admin_referer( 'defender_antispam_migrate', '_wpnonce' ) ) {
	$result = Migration::migrate();
	
	if ( $result['success'] ) {
		echo '<div class="notice notice-success"><p><strong>' . __( 'Migration erfolgreich!', 'cpsec' ) . '</strong></p>';
		echo '<ul>';
		echo '<li>' . sprintf( __( '%d Blogs migriert', 'cpsec' ), $result['blogs_migrated'] ) . '</li>';
		echo '<li>' . sprintf( __( '%d Patterns migriert', 'cpsec' ), $result['patterns_migrated'] ) . '</li>';
		if ( $result['settings_migrated'] ) {
			echo '<li>' . __( 'Einstellungen migriert', 'cpsec' ) . '</li>';
		}
		echo '</ul></div>';
	} else {
		echo '<div class="notice notice-error"><p><strong>' . __( 'Migration teilweise fehlgeschlagen:', 'cpsec' ) . '</strong></p>';
		echo '<ul>';
		foreach ( $result['errors'] as $error ) {
			echo '<li>' . esc_html( $error ) . '</li>';
		}
		echo '</ul></div>';
	}
}

/**
 * GET-basiertes Update wurde entfernt, alles läuft jetzt via AJAX
 */

// Speichere Einstellungen
if ( isset( $_POST['defender_antispam_save'] ) && check_admin_referer( 'defender_antispam_settings' ) ) {
	$settings = array(
		'ip_blocking_enabled'     => isset( $_POST['ip_blocking_enabled'] ),
		'ip_blocking_threshold'   => absint( $_POST['ip_blocking_threshold'] ?? 1 ),
		'rate_limit_enabled'      => isset( $_POST['rate_limit_enabled'] ),
		'rate_limit_count'        => absint( $_POST['rate_limit_count'] ?? 3 ),
		'rate_limit_period'       => absint( $_POST['rate_limit_period'] ?? 24 ),
		'auto_spam_enabled'       => isset( $_POST['auto_spam_enabled'] ),
		'auto_spam_certainty'     => absint( $_POST['auto_spam_certainty'] ?? 80 ),
		'patterns_enabled'        => isset( $_POST['patterns_enabled'] ),
		'human_verification'      => sanitize_text_field( $_POST['human_verification'] ?? 'turnstile' ),
		'turnstile_site_key'      => sanitize_text_field( $_POST['turnstile_site_key'] ?? '' ),
		'turnstile_secret_key'    => sanitize_text_field( $_POST['turnstile_secret_key'] ?? '' ),
		'recaptcha_site_key'      => sanitize_text_field( $_POST['recaptcha_site_key'] ?? '' ),
		'recaptcha_secret_key'    => sanitize_text_field( $_POST['recaptcha_secret_key'] ?? '' ),
		'honeypot_enabled'        => isset( $_POST['honeypot_enabled'] ),
		'disposable_email_check_enabled' => isset( $_POST['disposable_email_check_enabled'] ),
		'disposable_domains_auto_update' => isset( $_POST['disposable_domains_auto_update'] ),
		'post_monitoring_enabled' => isset( $_POST['post_monitoring_enabled'] ),
		'post_spam_certainty'     => absint( $_POST['post_spam_certainty'] ?? 90 ),
		'notify_on_spam'          => isset( $_POST['notify_on_spam'] ),
		'notify_email'            => sanitize_email( $_POST['notify_email'] ?? '' ),
		'spam_user_on_blog_spam'  => isset( $_POST['spam_user_on_blog_spam'] ),
		'show_toolbar_menu'       => isset( $_POST['show_toolbar_menu'] ),
	);
	
	// Q&A Questions verarbeiten
	if ( ! empty( $_POST['qa_questions'] ) && ! empty( $_POST['qa_answers'] ) ) {
		$questions = array_map( 'sanitize_text_field', explode( "\n", $_POST['qa_questions'] ) );
		$answers = array_map( 'sanitize_text_field', explode( "\n", $_POST['qa_answers'] ) );
		$qa_array = array();
		
		foreach ( $questions as $i => $question ) {
			if ( ! empty( trim( $question ) ) && ! empty( trim( $answers[ $i ] ?? '' ) ) ) {
				$qa_array[] = array(
					'question' => trim( $question ),
					'answer' => trim( $answers[ $i ] )
				);
			}
		}
		
		$settings['qa_questions'] = $qa_array;
	}
	
	Settings::save( $settings );
	
	echo '<div class="notice notice-success"><p>' . __( 'Einstellungen gespeichert!', 'cpsec' ) . '</p></div>';
}

$settings = Settings::get_all();
?>

<div class="wrap">
	<h1><?php _e( '🛡️ Anti-Spam Einstellungen', 'cpsec' ); ?></h1>
	
	<form method="post" action="">
		<?php wp_nonce_field( 'defender_antispam_settings' ); ?>
		
		<table class="form-table">
			<tr>
				<th colspan="2"><h2><?php _e( 'IP-Blocking', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'IP-Blocking aktivieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="ip_blocking_enabled" value="1" <?php checked( $settings['ip_blocking_enabled'] ); ?> />
						<?php _e( 'Blockiere IPs nach wiederholten Spam-Registrierungen', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Spam-Schwellwert', 'cpsec' ); ?></th>
				<td>
					<input type="number" name="ip_blocking_threshold" value="<?php echo esc_attr( $settings['ip_blocking_threshold'] ); ?>" min="1" max="10" />
					<p class="description"><?php _e( 'Blockiere IP nach X Spam-Blogs', 'cpsec' ); ?></p>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Rate Limiting', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Rate Limiting aktivieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="rate_limit_enabled" value="1" <?php checked( $settings['rate_limit_enabled'] ); ?> />
						<?php _e( 'Begrenze Registrierungen pro IP', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Maximale Registrierungen', 'cpsec' ); ?></th>
				<td>
					<input type="number" name="rate_limit_count" value="<?php echo esc_attr( $settings['rate_limit_count'] ); ?>" min="1" max="20" />
					<?php _e( 'Registrierungen innerhalb', 'cpsec' ); ?>
					<input type="number" name="rate_limit_period" value="<?php echo esc_attr( $settings['rate_limit_period'] ); ?>" min="1" max="168" />
					<?php _e( 'Stunden', 'cpsec' ); ?>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Auto-Spam', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Auto-Spam aktivieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="auto_spam_enabled" value="1" <?php checked( $settings['auto_spam_enabled'] ); ?> />
						<?php _e( 'Markiere verdächtige Blogs automatisch als Spam', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Certainty-Schwellwert', 'cpsec' ); ?></th>
				<td>
					<input type="number" name="auto_spam_certainty" value="<?php echo esc_attr( $settings['auto_spam_certainty'] ); ?>" min="50" max="100" />%
					<p class="description"><?php _e( 'Blogs mit diesem oder höherem Certainty-Wert werden automatisch als Spam markiert', 'cpsec' ); ?></p>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Pattern Matching', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Pattern Matching aktivieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="patterns_enabled" value="1" <?php checked( $settings['patterns_enabled'] ); ?> />
						<?php _e( 'Nutze Regex-Patterns zur Spam-Erkennung', 'cpsec' ); ?>
					</label>
					<p class="description">
						<a href="<?php echo network_admin_url( 'admin.php?page=cp-defender-antispam-patterns' ); ?>">
							<?php _e( 'Patterns verwalten →', 'cpsec' ); ?>
						</a>
					</p>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Human Verification', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Verifizierungstyp', 'cpsec' ); ?></th>
				<td>
					<select name="human_verification" id="human_verification">
						<option value="none" <?php selected( $settings['human_verification'], 'none' ); ?>><?php _e( 'Keine', 'cpsec' ); ?></option>
						<option value="turnstile" <?php selected( $settings['human_verification'], 'turnstile' ); ?>>Cloudflare Turnstile</option>
						<option value="recaptcha" <?php selected( $settings['human_verification'], 'recaptcha' ); ?>>reCAPTCHA (Legacy)</option>
						<option value="questions" <?php selected( $settings['human_verification'], 'questions' ); ?>><?php _e( 'Sicherheitsfragen', 'cpsec' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Empfohlen: Cloudflare Turnstile. reCAPTCHA bleibt als Legacy-Option verfügbar.', 'cpsec' ); ?></p>
				</td>
			</tr>
			<tr class="turnstile-settings" style="display: <?php echo $settings['human_verification'] === 'turnstile' ? 'table-row' : 'none'; ?>;">
				<th scope="row">Turnstile Site Key</th>
				<td>
					<input type="text" name="turnstile_site_key" value="<?php echo esc_attr( $settings['turnstile_site_key'] ); ?>" class="regular-text" />
					<p class="description"><a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank"><?php _e( 'Turnstile Keys in Cloudflare erstellen', 'cpsec' ); ?></a></p>
				</td>
			</tr>
			<tr class="turnstile-settings" style="display: <?php echo $settings['human_verification'] === 'turnstile' ? 'table-row' : 'none'; ?>;">
				<th scope="row">Turnstile Secret Key</th>
				<td>
					<input type="text" name="turnstile_secret_key" value="<?php echo esc_attr( $settings['turnstile_secret_key'] ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr class="recaptcha-settings" style="display: <?php echo $settings['human_verification'] === 'recaptcha' ? 'table-row' : 'none'; ?>;">
				<th scope="row">reCAPTCHA Site Key</th>
				<td>
					<input type="text" name="recaptcha_site_key" value="<?php echo esc_attr( $settings['recaptcha_site_key'] ); ?>" class="regular-text" />
					<p class="description"><a href="https://www.google.com/recaptcha/admin" target="_blank"><?php _e( 'reCAPTCHA Keys erhalten', 'cpsec' ); ?></a></p>
				</td>
			</tr>
			<tr class="recaptcha-settings" style="display: <?php echo $settings['human_verification'] === 'recaptcha' ? 'table-row' : 'none'; ?>;">
				<th scope="row">reCAPTCHA Secret Key</th>
				<td>
					<input type="text" name="recaptcha_secret_key" value="<?php echo esc_attr( $settings['recaptcha_secret_key'] ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr class="qa-settings" style="display: <?php echo $settings['human_verification'] === 'questions' ? 'table-row' : 'none'; ?>;">
				<th scope="row"><?php _e( 'Fragen & Antworten', 'cpsec' ); ?></th>
				<td>
					<?php
					$questions_text = '';
					$answers_text = '';
					if ( ! empty( $settings['qa_questions'] ) ) {
						foreach ( $settings['qa_questions'] as $qa ) {
							$questions_text .= $qa['question'] . "\n";
							$answers_text .= $qa['answer'] . "\n";
						}
					}
					?>
					<p><?php _e( 'Fragen (eine pro Zeile):', 'cpsec' ); ?></p>
					<textarea name="qa_questions" rows="5" class="large-text"><?php echo esc_textarea( trim( $questions_text ) ); ?></textarea>
					<p><?php _e( 'Antworten (eine pro Zeile, in gleicher Reihenfolge):', 'cpsec' ); ?></p>
					<textarea name="qa_answers" rows="5" class="large-text"><?php echo esc_textarea( trim( $answers_text ) ); ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Honeypot aktivieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="honeypot_enabled" value="1" <?php checked( $settings['honeypot_enabled'] ); ?> />
						<?php _e( 'Verstecktes Feld im Signup-Formular. Wenn ausgefüllt, wird die Registrierung als Bot blockiert.', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Disposable Email Protection', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Wegwerf-E-Mails blockieren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="disposable_email_check_enabled" value="1" <?php checked( $settings['disposable_email_check_enabled'] ); ?> />
						<?php _e( 'Blockiere Registrierungen mit bekannten Wegwerf-E-Mail-Adressen (z.B. 10minutemail, Guerrilla Mail, Mailinator)', 'cpsec' ); ?>
					</label>
					<?php
					$info = Disposable_Email::get_list_info();
					$last_update = $info['last_update'];
					?>
					<p class="description">
						<strong><?php _e( 'Domain-Liste:', 'cpsec' ); ?></strong> 
						<?php 
						if ( $info['file_exists'] ) {
							echo sprintf( 
								__( '%s Domains | Letzte Aktualisierung: %s', 'cpsec' ),
								number_format_i18n( $info['count'] ),
								$last_update > 0 ? wp_date( 'j. F Y, H:i', $last_update ) : __( 'Nie', 'cpsec' )
							);
						} else {
							echo '<span style="color:#d63638;">' . __( 'Keine Domain-Liste gefunden', 'cpsec' ) . '</span>';
						}
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Automatische Updates', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="disposable_domains_auto_update" value="1" <?php checked( $settings['disposable_domains_auto_update'] ); ?> />
						<?php _e( 'Domain-Liste wöchentlich automatisch aktualisieren (via WP-Cron)', 'cpsec' ); ?>
					</label>
					<p class="description">
						<?php _e( 'Hält die Liste mit neuen Wegwerf-Domains auf dem aktuellen Stand.', 'cpsec' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Manuelle Aktualisierung', 'cpsec' ); ?></th>
				<td>
					<button type="button" id="btn-update-disposable-domains" class="button button-secondary">
						<?php _e( 'Domain-Liste jetzt aktualisieren', 'cpsec' ); ?>
					</button>
					<p class="description">
						<?php _e( 'Lädt die neueste Liste von GitHub herunter. Quelle: disposable-email-domains/disposable-email-domains', 'cpsec' ); ?>
					</p>
					<div id="disposable-update-result" style="margin-top: 10px; display: none;"></div>
				</td>
			</tr>
			
			<tr>
				<th colspan="2"><h2><?php _e( 'Weitere Optionen', 'cpsec' ); ?></h2></th>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Benutzer mitsperren', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="spam_user_on_blog_spam" value="1" <?php checked( $settings['spam_user_on_blog_spam'] ); ?> />
						<?php _e( 'Markiere auch die Benutzer eines Spam-Blogs als Spam', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Toolbar-Menü anzeigen', 'cpsec' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="show_toolbar_menu" value="1" <?php checked( $settings['show_toolbar_menu'] ); ?> />
						<?php _e( 'Zeige Anti-Spam-Menü in der Admin-Toolbar', 'cpsec' ); ?>
					</label>
				</td>
			</tr>
		</table>
		
		<?php submit_button( __( 'Einstellungen speichern', 'cpsec' ), 'primary', 'defender_antispam_save' ); ?>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	function toggleVerificationSettings() {
		var val = $('#human_verification').val();
		$('.turnstile-settings').toggle(val === 'turnstile');
		$('.recaptcha-settings').toggle(val === 'recaptcha');
		$('.qa-settings').toggle(val === 'questions');
	}

	$('#human_verification').on('change', function() {
		toggleVerificationSettings();
	});

	toggleVerificationSettings();

	// Disposable Domain List Update
	$('#btn-update-disposable-domains').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var $result = $('#disposable-update-result');
		
		$btn.prop('disabled', true);
		$btn.text('<?php _e( 'Wird aktualisiert...', 'cpsec' ); ?>');
		$result.hide();
		
		$.ajax({
			url: defenderAntiSpam.ajaxUrl,
			type: 'POST',
			data: {
				action: 'defender_update_disposable_domains',
				nonce: defenderAntiSpam.nonce
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					$result.html(
						'<div class="notice notice-success" style="padding: 12px; margin: 0;">' +
						'<p><strong>' + response.data.message + '</strong></p>' +
						'</div>'
					).show();
				} else {
					$result.html(
						'<div class="notice notice-error" style="padding: 12px; margin: 0;">' +
						'<p><strong><?php _e( 'Update fehlgeschlagen:', 'cpsec' ); ?></strong> ' + response.data.message + '</p>' +
						'</div>'
					).show();
				}
			},
			error: function() {
				$result.html(
					'<div class="notice notice-error" style="padding: 12px; margin: 0;">' +
					'<p><strong><?php _e( 'Fehler:', 'cpsec' ); ?></strong> <?php _e( 'AJAX-Request fehlgeschlagen', 'cpsec' ); ?></p>' +
					'</div>'
				).show();
			},
			complete: function() {
				$btn.prop('disabled', false);
				$btn.text('<?php _e( 'Domain-Liste jetzt aktualisieren', 'cpsec' ); ?>');
			}
		});
	});
});
</script>
