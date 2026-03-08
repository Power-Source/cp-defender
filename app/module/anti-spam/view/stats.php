<?php
/**
 * Anti-Spam Statistics View
 * 
 * @package CP_Defender\Module\Anti_Spam
 */

use CP_Defender\Module\Anti_Spam\Model\Pattern;
use CP_Defender\Module\Anti_Spam\Model\IP_Reputation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$table_blogs = $wpdb->base_prefix . 'defender_antispam_blogs';
$table_ips = $wpdb->base_prefix . 'defender_antispam_ips';
$table_blogs_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_blogs ) ) === $table_blogs;
$table_ips_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_ips ) ) === $table_ips;

// Statistiken sammeln
$total_blogs = get_blog_count();
$spam_blogs = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->blogs} WHERE spam = 1" );
$auto_spammed = $table_blogs_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_blogs} WHERE spammed_date > '1970-01-01'" ) : 0;
$patterns = Pattern::get_all();
$total_matches = array_sum( array_column( $patterns, 'matched' ) );
$top_patterns = array_slice( $patterns, 0, 10 );
usort( $top_patterns, function($a, $b) {
	return ( $b['matched'] ?? 0 ) - ( $a['matched'] ?? 0 );
});

$top_spammer_ips = IP_Reputation::get_top_spammers( 10 );
$blocked_ips = count( IP_Reputation::get_blocked_ips( 1000 ) );

// Zeitliche Statistik (letzte 30 Tage)
$daily_stats = array();
if ( $table_blogs_exists ) {
	$daily_stats = $wpdb->get_results(
		"SELECT DATE(signup_date) as date, 
			COUNT(*) as signups,
			SUM(CASE WHEN spammed_date > '1970-01-01' THEN 1 ELSE 0 END) as spammed
		FROM {$table_blogs}
		WHERE signup_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
		GROUP BY DATE(signup_date)
		ORDER BY date DESC
		LIMIT 30"
	);
}
?>

