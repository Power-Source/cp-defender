<?php

namespace CP_Defender\Module;

use Hammer\Base\Module;
use CP_Defender\Module\Anti_Spam\Controller\Main;

/**
 * Anti-Spam Modul
 * Schützt Multisite-Installationen vor Spam-Registrierungen und Spam-Blogs
 * 
 * @package CP_Defender\Module
 * @since 1.1.0
 */
class Anti_Spam extends Module {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// Nur für Multisite-Installationen aktivieren
		if ( ! is_multisite() ) {
			return;
		}
		
		// Registriere Custom Post Type für Pattern
		$this->register_post_types();
		
		// Starte Hauptcontroller
		new Main();
	}
	
	/**
	 * Registriert Custom Post Types für das Anti-Spam Modul
	 */
	private function register_post_types(): void {
		// Blog-Spam-Log
		register_post_type( 'wd_antispam_log', array(
			'labels'          => array(
				'name'          => __( 'Anti-Spam Protokolle', 'cpsec' ),
				'singular_name' => __( 'Anti-Spam Protokoll', 'cpsec' )
			),
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'capability_type' => array( 'wd_antispam_log', 'wd_antispam_logs' ),
			'map_meta_cap'    => true,
			'hierarchical'    => false,
			'rewrite'         => false,
			'query_var'       => false,
			'supports'        => array( '' ),
		) );
	}
}
