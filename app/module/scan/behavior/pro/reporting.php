<?php


namespace CP_Defender\Module\Scan\Behavior\Pro;

use Hammer\Base\Behavior;
use CP_Defender\Behavior\Utils;

class Reporting extends Behavior {
	public function scheduleReportTime( $settings ) {
		if ( $settings->notification ) {
			$cronTime = \CP_Defender\Behavior\Utils::instance()->reportCronTimestamp( $settings->time, 'scanReportCron' );
			wp_schedule_event( $cronTime, 'daily', 'scanReportCron' );
		} else {
			wp_clear_scheduled_hook( 'processScanCron' );
		}

	}
}