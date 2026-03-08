<?php
/**
 * Anti-Spam Patterns View
 * 
 * @package CP_Defender\Module\Anti_Spam
 */

use CP_Defender\Module\Anti_Spam\Model\Pattern;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pattern löschen
if ( isset( $_POST['delete_patterns'] ) && check_admin_referer( 'defender_antispam_patterns' ) ) {
	if ( ! empty( $_POST['pattern_ids'] ) ) {
		$deleted = Pattern::delete_multiple( array_map( 'absint', $_POST['pattern_ids'] ) );
		echo '<div class="notice notice-success"><p>' . sprintf( __( '%d Pattern(s) gelöscht!', 'cpsec' ), $deleted ) . '</p></div>';
	}
}

// Pattern speichern
if ( isset( $_POST['save_pattern'] ) && check_admin_referer( 'defender_antispam_pattern_edit' ) ) {
	$pattern = array(
		'regex'  => wp_unslash( $_POST['regex'] ?? '' ),
		'desc'   => sanitize_text_field( $_POST['desc'] ?? '' ),
		'type'   => sanitize_text_field( $_POST['type'] ?? Pattern::TYPE_DOMAIN ),
		'action' => sanitize_text_field( $_POST['action'] ?? Pattern::ACTION_SPAM ),
	);
	
	$id = isset( $_POST['pattern_id'] ) ? absint( $_POST['pattern_id'] ) : null;
	
	if ( Pattern::validate( $pattern ) ) {
		$saved_id = Pattern::save( $pattern, $id );
		if ( $saved_id >= 0 ) {
			echo '<div class="notice notice-success"><p>' . __( 'Pattern gespeichert!', 'cpsec' ) . '</p></div>';
			unset( $_POST );
			unset( $_GET['id'] );
		} else {
			echo '<div class="notice notice-error"><p>' . __( 'Fehler beim Speichern des Patterns.', 'cpsec' ) . '</p></div>';
		}
	} else {
		echo '<div class="notice notice-error"><p>' . __( 'Ungültiges Pattern. Bitte prüfe den Regex-Ausdruck.', 'cpsec' ) . '</p></div>';
	}
}

$patterns = Pattern::get_all();
$editing_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : null;
$editing_pattern = $editing_id !== null ? Pattern::get( $editing_id ) : null;

if ( isset( $_POST['regex'] ) && ! empty( $_POST['regex'] ) ) {
	// Wenn Validierung fehlgeschlagen, Daten wiederherstellen
	$editing_pattern = array(
		'regex' => wp_unslash( $_POST['regex'] ),
		'desc' => sanitize_text_field( $_POST['desc'] ?? '' ),
		'type' => sanitize_text_field( $_POST['type'] ?? Pattern::TYPE_DOMAIN ),
		'action' => sanitize_text_field( $_POST['action'] ?? Pattern::ACTION_SPAM ),
	);
}
?>

