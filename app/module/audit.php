<?php


namespace CP_Defender\Module;

use Hammer\Base\Module;
use CP_Defender\Module\Audit\Controller\Main;

class Audit extends Module {
	public function __construct() {
		if ( file_exists( __DIR__ . '/audit/test' ) ) {
			@unlink( __DIR__ . '/audit/test' );
		}
		new Main();
	}
}