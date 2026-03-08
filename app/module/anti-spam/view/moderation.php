<?php
/**
 * Anti-Spam Moderation View
 * Übersicht aller Blogs mit Spam-Status
 * 
 * @package CP_Defender\Module\Anti_Spam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

// Bulk-Aktionen
if ( isset( $_POST['bulk_action'] ) && check_admin_referer( 'defender_antispam_moderation' ) ) {
	$blog_ids = array_map( 'absint', $_POST['blog_ids'] ?? array() );
	$action = sanitize_text_field( $_POST['bulk_action_type'] ?? '' );
	$count = 0;
	
	foreach ( $blog_ids as $blog_id ) {
		if ( $blog_id <= 1 ) continue; // Hauptseite nicht anfassen
		
		switch ( $action ) {
			case 'spam':
				update_blog_status( $blog_id, 'spam', 1 );
				$count++;
				break;
			case 'unspam':
				update_blog_status( $blog_id, 'spam', 0 );
				$count++;
				break;
			case 'delete':
				wpmu_delete_blog( $blog_id, true );
				$count++;
				break;
			case 'ignore':
				$wpdb->update(
					$wpdb->base_prefix . 'defender_antispam_blogs',
					array( 'is_ignored' => 1 ),
					array( 'blog_id' => $blog_id )
				);
				$count++;
				break;
		}
	}
	
	if ( $count > 0 ) {
		echo '<div class="notice notice-success"><p>' . sprintf( __( '%d Blog(s) aktualisiert!', 'cpsec' ), $count ) . '</p></div>';
	}
}

// Filter
$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'suspicious';
$per_page = 20;
$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$offset = ( $paged - 1 ) * $per_page;

$table = $wpdb->base_prefix . 'defender_antispam_blogs';

// Query erstellen
$where = array();
$join = "INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id";

switch ( $status_filter ) {
	case 'spam':
		$where[] = "b.spam = 1";
		break;
	case 'suspicious':
		$where[] = "a.certainty > 50";
		$where[] = "a.is_ignored = 0";
		$where[] = "b.spam = 0";
		break;
	case 'ignored':
		$where[] = "a.is_ignored = 1";
		break;
	case 'all':
		$where[] = "b.blog_id > 1"; // Nicht Hauptseite
		break;
}

$where_sql = implode( ' AND ', $where );

$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} a {$join} WHERE {$where_sql}" );
$total_pages = ceil( $total / $per_page );

$results = $wpdb->get_results( 
	$wpdb->prepare(
		"SELECT a.*, b.domain, b.path, b.registered, b.spam, b.deleted 
		FROM {$table} a 
		{$join}
		WHERE {$where_sql}
		ORDER BY a.certainty DESC, a.signup_date DESC
		LIMIT %d OFFSET %d",
		$per_page,
		$offset
	)
);

// Statistiken
$stats = array(
	'total' => get_blog_count(),
	'spam' => $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->blogs} WHERE spam = 1" ),
	'suspicious' => $wpdb->get_var( "SELECT COUNT(*) FROM {$table} a INNER JOIN {$wpdb->blogs} b ON a.blog_id = b.blog_id WHERE a.certainty > 50 AND a.is_ignored = 0 AND b.spam = 0" ),
	'ignored' => $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE is_ignored = 1" ),
);
?>

<div class="wrap">
	<h1><?php _e( '🛡️ Anti-Spam Moderation', 'cpsec' ); ?></h1>
	
	<div class="defender-antispam-stats" style="background: #fff; padding: 15px; border: 1px solid #ccd0d4; margin-bottom: 20px;">
		<h3 style="margin-top: 0;"><?php _e( 'Übersicht', 'cpsec' ); ?></h3>
		<div style="display: flex; gap: 30px;">
			<div>
				<strong><?php _e( 'Gesamt:', 'cpsec' ); ?></strong> 
				<?php echo number_format_i18n( $stats['total'] ); ?>
			</div>
			<div>
				<strong><?php _e( 'Spam:', 'cpsec' ); ?></strong> 
				<span style="color: #d63638;"><?php echo number_format_i18n( $stats['spam'] ); ?></span>
			</div>
			<div>
				<strong><?php _e( 'Verdächtig:', 'cpsec' ); ?></strong> 
				<span style="color: #dba617;"><?php echo number_format_i18n( $stats['suspicious'] ); ?></span>
			</div>
			<div>
				<strong><?php _e( 'Ignoriert:', 'cpsec' ); ?></strong> 
				<?php echo number_format_i18n( $stats['ignored'] ); ?>
			</div>
		</div>
	</div>
	
	<ul class="subsubsub">
		<li><a href="?page=cp-defender-antispam&status=all" class="<?php echo $status_filter === 'all' ? 'current' : ''; ?>"><?php _e( 'Alle', 'cpsec' ); ?></a> |</li>
		<li><a href="?page=cp-defender-antispam&status=suspicious" class="<?php echo $status_filter === 'suspicious' ? 'current' : ''; ?>"><?php _e( 'Verdächtig', 'cpsec' ); ?> <span class="count">(<?php echo $stats['suspicious']; ?>)</span></a> |</li>
		<li><a href="?page=cp-defender-antispam&status=spam" class="<?php echo $status_filter === 'spam' ? 'current' : ''; ?>"><?php _e( 'Spam', 'cpsec' ); ?> <span class="count">(<?php echo $stats['spam']; ?>)</span></a> |</li>
		<li><a href="?page=cp-defender-antispam&status=ignored" class="<?php echo $status_filter === 'ignored' ? 'current' : ''; ?>"><?php _e( 'Ignoriert', 'cpsec' ); ?> <span class="count">(<?php echo $stats['ignored']; ?>)</span></a></li>
	</ul>
	
	<form method="post" action="">
		<?php wp_nonce_field( 'defender_antispam_moderation' ); ?>
		
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="bulk_action_type">
					<option value=""><?php _e( 'Bulk-Aktionen', 'cpsec' ); ?></option>
					<option value="spam"><?php _e( 'Als Spam markieren', 'cpsec' ); ?></option>
					<option value="unspam"><?php _e( 'Kein Spam', 'cpsec' ); ?></option>
					<option value="ignore"><?php _e( 'Ignorieren', 'cpsec' ); ?></option>
					<option value="delete"><?php _e( 'Löschen', 'cpsec' ); ?></option>
				</select>
				<button type="submit" name="bulk_action" class="button action"><?php _e( 'Anwenden', 'cpsec' ); ?></button>
			</div>
			
			<?php if ( $total_pages > 1 ) : ?>
				<div class="tablenav-pages">
					<span class="displaying-num"><?php printf( __( '%s Einträge', 'cpsec' ), number_format_i18n( $total ) ); ?></span>
					<?php
					echo paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'current' => $paged,
						'total' => $total_pages,
					) );
					?>
				</div>
			<?php endif; ?>
		</div>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" id="select-all-blogs" /></td>
					<th><?php _e( 'Blog', 'cpsec' ); ?></th>
					<th><?php _e( 'Certainty', 'cpsec' ); ?></th>
					<th><?php _e( 'IP', 'cpsec' ); ?></th>
					<th><?php _e( 'Registriert', 'cpsec' ); ?></th>
					<th><?php _e( 'Status', 'cpsec' ); ?></th>
					<th><?php _e( 'Aktionen', 'cpsec' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $results ) ) : ?>
					<tr><td colspan="7"><?php _e( 'Keine Blogs gefunden.', 'cpsec' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $results as $row ) : ?>
						<?php 
						$blog_details = get_blog_details( $row->blog_id );
						$url = $blog_details ? $blog_details->siteurl : '';
						$name = $blog_details ? $blog_details->blogname : __( 'Unbekannt', 'cpsec' );
						
						$certainty_class = '';
						if ( $row->certainty >= 80 ) {
							$certainty_class = 'error';
						} elseif ( $row->certainty >= 50 ) {
							$certainty_class = 'warning';
						}
						?>
						<tr class="<?php echo $row->spam ? 'spam-blog' : ''; ?>">
							<th scope="row" class="check-column">
								<input type="checkbox" name="blog_ids[]" value="<?php echo esc_attr( $row->blog_id ); ?>" />
							</th>
							<td>
								<strong><a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $name ); ?></a></strong>
								<br><small><?php echo esc_html( $row->domain . $row->path ); ?></small>
							</td>
							<td>
								<span class="<?php echo esc_attr( $certainty_class ); ?>">
									<?php echo absint( $row->certainty ); ?>%
								</span>
								<?php if ( $row->pattern_matched ) : ?>
									<br><small>🎯 <?php echo esc_html( $row->pattern_matched ); ?></small>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $row->last_ip ) : ?>
									<?php echo esc_html( $row->last_ip ); ?>
								<?php endif; ?>
							</td>
							<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $row->registered ) ); ?></td>
							<td>
								<?php if ( $row->spam ) : ?>
									<span style="color: #d63638;">⛔ <?php _e( 'Spam', 'cpsec' ); ?></span>
								<?php elseif ( $row->is_ignored ) : ?>
									<span style="color: #999;">👁️ <?php _e( 'Ignoriert', 'cpsec' ); ?></span>
								<?php elseif ( $row->certainty > 50 ) : ?>
									<span style="color: #dba617;">⚠️ <?php _e( 'Verdächtig', 'cpsec' ); ?></span>
								<?php else : ?>
									<span style="color: #46b450;">✅ <?php _e( 'OK', 'cpsec' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $row->spam ) : ?>
									<a href="<?php echo network_admin_url( 'sites.php?action=confirm&action2=notspam&id=' . $row->blog_id ); ?>" class="button button-small">
										<?php _e( 'Kein Spam', 'cpsec' ); ?>
									</a>
								<?php else : ?>
									<a href="<?php echo network_admin_url( 'sites.php?action=confirm&action2=spam&id=' . $row->blog_id ); ?>" class="button button-small">
										<?php _e( 'Spam', 'cpsec' ); ?>
									</a>
								<?php endif; ?>
								<a href="<?php echo network_admin_url( 'site-info.php?id=' . $row->blog_id ); ?>" class="button button-small" target="_blank">
									<?php _e( 'Details', 'cpsec' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div class="tablenav bottom">
			<?php if ( $total_pages > 1 ) : ?>
				<div class="tablenav-pages">
					<?php
					echo paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'current' => $paged,
						'total' => $total_pages,
					) );
					?>
				</div>
			<?php endif; ?>
		</div>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	$('#select-all-blogs').on('click', function() {
		$('input[name="blog_ids[]"]').prop('checked', this.checked);
	});
});
</script>

<style>
.spam-blog { opacity: 0.6; background: #fff5f5; }
.error { color: #d63638; font-weight: bold; }
.warning { color: #dba617; font-weight: bold; }
</style>
