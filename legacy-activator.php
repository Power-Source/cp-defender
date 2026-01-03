<?php


class WD_Legacy_Activator {
	public $cp_defender;

	public function __construct( CP_Defender $cp_defender ) {
		$this->cp_defender = $cp_defender;
		include_once $this->cp_defender->getPluginPath() . 'app/controller/requirement.php';
		new WD_Requirement();
	}
}