<?php

namespace CP_Defender\Module\Anti_Spam\Controller;

use CP_Defender\Module\Anti_Spam\Model\Settings;
use CP_Defender\Module\Anti_Spam\Model\Pattern;
use CP_Defender\Module\Anti_Spam\Model\IP_Reputation;
use CP_Defender\Module\Anti_Spam\Behavior\Disposable_Email;

/**
 * Signup Protection Controller
 * Schützt Multisite-Signups vor Spam
 * 
 * @package CP_Defender\Module\Anti_Spam
 * @since 1.1.0
 */
class Signup_Protection {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// Signup-Form Hooks
		add_action( 'signup_blogform', array( $this, 'add_verification_field' ), 50 );
		add_filter( 'wpmu_validate_blog_signup', array( $this, 'validate_blog_signup' ) );
		add_filter( 'wpmu_validate_user_signup', array( $this, 'validate_user_signup' ) );
		
		// BuddyPress Support
		add_action( 'bp_before_registration_submit_buttons', array( $this, 'add_verification_field_bp' ), 50 );
		add_action( 'bp_signup_validate', array( $this, 'validate_bp_signup' ) );
		
		// Nach erfolgreicher Aktivierung
		add_action( 'wpmu_new_blog', array( $this, 'track_new_blog' ), 10, 2 );
		add_filter( 'add_signup_meta', array( $this, 'save_signup_meta' ) );
		
