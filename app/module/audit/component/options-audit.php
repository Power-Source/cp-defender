<?php


namespace CP_Defender\Module\Audit\Component;

use CP_Defender\Behavior\Utils;
use CP_Defender\Module\Audit\Event_Abstract;

class Options_Audit extends Event_Abstract {
	const CONTEXT_SETTINGS = 'ct_setting';

	public function get_hooks() {
		return array(
			'update_option' => array(
				'args'        => array( 'option', 'old_value', 'value' ),
				'callback'    => array( '\CP_Defender\Module\Audit\Component\Options_Audit', 'process_options' ),
				'level'       => self::LOG_LEVEL_ERROR,
				'event_type'  => 'settings',
				'action_type' => Audit_API::ACTION_UPDATED,
			),
			/*'update_site_option' => array(
				'args'        => array( 'option', 'old_value', 'value' ),
				'callback'    => array( 'WD_Options_Audit', 'process_network_options' ),
				'level'       => self::LOG_LEVEL_ERROR,
				'event_type'  => 'settings',
				'action_type' => Audit_API::ACTION_UPDATED,
			)*/
		);
	}

	public static function process_network_options() {
		$args   = func_get_args();
		$option = $args[1]['option'];
		$old    = $args[1]['old_value'];
		$new    = $args[1]['value'];

		$option_human_read = self::key_to_human_name( $option );

		if ( $old == $new ) {
			return false;
		}

		if ( is_array( $old ) ) {
			$old = implode( ', ', $old );
		}

		if ( is_array( $new ) ) {
			$new = implode( ', ', $new );
		}

		$text = sprintf( esc_html__( "%s Aktualisiere Netzwerkoption %s von %s auf %s", cp_defender()->domain ),
			\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old, $new );

		return array( $text, self::CONTEXT_SETTINGS );
	}

