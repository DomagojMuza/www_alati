<?php
/**
 * Plugin Name: Contact Form Mailer
 * Description: Contact form with PHPMailer (SMTP), client-side validation, honeypot + rate-limit spam protection. Use shortcode [contact_form].
 * Version:     1.1.0
 * Author:      Your Name
 * Text Domain: contact-form-mailer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'phpmailer_init', function( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465;              // TLS port
    $phpmailer->SMTPSecure = 'ssl';            // Enable TLS
    $phpmailer->Username   = 'domim1998@gmail.com';
    $phpmailer->Password   = 'adyd olod hcap stup'; // NOT your Gmail password
    $phpmailer->From       = 'domim1998@gmail.com';
    $phpmailer->FromName   = 'Ordino Pro Service';
});

// Load PHPMailer bundled with WordPress core (available since WP 5.5)
require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

define( 'CFM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CFM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class Contact_Form_Mailer {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'custom_contact_form', array( $this, 'render_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_cfm_submit', array( $this, 'handle_submission' ) );
		add_action( 'wp_ajax_nopriv_cfm_submit', array( $this, 'handle_submission' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	// -------------------------------------------------------------------------
	// Assets
	// -------------------------------------------------------------------------

	public function enqueue_assets() {
		wp_enqueue_style(
			'cfm-style',
			CFM_PLUGIN_URL . 'assets/contact-form.css',
			array(),
			'1.0.0'
		);
		wp_enqueue_script(
			'cfm-script',
			CFM_PLUGIN_URL . 'assets/contact-form.js',
			array(),
			'1.0.0',
			true
		);
		wp_localize_script( 'cfm-script', 'cfm_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		) );
	}

	// -------------------------------------------------------------------------
	// Shortcode / Form HTML
	// -------------------------------------------------------------------------

	public function render_form() {
		ob_start();
		?>
		<div class="cfm-wrapper">
			<form id="cfm-form" class="cfm-form" novalidate>

				<?php wp_nonce_field( 'cfm_nonce', 'cfm_nonce_field' ); ?>

				<!-- Time-based spam check: timestamp set at page render -->
				<input type="hidden" name="cfm_timestamp" value="<?php echo esc_attr( time() ); ?>" />

				<!-- Honeypot: korisnici nikada ne vide ni ne ispunjavaju ovo -->
				<div class="cfm-honeypot" aria-hidden="true">
					<label for="cfm_website">Ostavite ovo prazno</label>
					<input type="text" id="cfm_website" name="cfm_website"
						   tabindex="-1" autocomplete="off" />
				</div>

				<!-- Redak 1: Ime + Prezime -->
				<div class="cfm-row cfm-two-col">
					<div class="cfm-field">
						<label for="cfm_name">
							Ime <span class="cfm-required" aria-hidden="true">*</span>
						</label>
						<input type="text" id="cfm_name" name="cfm_name"
							   placeholder="npr. Ivan" maxlength="100"
							   autocomplete="given-name" />
						<span class="cfm-error" id="cfm_name_error" role="alert"></span>
					</div>
					<div class="cfm-field">
						<label for="cfm_surname">
							Prezime <span class="cfm-required" aria-hidden="true">*</span>
						</label>
						<input type="text" id="cfm_surname" name="cfm_surname"
							   placeholder="npr. Horvat" maxlength="100"
							   autocomplete="family-name" />
						<span class="cfm-error" id="cfm_surname_error" role="alert"></span>
					</div>
				</div>

				<!-- Redak 2: E-mail + Telefon -->
				<div class="cfm-row cfm-two-col">
					<div class="cfm-field">
						<label for="cfm_email">
							E-mail adresa <span class="cfm-required" aria-hidden="true">*</span>
						</label>
						<input type="email" id="cfm_email" name="cfm_email"
							   placeholder="vas@email.com" maxlength="254"
							   autocomplete="email" />
						<span class="cfm-error" id="cfm_email_error" role="alert"></span>
					</div>
					<div class="cfm-field">
						<label for="cfm_phone">Broj telefona</label>
						<input type="tel" id="cfm_phone" name="cfm_phone"
							   placeholder="+385 91 234 5678" maxlength="30"
							   autocomplete="tel" />
						<span class="cfm-error" id="cfm_phone_error" role="alert"></span>
					</div>
				</div>

				<!-- Redak 3: Poruka -->
				<div class="cfm-row">
					<div class="cfm-field">
						<label for="cfm_message">
							Poruka <span class="cfm-required" aria-hidden="true">*</span>
						</label>
						<textarea id="cfm_message" name="cfm_message" rows="6"
								  placeholder="Napišite svoju poruku ovdje…" maxlength="5000"></textarea>
						<span class="cfm-char-count" id="cfm_message_count">0 / 5000</span>
						<span class="cfm-error" id="cfm_message_error" role="alert"></span>
					</div>
				</div>

				<!-- Redak 4: Suglasnost za privatnost -->
				<div class="cfm-row">
					<div class="cfm-field cfm-checkbox-field">
						<label class="cfm-checkbox-label">
							<input type="checkbox" id="cfm_privacy" name="cfm_privacy" value="1" />
							<span>Slažem se s obradom mojih osobnih podataka. <span class="cfm-required" aria-hidden="true">*</span></span>
						</label>
						<span class="cfm-error" id="cfm_privacy_error" role="alert"></span>
					</div>
				</div>

				<!-- Redak 5: Gumb za slanje -->
				<div class="cfm-row cfm-submit-row">
					<button type="submit" class="cfm-submit-btn" id="cfm-submit-btn">
						<span class="cfm-btn-text btn btn-color-primary btn-style-default btn-shape-semi-round">Pošalji poruku</span>
						<span class="cfm-btn-spinner" aria-hidden="true"></span>
					</button>
				</div>

				<!-- Server response area -->
				<div id="cfm-response" class="cfm-response" role="alert" aria-live="polite"></div>

			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	// -------------------------------------------------------------------------
	// AJAX handler
	// -------------------------------------------------------------------------

	public function handle_submission() {

		// 1. Provjera nonce (CSRF zaštita)
		if ( ! check_ajax_referer( 'cfm_nonce', 'cfm_nonce_field', false ) ) {
			wp_send_json_error( array( 'message' => 'Greška' ) );
		}

		// 2. Honeypot provjera (tiho propuštanje kako botovima ne bi otkrili mehanizam)
		if ( ! empty( $_POST['cfm_website'] ) ) {
			wp_send_json_success( array( 'message' => 'Hvala! Vaša poruka je poslana.' ) );
		}

		// 3. Provjera vremena (obrazac mora biti na ekranu najmanje 3 sekunde)
		$render_time = isset( $_POST['cfm_timestamp'] ) ? absint( $_POST['cfm_timestamp'] ) : 0;
		if ( $render_time > 0 && ( time() - $render_time ) < 3 ) {
			wp_send_json_error( array( 'message' => 'Molimo pričekajte trenutak i pokušajte ponovo.' ) );
		}

		// 4. Ograničenje broja slanja: max 5 po IP adresi na sat
		$ip       = $this->get_client_ip();
		$rate_key = 'cfm_rate_' . md5( $ip );
		$count    = (int) get_transient( $rate_key );
		if ( $count >= 5 ) {
			wp_send_json_error( array( 'message' => 'Greška' ) );
		}

		// 5. Sanitise inputs
		$name    = sanitize_text_field( wp_unslash( $_POST['cfm_name']    ?? '' ) );
		$surname = sanitize_text_field( wp_unslash( $_POST['cfm_surname'] ?? '' ) );
		$email   = sanitize_email(      wp_unslash( $_POST['cfm_email']   ?? '' ) );
		$phone   = sanitize_text_field( wp_unslash( $_POST['cfm_phone']   ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['cfm_message'] ?? '' ) );
		$privacy = ! empty( $_POST['cfm_privacy'] ) && '1' === $_POST['cfm_privacy'];

		// 6. Provjera podataka na serveru
		$errors = array();

		if ( mb_strlen( $name ) < 2 ) {
			$errors['cfm_name'] = 'Molimo unesite ispravno ime (najmanje 2 znaka).';
		}
		if ( mb_strlen( $surname ) < 2 ) {
			$errors['cfm_surname'] = 'Molimo unesite ispravno prezime (najmanje 2 znaka).';
		}
		if ( empty( $email ) || ! is_email( $email ) ) {
			$errors['cfm_email'] = 'Molimo unesite ispravnu e-mail adresu.';
		}
		if ( ! empty( $phone ) && ! preg_match( '/^[\+\d\s\-\(\)]{6,30}$/', $phone ) ) {
			$errors['cfm_phone'] = 'Molimo unesite ispravan broj telefona.';
		}
		if ( mb_strlen( $message ) < 10 ) {
			$errors['cfm_message'] = 'Molimo unesite poruku (najmanje 10 znakova).';
		}
		if ( ! $privacy ) {
			$errors['cfm_privacy'] = 'Morate prihvatiti uvjete privatnosti za nastavak.';
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		// 7. Increment rate-limit counter
		set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );

		// 8. Izradi i pošalji e-mail putem PHPMailera
		$to         = get_option( 'cfm_recipient_email', get_option( 'admin_email' ) );
		$subject    = get_option( 'cfm_email_subject', 'Nova poruka s kontaktnog obrasca' );
		$body       = $this->build_email_body( $name, $surname, $email, $phone, $message );
		$sent_error = $this->send_via_phpmailer( $to, $subject, $body, $name, $surname, $email );

		if ( null === $sent_error ) {
			// 9. Pošalji potvrdu posjetitelju
			$this->send_via_phpmailer(
				$email,
				'Potvrda primanja Vaše poruke — ' . get_bloginfo( 'name' ),
				$this->build_confirmation_email( $name, $surname ),
				get_option( 'cfm_smtp_from_name', get_bloginfo( 'name' ) ),
				'',
				get_option( 'cfm_smtp_from_email', get_option( 'admin_email' ) )
			);

			$success_msg = get_option(
				'cfm_success_message',
				'Hvala! Vaša poruka je uspješno poslana. Javit ćemo Vam se uskoro.'
			);
			wp_send_json_success( array( 'message' => $success_msg ) );
		} else {
			wp_send_json_error( array( 'message' => 'Žao nam je, poruka nije mogla biti poslana. Molimo pokušajte ponovo ili nas kontaktirajte izravno.', 'e' => $sent_error ) );
		}
	}

	// -------------------------------------------------------------------------
	// PHPMailer send
	// -------------------------------------------------------------------------

	/**
	 * Send an e-mail using PHPMailer with optional SMTP.
	 *
	 * @return null on success, string error message on failure.
	 */
	private function send_via_phpmailer( $to, $subject, $body, $sender_name, $sender_surname, $sender_email ) {

		$smtp_enabled    = (bool) get_option( 'cfm_smtp_enabled', 0 );
		$smtp_host       = get_option( 'cfm_smtp_host', '' );
		$smtp_port       = (int) get_option( 'cfm_smtp_port', 587 );
		$smtp_user       = get_option( 'cfm_smtp_username', '' );
		$smtp_pass       = get_option( 'cfm_smtp_password', '' );
		$smtp_enc        = get_option( 'cfm_smtp_encryption', 'tls' ); // 'tls', 'ssl', 'none'
		$from_email      = get_option( 'cfm_smtp_from_email', get_option( 'admin_email' ) );
		$from_name       = get_option( 'cfm_smtp_from_name', get_bloginfo( 'name' ) );

		try {
			$mail = new PHPMailer( true );

			if ( $smtp_enabled && ! empty( $smtp_host ) ) {
				$mail->isSMTP();
				$mail->Host       = $smtp_host;
				$mail->Port       = $smtp_port;

				if ( ! empty( $smtp_user ) ) {
					$mail->SMTPAuth   = true;
					$mail->Username   = $smtp_user;
					$mail->Password   = $smtp_pass;
				}

				if ( 'ssl' === $smtp_enc ) {
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				} elseif ( 'tls' === $smtp_enc ) {
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				} else {
					$mail->SMTPSecure = '';
					$mail->SMTPAutoTLS = false;
				}
			} else {
				// Fall back to PHP mail()
				$mail->isMail();
			}

			$mail->CharSet  = PHPMailer::CHARSET_UTF8;
			$mail->setFrom( $from_email, $from_name );
			$mail->addAddress( $to );
			$mail->addReplyTo( $sender_email, $sender_name . ' ' . $sender_surname );

			$mail->isHTML( true );
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = wp_strip_all_tags( $body );

			$mail->send();
			return null; // success

		} catch ( PHPMailerException $e ) {
			return $e->getMessage();
		}
	}

	// -------------------------------------------------------------------------
	// Email body
	// -------------------------------------------------------------------------

	private function build_email_body( $name, $surname, $email, $phone, $message ) {
		$site      = esc_html( get_bloginfo( 'name' ) );
		$date      = esc_html( current_time( 'd.m.Y. H:i' ) );
		$phone_out = ! empty( $phone ) ? esc_html( $phone ) : '<em>Nije navedeno</em>';
		$msg_out   = nl2br( esc_html( $message ) );

		return <<<HTML
<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<style>
  body{margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif}
  .wrap{max-width:620px;margin:30px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}
  .hdr{background:#1e3a5f;color:#fff;padding:26px 32px}
  .hdr h2{margin:0;font-size:20px;letter-spacing:.3px}
  .hdr p{margin:4px 0 0;font-size:13px;opacity:.75}
  .body{padding:28px 32px}
  .field{margin-bottom:22px}
  .lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#64748b;margin-bottom:5px}
  .val{font-size:15px;color:#1e293b;padding:10px 14px;background:#f8fafc;border-left:3px solid #3b82f6;border-radius:4px;line-height:1.5}
  .ftr{padding:14px 32px;background:#f8fafc;font-size:12px;color:#94a3b8;text-align:center;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h2>Nova poruka s kontaktnog obrasca</h2>
    <p>{$site} &bull; {$date}</p>
  </div>
  <div class="body">
    <div class="field">
      <div class="lbl">Puno ime</div>
      <div class="val">{$name} {$surname}</div>
    </div>
    <div class="field">
      <div class="lbl">E-mail adresa</div>
      <div class="val">{$email}</div>
    </div>
    <div class="field">
      <div class="lbl">Broj telefona</div>
      <div class="val">{$phone_out}</div>
    </div>
    <div class="field">
      <div class="lbl">Poruka</div>
      <div class="val">{$msg_out}</div>
    </div>
  </div>
  <div class="ftr">Ova e-mail poruka je automatski generirana od strane {$site}. Molimo nemojte odgovarati izravno.</div>
</div>
</body>
</html>
HTML;
	}

	private function build_confirmation_email( $name, $surname ) {
		$site        = esc_html( get_bloginfo( 'name' ) );
		$name_esc    = esc_html( $name );
		$surname_esc = esc_html( $surname );

		return <<<HTML
<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<style>
  body{margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif}
  .wrap{max-width:620px;margin:30px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}
  .hdr{background:#1e3a5f;color:#fff;padding:26px 32px}
  .hdr h2{margin:0;font-size:20px;letter-spacing:.3px}
  .hdr p{margin:4px 0 0;font-size:13px;opacity:.75}
  .body{padding:32px 32px;font-size:15px;color:#374151;line-height:1.7}
  .body p{margin:0 0 16px}
  .ftr{padding:14px 32px;background:#f8fafc;font-size:12px;color:#94a3b8;text-align:center;border-top:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h2>Potvrda primanja poruke</h2>
    <p>{$site}</p>
  </div>
  <div class="body">
    <p>Poštovani/a <strong>{$name_esc} {$surname_esc}</strong>,</p>
    <p>Hvala Vam na poruci! Uspješno smo primili Vaš upit i javit ćemo Vam se u najkraćem mogućem roku.</p>
    <p>Lijep pozdrav,<br><strong>{$site}</strong></p>
  </div>
  <div class="ftr">Ova e-mail poruka je automatski generirana. Molimo nemojte odgovarati na nju.</div>
</div>
</body>
</html>
HTML;
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	private function get_client_ip() {
		foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = trim( explode( ',', $_SERVER[ $key ] )[0] );
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return $ip;
				}
			}
		}
		return sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
	}

	// -------------------------------------------------------------------------
	// Admin settings page
	// -------------------------------------------------------------------------

	public function add_settings_page() {
		add_options_page(
			'Contact Form Mailer',
			'Contact Form',
			'manage_options',
			'contact-form-mailer',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		// General
		register_setting( 'cfm_settings_group', 'cfm_recipient_email',  array( 'sanitize_callback' => 'sanitize_email' ) );
		register_setting( 'cfm_settings_group', 'cfm_email_subject',    array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'cfm_settings_group', 'cfm_success_message',  array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
		// SMTP
		register_setting( 'cfm_settings_group', 'cfm_smtp_enabled',    array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_host',       array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_port',       array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_username',   array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_password',   array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_encryption', array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_from_email', array( 'sanitize_callback' => 'sanitize_email' ) );
		register_setting( 'cfm_settings_group', 'cfm_smtp_from_name',  array( 'sanitize_callback' => 'sanitize_text_field' ) );
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1>Contact Form Mailer Settings</h1>
			<p>Place the form anywhere with the shortcode: <code>[contact_form]</code></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'cfm_settings_group' ); ?>

				<h2 class="title">General</h2>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cfm_recipient_email">Recipient Email</label>
						</th>
						<td>
							<input type="email" id="cfm_recipient_email" name="cfm_recipient_email" class="regular-text"
								value="<?php echo esc_attr( get_option( 'cfm_recipient_email', get_option( 'admin_email' ) ) ); ?>" />
							<p class="description">All form submissions are sent to this address.</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="cfm_email_subject">Email Subject</label>
						</th>
						<td>
							<input type="text" id="cfm_email_subject" name="cfm_email_subject" class="regular-text"
								value="<?php echo esc_attr( get_option( 'cfm_email_subject', 'Nova poruka s kontaktnog obrasca' ) ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="cfm_success_message">Success Message</label>
						</th>
						<td>
							<textarea id="cfm_success_message" name="cfm_success_message" rows="3" class="large-text"
							><?php echo esc_textarea( get_option( 'cfm_success_message', 'Hvala! Vaša poruka je uspješno poslana. Javit ćemo Vam se uskoro.' ) ); ?></textarea>
							<p class="description">Displayed to the visitor after a successful submission.</p>
						</td>
					</tr>
				</table>

				<h2 class="title">PHPMailer / SMTP Configuration</h2>
				<p class="description">Leave SMTP disabled to use the server&rsquo;s default PHP <code>mail()</code> function via PHPMailer.</p>
				<table class="form-table">
					<tr>
						<th scope="row">Enable SMTP</th>
						<td>
							<label>
								<input type="checkbox" name="cfm_smtp_enabled" value="1"
									<?php checked( 1, get_option( 'cfm_smtp_enabled', 0 ) ); ?> />
								Send mail via SMTP server
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_host">SMTP Host</label></th>
						<td>
							<input type="text" id="cfm_smtp_host" name="cfm_smtp_host" class="regular-text"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_host', '' ) ); ?>"
								placeholder="smtp.example.com" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_port">SMTP Port</label></th>
						<td>
							<input type="number" id="cfm_smtp_port" name="cfm_smtp_port" class="small-text"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_port', 587 ) ); ?>"
								min="1" max="65535" />
							<p class="description">Common ports: 25 (plain), 465 (SSL), 587 (STARTTLS / TLS).</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_encryption">Encryption</label></th>
						<td>
							<select id="cfm_smtp_encryption" name="cfm_smtp_encryption">
								<?php
								$enc = get_option( 'cfm_smtp_encryption', 'tls' );
								foreach ( array(
									'tls'  => 'STARTTLS (recommended)',
									'ssl'  => 'SSL / TLS',
									'none' => 'None',
								) as $val => $label ) :
									?>
									<option value="<?php echo esc_attr( $val ); ?>"
										<?php selected( $enc, $val ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_username">SMTP Username</label></th>
						<td>
							<input type="text" id="cfm_smtp_username" name="cfm_smtp_username" class="regular-text"
								autocomplete="off"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_username', '' ) ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_password">SMTP Password</label></th>
						<td>
							<input type="password" id="cfm_smtp_password" name="cfm_smtp_password" class="regular-text"
								autocomplete="new-password"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_password', '' ) ); ?>" />
							<p class="description">Stored in the database. For higher security consider using environment variables or a secrets plugin.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_from_email">From Email</label></th>
						<td>
							<input type="email" id="cfm_smtp_from_email" name="cfm_smtp_from_email" class="regular-text"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_from_email', get_option( 'admin_email' ) ) ); ?>" />
							<p class="description">Must match the authenticated SMTP account to avoid rejection.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cfm_smtp_from_name">From Name</label></th>
						<td>
							<input type="text" id="cfm_smtp_from_name" name="cfm_smtp_from_name" class="regular-text"
								value="<?php echo esc_attr( get_option( 'cfm_smtp_from_name', get_bloginfo( 'name' ) ) ); ?>" />
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

Contact_Form_Mailer::get_instance();
