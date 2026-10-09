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

		$text = sprintf( esc_html__( "%s Aktualisiere Netzwerkoption %s von %s auf %s", 'cpsec' ),
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
						$text = sprintf( esc_html__( "%s hat die Webseiten-Registrierung deaktiviert", 'cpsec' ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ) );
					} else {
						$text = sprintf( esc_html__( "%s hat die Webseiten-Registrierung aktiviert", 'cpsec' ), \CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ) );
					}
					break;
				case 'start_of_week':
					global $wp_locale;
					$old_day = $wp_locale->get_weekday( $old );
					$new_day = $wp_locale->get_weekday( $new );
					$text    = sprintf( esc_html__( "%s aktualisierte Option %s von %s auf %s", 'cpsec' ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old_day, $new_day );
					break;
				case 'WPLANG':
					//no old value here
					$text = sprintf( esc_html__( "%s aktualisierte Option %s auf %s", 'cpsec' ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old, $new );
					break;
				default:
					$text = sprintf( esc_html__( "%s aktualisierte Option %s von %s auf %s", 'cpsec' ),
						\CP_Defender\Behavior\Utils::instance()->getDisplayName( get_current_user_id() ), $option_human_read, $old, $new );
					break;
			}

			return array( $text, self::CONTEXT_SETTINGS );
		}

		return false;
	}

	private static function key_to_human_name( $key ) {
		$human_read = apply_filters( 'wd_audit_settings_keys', array(
			'blogname'                      => esc_html__( "Seitentitel", 'cpsec' ),
			'blogdescription'               => esc_html__( "Tagline", 'cpsec' ),
			'gmt_offset'                    => esc_html__( "Zeitzone", 'cpsec' ),
			'date_format'                   => esc_html__( "Datumsformat", 'cpsec' ),
			'time_format'                   => esc_html__( "Uhrzeitformat", 'cpsec' ),
			'start_of_week'                 => esc_html__( "Woche beginnt am", 'cpsec' ),
			'timezone_string'               => esc_html__( "Zeitzone", 'cpsec' ),
			'WPLANG'                        => esc_html__( "Webseiten-Sprache", 'cpsec' ),
			'siteurl'                       => esc_html__( "WordPress-Adresse (URL)", 'cpsec' ),
			'home'                          => esc_html__( "Webseiten-Adresse (URL)", 'cpsec' ),
			'admin_email'                   => esc_html__( "-Mail-Adresse", 'cpsec' ),
			'users_can_register'            => esc_html__( "Mitgliedschaft", 'cpsec' ),
			'default_role'                  => esc_html__( "Standardrolle für neue Benutzer", 'cpsec' ),
			'default_pingback_flag'         => esc_html__( "Standard-Artikel-Einstellungen", 'cpsec' ),
			'default_ping_status'           => esc_html__( "Standard-Artikel-Einstellungen", 'cpsec' ),
			'default_comment_status'        => esc_html__( "Standard-Artikel-Einstellungen", 'cpsec' ),
			'comments_notify'               => esc_html__( "-Mail-Benachrichtigungen", 'cpsec' ),
			'moderation_notify'             => esc_html__( "-Mail-Benachrichtigungen", 'cpsec' ),
			'comment_moderation'            => esc_html__( "Bevor ein Kommentar erscheint", 'cpsec' ),
			'require_name_email'            => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'comment_whitelist'             => esc_html__( "Bevor ein Kommentar erscheint", 'cpsec' ),
			'comment_max_links'             => esc_html__( "Kommentar-Moderation", 'cpsec' ),
			'moderation_keys'               => esc_html__( "Kommentar-Moderation", 'cpsec' ),
			'blacklist_keys'                => esc_html__( "Kommentar-Blacklist", 'cpsec' ),
			'show_avatars'                  => esc_html__( "Avatar-Anzeige", 'cpsec' ),
			'avatar_rating'                 => esc_html__( "Maximale Bewertung", 'cpsec' ),
			'avatar_default'                => esc_html__( "Standard-Avatar", 'cpsec' ),
			'close_comments_for_old_posts'  => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'close_comments_days_old'       => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'thread_comments'               => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'thread_comments_depth'         => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'page_comments'                 => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'comments_per_page'             => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'default_comments_page'         => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'comment_order'                 => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'comment_registration'          => esc_html__( "Andere Kommentar-Einstellungen", 'cpsec' ),
			'thumbnail_size_w'              => esc_html__( "Thumbnail-Größe", 'cpsec' ),
			'thumbnail_size_h'              => esc_html__( "Thumbnail-Größe", 'cpsec' ),
			'thumbnail_crop'                => esc_html__( "Thumbnail-Größe", 'cpsec' ),
			'medium_size_w'                 => esc_html__( "Mittlere Größe", 'cpsec' ),
			'medium_size_h'                 => esc_html__( "Mittlere Größe", 'cpsec' ),
			'medium_large_size_w'           => esc_html__( "Mittlere Größe", 'cpsec' ),
			'medium_large_size_h'           => esc_html__( "Mittlere Größe", 'cpsec' ),
			'large_size_w'                  => esc_html__( "Große Größe", 'cpsec' ),
			'large_size_h'                  => esc_html__( "Große Größe", 'cpsec' ),
			'image_default_size'            => esc_html__( "", 'cpsec' ),
			'image_default_align'           => esc_html__( "", 'cpsec' ),
			'image_default_link_type'       => esc_html__( "", 'cpsec' ),
			'uploads_use_yearmonth_folders' => esc_html__( "Dateien hochladen", 'cpsec' ),
			'posts_per_page'                => esc_html__( "Blogseiten zeigen maximal", 'cpsec' ),
			'posts_per_rss'                 => esc_html__( "Syndikationsfeeds zeigen die neuesten", 'cpsec' ),
			'rss_use_excerpt'               => esc_html__( "ür jeden Artikel in einem Feed anzeigen", 'cpsec' ),
			'show_on_front'                 => esc_html__( "Startseite zeigt an", 'cpsec' ),
			'page_on_front'                 => esc_html__( "Startseite", 'cpsec' ),
			'page_for_posts'                => esc_html__( "Beitragsseite", 'cpsec' ),
			'blog_public'                   => esc_html__( "Suchmaschinen-Sichtbarkeit", 'cpsec' ),
			'default_category'              => esc_html__( "Standard-Beitragskategorie", 'cpsec' ),
			'default_email_category'        => esc_html__( "Standard-Mail-Kategorie", 'cpsec' ),
			'default_link_category'         => esc_html__( "", 'cpsec' ),
			'default_post_format'           => esc_html__( "Standard-Beitragsformat", 'cpsec' ),
			'mailserver_url'                => esc_html__( "Mail-Server", 'cpsec' ),
			'mailserver_port'               => esc_html__( "Port", 'cpsec' ),
			'mailserver_login'              => esc_html__( "Login Name", 'cpsec' ),
			'mailserver_pass'               => esc_html__( "Passwort", 'cpsec' ),
			'ping_sites'                    => esc_html__( "", 'cpsec' ),
			'permalink_structure'           => esc_html__( "Permalink Einstellung", 'cpsec' ),
			'category_base'                 => esc_html__( "Kategoriebasis", 'cpsec' ),
			'tag_base'                      => esc_html__( "Tag-Basis", 'cpsec' ),
			'registrationnotification'      => esc_html__( "Registrierungsbenachrichtigung", 'cpsec' ),
			'registration'                  => esc_html__( "Neue Registrierungen erlauben", 'cpsec' ),
			'add_new_users'                 => esc_html__( "Neue Benutzer hinzufügen", 'cpsec' ),
			'menu_items'                    => esc_html__( "Administrationsmenüs aktivieren", 'cpsec' ),
			'upload_space_check_disabled'   => esc_html__( "Webseiten Upload-Speicherplatz deaktiviert", 'cpsec' ),
			'blog_upload_space'             => esc_html__( "Webseiten Upload-Speicherplatz", 'cpsec' ),
			'upload_filetypes'              => esc_html__( "Upload Dateitypen", 'cpsec' ),
			'site_name'                     => esc_html__( "Netzwerk Titel", 'cpsec' ),
			'first_post'                    => esc_html__( "Erster Beitrag", 'cpsec' ),
			'first_page'                    => esc_html__( "Erste Seite", 'cpsec' ),
			'first_comment'                 => esc_html__( "Erster Kommentar", 'cpsec' ),
			'first_comment_url'             => esc_html__( "URL des ersten Kommentars", 'cpsec' ),
			'first_comment_author'          => esc_html__( "Autor des ersten Kommentars", 'cpsec' ),
			'welcome_email'                 => esc_html__( "Willkommens-E-Mail", 'cpsec' ),
			'welcome_user_email'            => esc_html__( "Willkommens-E-Mail an Benutzer", 'cpsec' ),
			'fileupload_maxk'               => esc_html__( "Maximale Upload-Dateigröße", 'cpsec' ),
			//'global_terms_enabled'          => esc_html__( "", 'cpsec' ),
			'illegal_names'                 => esc_html__( "Verbotene Namen", 'cpsec' ),
			'limited_email_domains'         => esc_html__( "Begrenzte E-Mail-Registrierungen", 'cpsec' ),
			'banned_email_domains'          => esc_html__( "Verbotene E-Mail-Domains", 'cpsec' ),
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
			self::CONTEXT_SETTINGS => esc_html__( "Einstellungen", 'cpsec' )
		);
	}
}