	/**
	 * @return bool|string
	 */
	public static function process_options() {
		$args              = func_get_args();
		$option            = $args[1]['option'];
		$old               = $args[1]['old_value'];
		$new               = $args[1]['value'];
		$option_human_read = self::key_to_human_name( $option );

		//to avoid the recursing compare if both are nested array, convert all to string
		$check1 = is_array( $old ) ? serialize( $old ) : $old;
		$check2 = is_array( $new ) ? serialize( $new ) : $new;

		if ( $check1 == $check2 ) {
			return false;
		}
		if ( $option_human_read !== false ) {
			//we will need special case for reader
			switch ( $option ) {
				case 'users_can_register':
					if ( $new == 0 ) {
						$text = sprintf( esc_html__( "%s hat die Webseiten-Registrierung deaktiviert", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ) );
					} else {
						$text = sprintf( esc_html__( "%s hat die Webseiten-Registrierung aktiviert", cp_defender()->domain ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ) );
					}
					break;
				case 'start_of_week':
					global $wp_locale;
					$old_day = $wp_locale->get_weekday( $old );
					$new_day = $wp_locale->get_weekday( $new );
					$text    = sprintf( esc_html__( "%s aktualisierte Option %s von %s auf %s", cp_defender()->domain ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old_day, $new_day );
					break;
				case 'WPLANG':
					//no old value here
					$text = sprintf( esc_html__( "%s aktualisierte Option %s auf %s", cp_defender()->domain ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old, $new );
					break;
				default:
					$text = sprintf( esc_html__( "%s aktualisierte Option %s von %s auf %s", cp_defender()->domain ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old, $new );
					break;
			}

			return array( $text, self::CONTEXT_SETTINGS );
		}

		return false;
	}

	private static function key_to_human_name( $key ) {
		$human_read = apply_filters( 'wd_audit_settings_keys', array(
			'blogname'                      => esc_html__( "Seitentitel", cp_defender()->domain ),
			'blogdescription'               => esc_html__( "Tagline", cp_defender()->domain ),
			'gmt_offset'                    => esc_html__( "Zeitzone", cp_defender()->domain ),
			'date_format'                   => esc_html__( "Datumsformat", cp_defender()->domain ),
			'time_format'                   => esc_html__( "Uhrzeitformat", cp_defender()->domain ),
			'start_of_week'                 => esc_html__( "Woche beginnt am", cp_defender()->domain ),
			'timezone_string'               => esc_html__( "Zeitzone", cp_defender()->domain ),
			'WPLANG'                        => esc_html__( "Webseiten-Sprache", cp_defender()->domain ),
			'siteurl'                       => esc_html__( "WordPress-Adresse (URL)", cp_defender()->domain ),
			'home'                          => esc_html__( "Webseiten-Adresse (URL)", cp_defender()->domain ),
			'admin_email'                   => esc_html__( "-Mail-Adresse", cp_defender()->domain ),
			'users_can_register'            => esc_html__( "Mitgliedschaft", cp_defender()->domain ),
			'default_role'                  => esc_html__( "Standardrolle für neue Benutzer", cp_defender()->domain ),
			'default_pingback_flag'         => esc_html__( "Standard-Artikel-Einstellungen", cp_defender()->domain ),
			'default_ping_status'           => esc_html__( "Standard-Artikel-Einstellungen", cp_defender()->domain ),
			'default_comment_status'        => esc_html__( "Standard-Artikel-Einstellungen", cp_defender()->domain ),
			'comments_notify'               => esc_html__( "-Mail-Benachrichtigungen", cp_defender()->domain ),
			'moderation_notify'             => esc_html__( "-Mail-Benachrichtigungen", cp_defender()->domain ),
			'comment_moderation'            => esc_html__( "Bevor ein Kommentar erscheint", cp_defender()->domain ),
			'require_name_email'            => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'comment_whitelist'             => esc_html__( "Bevor ein Kommentar erscheint", cp_defender()->domain ),
			'comment_max_links'             => esc_html__( "Kommentar-Moderation", cp_defender()->domain ),
			'moderation_keys'               => esc_html__( "Kommentar-Moderation", cp_defender()->domain ),
			'blacklist_keys'                => esc_html__( "Kommentar-Blacklist", cp_defender()->domain ),
			'show_avatars'                  => esc_html__( "Avatar-Anzeige", cp_defender()->domain ),
			'avatar_rating'                 => esc_html__( "Maximale Bewertung", cp_defender()->domain ),
			'avatar_default'                => esc_html__( "Standard-Avatar", cp_defender()->domain ),
			'close_comments_for_old_posts'  => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'close_comments_days_old'       => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'thread_comments'               => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'thread_comments_depth'         => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'page_comments'                 => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'comments_per_page'             => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'default_comments_page'         => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'comment_order'                 => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'comment_registration'          => esc_html__( "Andere Kommentar-Einstellungen", cp_defender()->domain ),
			'thumbnail_size_w'              => esc_html__( "Thumbnail-Größe", cp_defender()->domain ),
			'thumbnail_size_h'              => esc_html__( "Thumbnail-Größe", cp_defender()->domain ),
			'thumbnail_crop'                => esc_html__( "Thumbnail-Größe", cp_defender()->domain ),
			'medium_size_w'                 => esc_html__( "Mittlere Größe", cp_defender()->domain ),
			'medium_size_h'                 => esc_html__( "Mittlere Größe", cp_defender()->domain ),
			'medium_large_size_w'           => esc_html__( "Mittlere Größe", cp_defender()->domain ),
			'medium_large_size_h'           => esc_html__( "Mittlere Größe", cp_defender()->domain ),
			'large_size_w'                  => esc_html__( "Große Größe", cp_defender()->domain ),
			'large_size_h'                  => esc_html__( "Große Größe", cp_defender()->domain ),
			'image_default_size'            => esc_html__( "", cp_defender()->domain ),
			'image_default_align'           => esc_html__( "", cp_defender()->domain ),
			'image_default_link_type'       => esc_html__( "", cp_defender()->domain ),
			'uploads_use_yearmonth_folders' => esc_html__( "Dateien hochladen", cp_defender()->domain ),
			'posts_per_page'                => esc_html__( "Blogseiten zeigen maximal", cp_defender()->domain ),
			'posts_per_rss'                 => esc_html__( "Syndikationsfeeds zeigen die neuesten", cp_defender()->domain ),
			'rss_use_excerpt'               => esc_html__( "ür jeden Artikel in einem Feed anzeigen", cp_defender()->domain ),
			'show_on_front'                 => esc_html__( "Startseite zeigt an", cp_defender()->domain ),
			'page_on_front'                 => esc_html__( "Startseite", cp_defender()->domain ),
			'page_for_posts'                => esc_html__( "Beitragsseite", cp_defender()->domain ),
			'blog_public'                   => esc_html__( "Suchmaschinen-Sichtbarkeit", cp_defender()->domain ),
			'default_category'              => esc_html__( "Standard-Beitragskategorie", cp_defender()->domain ),
			'default_email_category'        => esc_html__( "Standard-Mail-Kategorie", cp_defender()->domain ),
			'default_link_category'         => esc_html__( "", cp_defender()->domain ),
			'default_post_format'           => esc_html__( "Standard-Beitragsformat", cp_defender()->domain ),
			'mailserver_url'                => esc_html__( "Mail-Server", cp_defender()->domain ),
			'mailserver_port'               => esc_html__( "Port", cp_defender()->domain ),
			'mailserver_login'              => esc_html__( "Login Name", cp_defender()->domain ),
			'mailserver_pass'               => esc_html__( "Passwort", cp_defender()->domain ),
			'ping_sites'                    => esc_html__( "", cp_defender()->domain ),
			'permalink_structure'           => esc_html__( "Permalink Einstellung", cp_defender()->domain ),
			'category_base'                 => esc_html__( "Kategoriebasis", cp_defender()->domain ),
			'tag_base'                      => esc_html__( "Tag-Basis", cp_defender()->domain ),
			'registrationnotification'      => esc_html__( "Registrierungsbenachrichtigung", cp_defender()->domain ),
			'registration'                  => esc_html__( "Neue Registrierungen erlauben", cp_defender()->domain ),
			'add_new_users'                 => esc_html__( "Neue Benutzer hinzufügen", cp_defender()->domain ),
			'menu_items'                    => esc_html__( "Administrationsmenüs aktivieren", cp_defender()->domain ),
			'upload_space_check_disabled'   => esc_html__( "Webseiten Upload-Speicherplatz deaktiviert", cp_defender()->domain ),
			'blog_upload_space'             => esc_html__( "Webseiten Upload-Speicherplatz", cp_defender()->domain ),
			'upload_filetypes'              => esc_html__( "Upload Dateitypen", cp_defender()->domain ),
			'site_name'                     => esc_html__( "Netzwerk Titel", cp_defender()->domain ),
			'first_post'                    => esc_html__( "Erster Beitrag", cp_defender()->domain ),
			'first_page'                    => esc_html__( "Erste Seite", cp_defender()->domain ),
			'first_comment'                 => esc_html__( "Erster Kommentar", cp_defender()->domain ),
			'first_comment_url'             => esc_html__( "URL des ersten Kommentars", cp_defender()->domain ),
			'first_comment_author'          => esc_html__( "Autor des ersten Kommentars", cp_defender()->domain ),
			'welcome_email'                 => esc_html__( "Willkommens-E-Mail", cp_defender()->domain ),
			'welcome_user_email'            => esc_html__( "Willkommens-E-Mail an Benutzer", cp_defender()->domain ),
			'fileupload_maxk'               => esc_html__( "Maximale Upload-Dateigröße", cp_defender()->domain ),
			//'global_terms_enabled'          => esc_html__( "", cp_defender()->domain ),
			'illegal_names'                 => esc_html__( "Verbotene Namen", cp_defender()->domain ),
			'limited_email_domains'         => esc_html__( "Begrenzte E-Mail-Registrierungen", cp_defender()->domain ),
			'banned_email_domains'          => esc_html__( "Verbotene E-Mail-Domains", cp_defender()->domain ),
		) );

		if ( isset( $human_read[ $key ] ) ) {
			if ( empty( $human_read[ $key ] ) ) {
				return $key;
			}

			return $human_read[ $key ];
		}

		return false;
	}

	public function dictionary() {
		return array(
			self::CONTEXT_SETTINGS => esc_html__( "Einstellungen", cp_defender()->domain )
		);
	}
}