<div class="wrap">
	<h1><?php _e( '🎯 Spam-Patterns', 'cpsec' ); ?></h1>
	
	<p><?php _e( 'Pattern Matching ist ein leistungsstarkes Tool zur Spam-Erkennung. Erstelle Regex-Patterns, die automatisch verdächtige Domains, Usernames, E-Mails oder Titles erkennen.', 'cpsec' ); ?></p>
	
	<form method="post" action="">
		<?php wp_nonce_field( 'defender_antispam_patterns' ); ?>
		
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<button type="submit" name="delete_patterns" class="button action" onclick="return confirm('<?php esc_attr_e( 'Möchtest du die ausgewählten Patterns wirklich löschen?', 'cpsec' ); ?>')">
					<?php _e( 'Ausgewählte löschen', 'cpsec' ); ?>
				</button>
			</div>
		</div>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" id="select-all" /></td>
					<th><?php _e( 'Pattern', 'cpsec' ); ?></th>
					<th><?php _e( 'Typ', 'cpsec' ); ?></th>
					<th><?php _e( 'Aktion', 'cpsec' ); ?></th>
					<th><?php _e( 'Matches', 'cpsec' ); ?></th>
					<th><?php _e( 'Aktionen', 'cpsec' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $patterns ) ) : ?>
					<tr>
						<td colspan="6"><?php _e( 'Noch keine Patterns vorhanden.', 'cpsec' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $patterns as $id => $pattern ) : ?>
						<tr>
							<th scope="row" class="check-column">
								<input type="checkbox" name="pattern_ids[]" value="<?php echo esc_attr( $id ); ?>" />
							</th>
							<td>
								<strong><?php echo esc_html( $pattern['regex'] ); ?></strong>
								<br><small><?php echo esc_html( $pattern['desc'] ?? '' ); ?></small>
							</td>
							<td>
								<?php
								switch ( $pattern['type'] ) {
									case Pattern::TYPE_DOMAIN: echo __( 'Domain', 'cpsec' ); break;
									case Pattern::TYPE_USERNAME: echo __( 'Username', 'cpsec' ); break;
									case Pattern::TYPE_EMAIL: echo __( 'E-Mail', 'cpsec' ); break;
									case Pattern::TYPE_TITLE: echo __( 'Titel', 'cpsec' ); break;
								}
								?>
							</td>
							<td>
								<?php echo $pattern['action'] === Pattern::ACTION_BLOCK 
									? '<span class="dashicons dashicons-shield" title="' . esc_attr__( 'Blockieren', 'cpsec' ) . '"></span> ' . __( 'Blockieren', 'cpsec' )
									: '<span class="dashicons dashicons-warning" title="' . esc_attr__( 'Als Spam markieren', 'cpsec' ) . '"></span> ' . __( 'Spam', 'cpsec' ); ?>
							</td>
							<td><?php echo number_format_i18n( $pattern['matched'] ?? 0 ); ?></td>
							<td>
								<a href="?page=cp-defender-antispam-patterns&id=<?php echo esc_attr( $id ); ?>#edit-pattern" class="button button-small">
									<?php _e( 'Bearbeiten', 'cpsec' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</form>
	
	<div id="edit-pattern" class="postbox" style="margin-top: 30px;">
		<h2 class="hndle"><?php echo $editing_pattern ? __( 'Pattern bearbeiten', 'cpsec' ) : __( 'Neues Pattern hinzufügen', 'cpsec' ); ?></h2>
		<div class="inside">
			<form method="post" action="">
				<?php wp_nonce_field( 'defender_antispam_pattern_edit' ); ?>
				<?php if ( $editing_id !== null ) : ?>
					<input type="hidden" name="pattern_id" value="<?php echo esc_attr( $editing_id ); ?>" />
				<?php endif; ?>
				
				<table class="form-table">
					<tr>
						<th scope="row"><label for="regex"><?php _e( 'Regex-Pattern', 'cpsec' ); ?>*</label></th>
						<td>
							<input type="text" id="regex" name="regex" value="<?php echo esc_attr( $editing_pattern['regex'] ?? '' ); ?>" class="large-text code" required />
							<p class="description"><?php _e( 'PCRE Regex mit Delimitern (z.B. /pattern/i)', 'cpsec' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="desc"><?php _e( 'Beschreibung', 'cpsec' ); ?></label></th>
						<td>
							<input type="text" id="desc" name="desc" value="<?php echo esc_attr( $editing_pattern['desc'] ?? '' ); ?>" class="large-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="type"><?php _e( 'Prüf-Typ', 'cpsec' ); ?>*</label></th>
						<td>
							<select id="type" name="type" required>
								<option value="<?php echo Pattern::TYPE_DOMAIN; ?>" <?php selected( $editing_pattern['type'] ?? '', Pattern::TYPE_DOMAIN ); ?>><?php _e( 'Domain', 'cpsec' ); ?></option>
								<option value="<?php echo Pattern::TYPE_USERNAME; ?>" <?php selected( $editing_pattern['type'] ?? '', Pattern::TYPE_USERNAME ); ?>><?php _e( 'Username', 'cpsec' ); ?></option>
								<option value="<?php echo Pattern::TYPE_EMAIL; ?>" <?php selected( $editing_pattern['type'] ?? '', Pattern::TYPE_EMAIL ); ?>><?php _e( 'E-Mail', 'cpsec' ); ?></option>
								<option value="<?php echo Pattern::TYPE_TITLE; ?>" <?php selected( $editing_pattern['type'] ?? '', Pattern::TYPE_TITLE ); ?>><?php _e( 'Site-Titel', 'cpsec' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="action"><?php _e( 'Aktion', 'cpsec' ); ?>*</label></th>
						<td>
							<select id="action" name="action" required>
								<option value="<?php echo Pattern::ACTION_SPAM; ?>" <?php selected( $editing_pattern['action'] ?? '', Pattern::ACTION_SPAM ); ?>><?php _e( 'Als Spam markieren (nach Signup)', 'cpsec' ); ?></option>
								<option value="<?php echo Pattern::ACTION_BLOCK; ?>" <?php selected( $editing_pattern['action'] ?? '', Pattern::ACTION_BLOCK ); ?>><?php _e( 'Signup blockieren', 'cpsec' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Pattern testen', 'cpsec' ); ?></th>
						<td>
							<button type="button" id="test-pattern" class="button"><?php _e( 'Pattern testen', 'cpsec' ); ?></button>
							<div id="test-results" style="margin-top: 10px;"></div>
						</td>
					</tr>
				</table>
				
				<?php submit_button( $editing_pattern ? __( 'Pattern aktualisieren', 'cpsec' ) : __( 'Pattern hinzufügen', 'cpsec' ), 'primary', 'save_pattern' ); ?>
			</form>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	// Select All
	$('#select-all').on('click', function() {
		$('input[name="pattern_ids[]"]').prop('checked', this.checked);
	});
	
	// Pattern testen
	$('#test-pattern').on('click', function() {
		var regex = $('#regex').val();
		var type = $('#type').val();
		var $results = $('#test-results');
		var $button = $(this);
		
		if (!regex) {
			alert('<?php esc_js( _e( 'Bitte gib ein Pattern ein.', 'cpsec' ) ); ?>');
			return;
		}
		
		$button.prop('disabled', true).text('<?php esc_js( _e( 'Teste...', 'cpsec' ) ); ?>');
		$results.html('<p><?php esc_js( _e( 'Teste Pattern...', 'cpsec' ) ); ?></p>');
		
		$.post(ajaxurl, {
			action: 'defender_antispam_test_pattern',
			nonce: '<?php echo wp_create_nonce( 'defender_antispam_nonce' ); ?>',
			regex: regex,
			type: type
		}, function(response) {
			$button.prop('disabled', false).text('<?php esc_js( _e( 'Pattern testen', 'cpsec' ) ); ?>');
			
			if (response.success) {
				var data = response.data;
				if (data.error) {
					$results.html('<div class="notice notice-error inline"><p>' + data.error + '</p></div>');
				} else if (data.total === 0) {
					$results.html('<div class="notice notice-info inline"><p><?php esc_js( _e( 'Keine Treffer in den letzten 10.000 Einträgen.', 'cpsec' ) ); ?></p></div>');
				} else {
					var html = '<div class="notice notice-success inline"><p>';
					html += '<?php esc_js( _e( 'Treffer:', 'cpsec' ) ); ?> <strong>' + data.total + '</strong> ';
					html += '<?php esc_js( _e( '(zeige', 'cpsec' ) ); ?> ' + data.shown + ')';
					html += '</p><ul style="list-style: disc; margin-left: 20px;">';
					$.each(data.matches, function(i, match) {
						html += '<li>' + $('<div>').text(match).html() + '</li>';
					});
					html += '</ul></div>';
					$results.html(html);
				}
			} else {
				$results.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
			}
		});
	});
});
</script>