		// IP-Blocking frühzeitig prüfen
		add_action( 'login_init', array( $this, 'check_ip_block' ) );
		add_action( 'signup_header', array( $this, 'check_ip_block' ) );
	}
	
	/**
	 * Prüft ob IP blockiert ist
	 */
	public function check_ip_block(): void {
		if ( ! Settings::get( 'ip_blocking_enabled' ) ) {
			return;
		}
		
		$ip = $this->get_user_ip();
		
		if ( IP_Reputation::is_blocked( $ip ) ) {
			wp_die(
				__( 'Deine IP-Adresse wurde aufgrund verdächtiger Aktivitäten blockiert. Bitte kontaktiere den Administrator.', 'cpsec' ),
				__( 'Zugriff verweigert', 'cpsec' ),
				array( 'response' => 403 )
			);
		}
	}
	
	/**
	 * Fügt Verification-Feld zum Signup-Formular hinzu
	 */
	public function add_verification_field( $errors ): void {
		$verification_type = Settings::get( 'human_verification' );
		
		switch ( $verification_type ) {
			case 'turnstile':
				$this->render_turnstile( $errors );
				break;
			case 'recaptcha':
				$this->render_recaptcha( $errors );
				break;
			case 'questions':
				$this->render_qa( $errors );
				break;
		}

		$this->render_honeypot();
	}

	/**
	 * Rendert Cloudflare Turnstile
	 */
	private function render_turnstile( $errors ): void {
		$site_key = Settings::get( 'turnstile_site_key' );

		if ( empty( $site_key ) ) {
			return;
		}

		echo '<div class="defender-turnstile-wrap">';
		echo '<label>' . __( 'Sicherheitsüberprüfung:', 'cpsec' ) . '</label>';

		if ( $errors && $errors->get_error_message( 'turnstile' ) ) {
			echo '<p class="error">' . esc_html( $errors->get_error_message( 'turnstile' ) ) . '</p>';
		}

		echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( $site_key ) . '"></div>';
		echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
		echo '</div>';
	}
	
	/**
	 * Fügt Verification-Feld für BuddyPress hinzu
	 */
	public function add_verification_field_bp(): void {
		global $bp;
		$this->add_verification_field( $bp->signup->errors ?? null );
	}
	
	/**
	 * Rendert reCAPTCHA
	 */
	private function render_recaptcha( $errors ): void {
		$site_key = Settings::get( 'recaptcha_site_key' );
		
		if ( empty( $site_key ) ) {
			return;
		}
		
		echo '<div class="defender-recaptcha-wrap">';
		echo '<label>' . __( 'Sicherheitsüberprüfung:', 'cpsec' ) . '</label>';
		
		if ( $errors && $errors->get_error_message( 'recaptcha' ) ) {
			echo '<p class="error">' . esc_html( $errors->get_error_message( 'recaptcha' ) ) . '</p>';
		}
		
		echo '<div class="g-recaptcha" data-sitekey="' . esc_attr( $site_key ) . '"></div>';
		echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
		echo '</div>';
	}
	
	/**
	 * Rendert Q&A
	 */
	private function render_qa( $errors ): void {
		$questions = Settings::get( 'qa_questions' );
		
		if ( empty( $questions ) ) {
			return;
		}
		
		// Zufällige Frage wählen
		$question = $questions[ array_rand( $questions ) ];
		$question_hash = md5( $question['question'] );
		
		echo '<div class="defender-qa-wrap">';
		echo '<label>' . esc_html( $question['question'] ) . '</label>';
		
		if ( $errors && $errors->get_error_message( 'qa_verification' ) ) {
			echo '<p class="error">' . esc_html( $errors->get_error_message( 'qa_verification' ) ) . '</p>';
		}
		
		echo '<input type="text" name="defender_qa_answer" autocomplete="off" />';
		echo '<input type="hidden" name="defender_qa_hash" value="' . esc_attr( $question_hash ) . '" />';
		echo '</div>';
	}

	/**
	 * Rendert Honeypot-Feld (unsichtbar für normale User)
	 */
	private function render_honeypot(): void {
		if ( ! Settings::get( 'honeypot_enabled' ) ) {
			return;
		}

		echo '<div class="defender-hp-wrap" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
		echo '<label for="defender_company">Company</label>';
		echo '<input type="text" id="defender_company" name="defender_company" value="" tabindex="-1" autocomplete="off" />';
		echo '</div>';
	}
	
	/**
	 * Validiert Blog-Signup
	 */
	public function validate_blog_signup( array $result ): array {
		if ( $this->should_bypass_for_admin_creation() ) {
			return $result;
		}

		// Honeypot prüfen
		$result = $this->check_honeypot( $result );

		// Disposable Email prüfen
		$result = $this->check_disposable_email( $result );

		// Rate Limiting prüfen
		$result = $this->check_rate_limit( $result );
		
		// IP-Reputation prüfen
		$result = $this->check_ip_reputation( $result );
		
		// Pattern prüfen (blocking)
		$result = $this->check_patterns_blocking( $result );
		
		// Human Verification prüfen
		$result = $this->check_human_verification( $result );
		
		return $result;
	}
	
	/**
	 * Validiert User-Signup
	 */
	public function validate_user_signup( array $result ): array {
		if ( $this->should_bypass_for_admin_creation() ) {
			return $result;
		}

		// Honeypot prüfen
		$result = $this->check_honeypot( $result );

		// Disposable Email prüfen
		$result = $this->check_disposable_email( $result );

		// Rate Limiting prüfen
		$result = $this->check_rate_limit( $result );
		
		// IP-Reputation prüfen
		$result = $this->check_ip_reputation( $result );
		
		// Pattern prüfen für Username/Email
		if ( ! empty( $_POST['user_name'] ) ) {
			$match = Pattern::check( $_POST['user_name'], Pattern::TYPE_USERNAME, Pattern::ACTION_BLOCK );
			if ( $match ) {
				$result['errors']->add( 'user_name', __( 'Dieser Benutzername ist nicht erlaubt. Bitte wähle einen anderen.', 'cpsec' ) );
			}
		}
		
		if ( ! empty( $_POST['user_email'] ) ) {
			$match = Pattern::check( $_POST['user_email'], Pattern::TYPE_EMAIL, Pattern::ACTION_BLOCK );
			if ( $match ) {
				$result['errors']->add( 'user_email', __( 'Diese E-Mail-Adresse ist nicht erlaubt. Bitte wähle eine andere.', 'cpsec' ) );
			}
		}
		
		// Human Verification prüfen
		$result = $this->check_human_verification( $result );
		
		return $result;
	}
	
	/**
	 * Validiert BuddyPress Signup
	 */
	public function validate_bp_signup(): void {
		global $bp;
		
		if ( ! isset( $bp->signup->errors ) ) {
			$bp->signup->errors = array();
		}

		if ( $this->is_honeypot_triggered() ) {
			$bp->signup->errors['defender_honeypot'] = __( 'Die Registrierung konnte nicht verarbeitet werden. Bitte versuche es erneut.', 'cpsec' );
			return;
		}

		// Disposable Email prüfen
		if ( ! empty( $_POST['signup_email'] ) && Settings::get( 'disposable_email_check_enabled', true ) ) {
			if ( Disposable_Email::is_disposable( wp_unslash( $_POST['signup_email'] ) ) ) {
				$bp->signup->errors['signup_email'] = __( 'Wegwerf-E-Mail-Adressen sind nicht erlaubt. Bitte verwende eine permanente E-Mail-Adresse.', 'cpsec' );
			}
		}

		$verification_type = Settings::get( 'human_verification' );
		switch ( $verification_type ) {
			case 'turnstile':
				if ( empty( $_POST['cf-turnstile-response'] ) || ! $this->verify_turnstile( wp_unslash( $_POST['cf-turnstile-response'] ) ) ) {
					$bp->signup->errors['turnstile'] = __( 'Turnstile-Verifizierung fehlgeschlagen. Bitte versuche es erneut.', 'cpsec' );
				}
				break;
			case 'recaptcha':
				if ( empty( $_POST['g-recaptcha-response'] ) || ! $this->verify_recaptcha( wp_unslash( $_POST['g-recaptcha-response'] ) ) ) {
					$bp->signup->errors['recaptcha'] = __( 'reCAPTCHA-Verifizierung fehlgeschlagen. Bitte versuche es erneut.', 'cpsec' );
				}
				break;
			case 'questions':
				if ( empty( $_POST['defender_qa_answer'] ) || empty( $_POST['defender_qa_hash'] ) || ! $this->verify_qa( wp_unslash( $_POST['defender_qa_answer'] ), wp_unslash( $_POST['defender_qa_hash'] ) ) ) {
					$bp->signup->errors['qa_verification'] = __( 'Die Antwort ist nicht korrekt. Bitte versuche es erneut.', 'cpsec' );
				}
				break;
		}
		
		// Rate Limiting
		if ( Settings::get( 'rate_limit_enabled' ) ) {
			$ip = $this->get_user_ip();
			$hours = Settings::get( 'rate_limit_period' );
			$limit = Settings::get( 'rate_limit_count' );
			$count = IP_Reputation::get_recent_signup_count( $ip, $hours );
			
			if ( $count >= $limit ) {
				$bp->signup->errors['multicheck'] = sprintf(
					__( 'Es sind zu viele Registrierungen von deiner IP-Adresse erfolgt. Bitte versuche es in %d Stunden erneut.', 'cpsec' ),
					$hours
				);
			}
		}
	}
	
	/**
	 * Prüft auf Disposable Email Domains
	 * Blockiert Wegwerf-E-Mail-Adressen
	 * 
	 * @param array $result Das Validierungsergebnis
	 * @return array Modifiziertes Result
	 */
	private function check_disposable_email( array $result ): array {
		if ( ! Settings::get( 'disposable_email_check_enabled', true ) ) {
			return $result;
		}
		
		// Prüfe Blog-Email (wpmu_validate_blog_signup)
		if ( ! empty( $_POST['blog_public_on'] ) && ! empty( $_POST['user_email'] ) ) {
			$email = wp_unslash( $_POST['user_email'] );
			
			if ( Disposable_Email::is_disposable( $email ) ) {
				$result['errors']->add(
					'user_email',
					__( 'Wegwerf-E-Mail-Adressen sind nicht erlaubt. Bitte verwende eine permanente E-Mail-Adresse.', 'cpsec' )
				);
			}
		}
		
		// Prüfe User-Email (wpmu_validate_user_signup)
		if ( ! empty( $_POST['user_email'] ) ) {
			$email = wp_unslash( $_POST['user_email'] );
			
			if ( Disposable_Email::is_disposable( $email ) ) {
				$result['errors']->add(
					'user_email',
					__( 'Wegwerf-E-Mail-Adressen sind nicht erlaubt. Bitte verwende eine permanente E-Mail-Adresse.', 'cpsec' )
				);
			}
		}
		
		return $result;
	}
	
	/**
	 * Prüft Rate Limiting
	 */
	private function check_rate_limit( array $result ): array {
		if ( ! Settings::get( 'rate_limit_enabled' ) ) {
			return $result;
		}
		
		$ip = $this->get_user_ip();
		$hours = Settings::get( 'rate_limit_period' );
		$limit = Settings::get( 'rate_limit_count' );
		
		$count = IP_Reputation::get_recent_signup_count( $ip, $hours );
		
		if ( $count >= $limit ) {
			$result['errors']->add( 
				'blogname', 
				sprintf(
					__( 'Es sind zu viele Registrierungen von deiner IP-Adresse erfolgt. Bitte versuche es in %d Stunden erneut.', 'cpsec' ),
					$hours
				)
			);
		}
		
		return $result;
	}
	
	/**
	 * Prüft IP-Reputation
	 */
	private function check_ip_reputation( array $result ): array {
		if ( ! Settings::get( 'ip_blocking_enabled' ) ) {
			return $result;
		}
		
		$ip = $this->get_user_ip();
		$threshold = Settings::get( 'ip_blocking_threshold' );
		$spam_count = IP_Reputation::get_spam_count( $ip );
		
		if ( $spam_count >= $threshold ) {
			$result['errors']->add(
				'blogname',
				__( 'Unsere automatischen Systeme haben verdächtige Aktivitäten von deiner IP-Adresse festgestellt. Bitte kontaktiere den Administrator.', 'cpsec' )
			);
			
			// IP blockieren
			IP_Reputation::block( $ip, sprintf( 'Auto-blocked: %d spam blogs', $spam_count ) );
		}
		
		return $result;
	}
	
	/**
	 * Prüft Patterns (blocking action)
	 */
	private function check_patterns_blocking( array $result ): array {
		if ( ! Settings::get( 'patterns_enabled' ) ) {
			return $result;
		}
		
		// Domain prüfen
		if ( ! empty( $_POST['blogname'] ) ) {
			$match = Pattern::check( $_POST['blogname'], Pattern::TYPE_DOMAIN, Pattern::ACTION_BLOCK );
			if ( $match ) {
				$result['errors']->add( 'blogname', __( 'Diese Domain ist nicht erlaubt. Bitte wähle eine andere.', 'cpsec' ) );
			}
		}
		
		// Title prüfen
		if ( ! empty( $_POST['blog_title'] ) ) {
			$match = Pattern::check( $_POST['blog_title'], Pattern::TYPE_TITLE, Pattern::ACTION_BLOCK );
			if ( $match ) {
				$result['errors']->add( 'blog_title', __( 'Dieser Titel ist nicht erlaubt. Bitte wähle einen anderen.', 'cpsec' ) );
			}
		}
		
		return $result;
	}
	
	/**
	 * Prüft Human Verification
	 */
	private function check_human_verification( array $result ): array {
		if ( $this->should_bypass_for_admin_creation() ) {
			return $result;
		}

		$verification_type = Settings::get( 'human_verification' );
		
		switch ( $verification_type ) {
			case 'turnstile':
				if ( empty( $_POST['cf-turnstile-response'] ) ) {
					$result['errors']->add( 'turnstile', __( 'Bitte bestätige, dass du kein Roboter bist.', 'cpsec' ) );
				} else {
					if ( ! $this->verify_turnstile( wp_unslash( $_POST['cf-turnstile-response'] ) ) ) {
						$result['errors']->add( 'turnstile', __( 'Turnstile-Verifizierung fehlgeschlagen. Bitte versuche es erneut.', 'cpsec' ) );
					}
				}
				break;

			case 'recaptcha':
				if ( empty( $_POST['g-recaptcha-response'] ) ) {
					$result['errors']->add( 'recaptcha', __( 'Bitte bestätige, dass du kein Roboter bist.', 'cpsec' ) );
				} else {
					if ( ! $this->verify_recaptcha( wp_unslash( $_POST['g-recaptcha-response'] ) ) ) {
						$result['errors']->add( 'recaptcha', __( 'reCAPTCHA-Verifizierung fehlgeschlagen. Bitte versuche es erneut.', 'cpsec' ) );
					}
				}
				break;
				
			case 'questions':
				if ( empty( $_POST['defender_qa_answer'] ) || empty( $_POST['defender_qa_hash'] ) ) {
					$result['errors']->add( 'qa_verification', __( 'Bitte beantworte die Sicherheitsfrage.', 'cpsec' ) );
				} else {
					if ( ! $this->verify_qa( $_POST['defender_qa_answer'], $_POST['defender_qa_hash'] ) ) {
						$result['errors']->add( 'qa_verification', __( 'Die Antwort ist nicht korrekt. Bitte versuche es erneut.', 'cpsec' ) );
					}
				}
				break;
		}
		
		return $result;
	}
	
	/**
	 * Verifiziert reCAPTCHA
	 */
	private function verify_recaptcha( string $response ): bool {
		$secret = Settings::get( 'recaptcha_secret_key' );
		$site_key = Settings::get( 'recaptcha_site_key' );
		
		if ( empty( $secret ) || empty( $site_key ) ) {
			return false;
		}
		
		$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
		$response = wp_remote_post( $verify_url, array(
			'body' => array(
				'secret' => $secret,
				'response' => $response,
				'remoteip' => $this->get_user_ip()
			)
		) );
		
		if ( is_wp_error( $response ) ) {
			return false;
		}
		
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		return ! empty( $body['success'] );
	}

	/**
	 * Verifiziert Cloudflare Turnstile
	 */
	private function verify_turnstile( string $response ): bool {
		$secret = Settings::get( 'turnstile_secret_key' );
		$site_key = Settings::get( 'turnstile_site_key' );

		if ( empty( $secret ) || empty( $site_key ) ) {
			return false;
		}

		$verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
		$http_response = wp_remote_post( $verify_url, array(
			'body' => array(
				'secret'   => $secret,
				'response' => $response,
				'remoteip' => $this->get_user_ip(),
			),
		) );

		if ( is_wp_error( $http_response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $http_response ), true );

		return ! empty( $body['success'] );
	}

	/**
	 * Prüft Honeypot-Feld und blockiert Bots
	 */
	private function check_honeypot( array $result ): array {
		if ( ! Settings::get( 'honeypot_enabled' ) ) {
			return $result;
		}

		if ( $this->is_honeypot_triggered() ) {
			$result['errors']->add( 'defender_honeypot', __( 'Die Registrierung konnte nicht verarbeitet werden. Bitte versuche es erneut.', 'cpsec' ) );
		}

		return $result;
	}

	/**
	 * Ermittelt, ob das Honeypot-Feld ausgefüllt wurde
	 */
	private function is_honeypot_triggered(): bool {
		if ( ! isset( $_POST['defender_company'] ) ) {
			return false;
		}

		$value = trim( (string) wp_unslash( $_POST['defender_company'] ) );

		return $value !== '';
	}
	
	/**
	 * Verifiziert Q&A
	 */
	private function verify_qa( string $answer, string $hash ): bool {
		$questions = Settings::get( 'qa_questions' );
		
		foreach ( $questions as $qa ) {
			if ( md5( $qa['question'] ) === $hash ) {
				return strcasecmp( trim( $answer ), trim( $qa['answer'] ) ) === 0;
			}
		}
		
		return false;
	}
	
	/**
	 * Speichert Signup-Meta-Daten
	 */
	public function save_signup_meta( array $meta ): array {
		$meta['defender_signup_data'] = array(
			'ip' => $this->get_user_ip(),
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'referer' => $_SERVER['HTTP_REFERER'] ?? '',
			'timestamp' => current_time( 'mysql' )
		);
		
		return $meta;
	}
	
	/**
	 * Trackt neuen Blog und prüft Spam-Patterns
	 */
	public function track_new_blog( int $blog_id, int $user_id ): void {
		global $wpdb;
		
		$ip = $this->get_user_ip();
		$table = $wpdb->base_prefix . 'defender_antispam_blogs';
		
		// Basis-Tracking einfügen
		$wpdb->insert( $table, array(
			'blog_id' => $blog_id,
			'last_user_id' => $user_id,
			'last_ip' => $ip,
			'last_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'signup_date' => current_time( 'mysql' ),
		), array( '%d', '%d', '%s', '%s', '%s' ) );
		
		// IP-Signup-Count erhöhen
		IP_Reputation::increment_signup_count( $ip );
		
		// Spam-Patterns prüfen (ACTION_SPAM)
		if ( Settings::get( 'patterns_enabled' ) && Settings::get( 'auto_spam_enabled' ) ) {
			$this->check_new_blog_for_spam( $blog_id );
		}
	}
	
	/**
	 * Prüft neuen Blog auf Spam-Patterns
	 */
	private function check_new_blog_for_spam( int $blog_id ): void {
		$blog_details = get_blog_details( $blog_id );
		$certainty = 0;
		$matched_patterns = array();
		
		// Domain prüfen
		$domain = str_replace( array( 'http://', 'https://', 'www.' ), '', $blog_details->domain );
		$match = Pattern::check( $domain, Pattern::TYPE_DOMAIN, Pattern::ACTION_SPAM );
		if ( $match ) {
			$certainty += 40;
			$matched_patterns[] = 'domain:' . $match['id'];
		}
		
		// Title prüfen
		$title = get_blog_option( $blog_id, 'blogname' );
		$match = Pattern::check( $title, Pattern::TYPE_TITLE, Pattern::ACTION_SPAM );
		if ( $match ) {
			$certainty += 40;
			$matched_patterns[] = 'title:' . $match['id'];
		}
		
		// Auto-Spam wenn über Threshold
		$threshold = Settings::get( 'auto_spam_certainty' );
		if ( $certainty >= $threshold ) {
			update_blog_status( $blog_id, 'spam', 1 );
			update_blog_option( $blog_id, 'defender_auto_spammed', true );
			
			// IP-Spam-Count erhöhen
			$ip = $this->get_user_ip();
			IP_Reputation::increment_spam_count( $ip );
			
			// In Tabelle aktualisieren
			global $wpdb;
			$wpdb->update(
				$wpdb->base_prefix . 'defender_antispam_blogs',
				array(
					'certainty' => $certainty,
					'spammed_date' => current_time( 'mysql' ),
					'pattern_matched' => implode( ',', $matched_patterns )
				),
				array( 'blog_id' => $blog_id ),
				array( '%d', '%s', '%s' ),
				array( '%d' )
			);
		} elseif ( $certainty > 0 ) {
			// Als verdächtig markieren
			global $wpdb;
			$wpdb->update(
				$wpdb->base_prefix . 'defender_antispam_blogs',
				array( 'certainty' => $certainty ),
				array( 'blog_id' => $blog_id ),
				array( '%d' ),
				array( '%d' )
			);
		}
	}
	
	/**
	 * Holt User-IP
	 */
	private function get_user_ip(): string {
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		
		// Prüfe Proxy-Header
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		
		// Sanitize
		$ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip );
		
		// Bei mehreren IPs erste nehmen
		if ( strpos( $ip, ',' ) !== false ) {
			$ips = explode( ',', $ip );
			$ip = trim( $ips[0] );
		}
		
		return $ip;
	}

	/**
	 * Bypass für vertrauenswürdige Admin-Erstellung im Dashboard.
	 */
	private function should_bypass_for_admin_creation(): bool {
		if ( ! is_admin() ) {
			return false;
		}

		if ( ! current_user_can( 'create_users' ) && ! current_user_can( 'manage_network_users' ) ) {
			return false;
		}

		// Nur relevante Admin-Screens (User/Site-Erstellung) ausnehmen.
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$base = $screen->base ?? '';

		if ( in_array( $base, array( 'user', 'user-new', 'site-new', 'network_user-new', 'network_site-new' ), true ) ) {
			return true;
		}

		$requested = sanitize_key( $_REQUEST['action'] ?? '' );

		return in_array( $requested, array( 'createuser', 'add-user', 'addsite' ), true );
	}
}
