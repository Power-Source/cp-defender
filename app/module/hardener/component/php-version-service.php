<?php


namespace CP_Defender\Module\Hardener\Component;

use CP_Defender\Module\Hardener\IRule_Service;
use CP_Defender\Module\Hardener\Rule_Service;

class PHP_Version_Service extends Rule_Service implements IRule_Service {
	const MINIMUM_SUPPORTED_VERSION = '8.5.12';
	const RECOMMENDED_VERSION = '8.5.12';

	/**
	 * @return bool
	 */
	public function check() {
		return version_compare( $this->getPHPVersion(), self::MINIMUM_SUPPORTED_VERSION, '>=' );
	}

	/**
	 * @return string
	 */
	public function getRecommendedVersion() {
		return self::RECOMMENDED_VERSION;
	}

	public function process() {

	}

	public function revert() {

	}

	public function listen() {

	}
}