<div class="wrap">
	<h1><?php _e( '📊 Anti-Spam Statistiken', 'cpsec' ); ?></h1>
	<?php if ( ! $table_blogs_exists || ! $table_ips_exists ) : ?>
		<div class="notice notice-warning"><p><?php _e( 'Einige Anti-Spam Tabellen sind noch nicht verfügbar. Statistiken werden angezeigt, sobald das Modul initialisiert wurde.', 'cpsec' ); ?></p></div>
	<?php endif; ?>
	
	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
		<div class="postbox">
			<h2 class="hndle"><?php _e( 'Blog-Statistiken', 'cpsec' ); ?></h2>
			<div class="inside">
				<table class="wp-list-table widefat">
					<tr><td><?php _e( 'Gesamt:', 'cpsec' ); ?></td><td><strong><?php echo number_format_i18n( $total_blogs ); ?></strong></td></tr>
					<tr><td><?php _e( 'Spam:', 'cpsec' ); ?></td><td><strong style="color: #d63638;"><?php echo number_format_i18n( $spam_blogs ); ?></strong></td></tr>
					<tr><td><?php _e( 'Auto-Spam:', 'cpsec' ); ?></td><td><strong><?php echo number_format_i18n( $auto_spammed ); ?></strong></td></tr>
				</table>
			</div>
		</div>
		
		<div class="postbox">
			<h2 class="hndle"><?php _e( 'Pattern-Statistiken', 'cpsec' ); ?></h2>
			<div class="inside">
				<table class="wp-list-table widefat">
					<tr><td><?php _e( 'Aktive Patterns:', 'cpsec' ); ?></td><td><strong><?php echo count( $patterns ); ?></strong></td></tr>
					<tr><td><?php _e( 'Gesamt-Matches:', 'cpsec' ); ?></td><td><strong><?php echo number_format_i18n( $total_matches ); ?></strong></td></tr>
					<tr><td><?php _e( 'Effektivität:', 'cpsec' ); ?></td><td><strong><?php echo $total_blogs > 0 ? round( ( $total_matches / $total_blogs ) * 100, 1 ) : 0; ?>%</strong></td></tr>
				</table>
			</div>
		</div>
		
		<div class="postbox">
			<h2 class="hndle"><?php _e( 'IP-Statistiken', 'cpsec' ); ?></h2>
			<div class="inside">
				<table class="wp-list-table widefat">
					<tr><td><?php _e( 'Verfolgte IPs:', 'cpsec' ); ?></td><td><strong><?php echo number_format_i18n( $table_ips_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_ips}" ) : 0 ); ?></strong></td></tr>
					<tr><td><?php _e( 'Blockierte IPs:', 'cpsec' ); ?></td><td><strong style="color: #d63638;"><?php echo number_format_i18n( $blocked_ips ); ?></strong></td></tr>
				</table>
			</div>
		</div>
	</div>
	
	<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
		<div class="postbox">
			<h2 class="hndle"><?php _e( 'Top 10 Patterns (nach Matches)', 'cpsec' ); ?></h2>
			<div class="inside">
				<?php if ( empty( $top_patterns ) ) : ?>
					<p><?php _e( 'Noch keine Pattern-Matches.', 'cpsec' ); ?></p>
				<?php else : ?>
					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th><?php _e( 'Pattern', 'cpsec' ); ?></th>
								<th><?php _e( 'Typ', 'cpsec' ); ?></th>
								<th><?php _e( 'Matches', 'cpsec' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $top_patterns as $pattern ) : ?>
								<?php if ( ( $pattern['matched'] ?? 0 ) === 0 ) continue; ?>
								<tr>
									<td><code><?php echo esc_html( $pattern['regex'] ); ?></code></td>
									<td><?php echo esc_html( $pattern['type'] ); ?></td>
									<td><strong><?php echo number_format_i18n( $pattern['matched'] ?? 0 ); ?></strong></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="postbox">
			<h2 class="hndle"><?php _e( 'Top 10 Spammer-IPs', 'cpsec' ); ?></h2>
			<div class="inside">
				<?php if ( empty( $top_spammer_ips ) ) : ?>
					<p><?php _e( 'Noch keine Spammer-IPs erfasst.', 'cpsec' ); ?></p>
				<?php else : ?>
					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th><?php _e( 'IP-Adresse', 'cpsec' ); ?></th>
								<th><?php _e( 'Spam-Count', 'cpsec' ); ?></th>
								<th><?php _e( 'Status', 'cpsec' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $top_spammer_ips as $ip_data ) : ?>
								<tr>
									<td><code><?php echo esc_html( $ip_data['ip_address'] ); ?></code></td>
									<td><strong style="color: #d63638;"><?php echo number_format_i18n( $ip_data['spam_count'] ); ?></strong></td>
									<td>
										<?php if ( $ip_data['is_blocked'] ) : ?>
											<span style="color: #d63638;">🚫 <?php _e( 'Blockiert', 'cpsec' ); ?></span>
										<?php else : ?>
											<span><?php _e( 'Aktiv', 'cpsec' ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="postbox" style="margin-top: 20px;">
		<h2 class="hndle"><?php _e( 'Registrierungs-Trend (letzte 30 Tage)', 'cpsec' ); ?></h2>
		<div class="inside">
			<?php if ( empty( $daily_stats ) ) : ?>
				<p><?php _e( 'Keine Daten verfügbar.', 'cpsec' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th><?php _e( 'Datum', 'cpsec' ); ?></th>
							<th><?php _e( 'Registrierungen', 'cpsec' ); ?></th>
							<th><?php _e( 'Als Spam markiert', 'cpsec' ); ?></th>
							<th><?php _e( 'Spam-Rate', 'cpsec' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $daily_stats as $stat ) : ?>
							<tr>
								<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $stat->date ) ); ?></td>
								<td><?php echo number_format_i18n( $stat->signups ); ?></td>
								<td><span style="color: #d63638;"><?php echo number_format_i18n( $stat->spammed ); ?></span></td>
								<td>
									<?php 
									$rate = $stat->signups > 0 ? ( $stat->spammed / $stat->signups ) * 100 : 0;
									$color = $rate > 50 ? '#d63638' : ( $rate > 20 ? '#dba617' : '#46b450' );
									?>
									<strong style="color: <?php echo $color; ?>;"><?php echo round( $rate, 1 ); ?>%</strong>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
