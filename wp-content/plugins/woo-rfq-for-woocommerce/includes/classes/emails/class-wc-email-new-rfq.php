<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_New_RFQ' ) ) :

/**
 * New RFQ Email
 *
 * An email sent to the admin when a new RFQ is received.
 *
 * @class       WC_Email_New_Order
 * @extends     WC_Email
 */
    #[\AllowDynamicProperties]
class WC_Email_New_RFQ extends WC_Email {
    public $_templates;
	/**
	 * Constructor
	 */
        //   WordPress.WP.I18n.NoEmptyStrings
        //   WordPress.WP.I18n.NonSingularStringLiteralText
	public function __construct() {

		$this->id               = 'new_rfq';
		$this->title            = __( 'RFQ-ToolKit New RFQ Admin', 'woo-rfq-for-woocommerce' );
        /* translators: description . */
		$this->description      = __( 'New quote request emails are sent to the recipient list when an order is received.', 'woo-rfq-for-woocommerce' );

		//$this->heading          = __( 'New customer quote request', 'woo-rfq-for-woocommerce' );


		$this->template_html    = 'emails/admin-new-rfq.php';
		$this->template_plain   = 'emails/plain/admin-new-rfq.php';

		$this->_templates = array($this->template_html,$this->template_plain);
        /* translators: placeholders date and number . */
        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
        );


		add_filter('woocommerce_template_directory',array( $this, 'gpls_rfq_woocommerce_locate_template_dir' ), 10, 2);

        if(!has_action('woocommerce_order_status_gplsquote-req_notification', array($this, 'trigger'), 100, 2)) {
            add_action('woocommerce_order_status_gplsquote-req_notification', array($this, 'trigger'), 100, 2);
        }


		// Call parent constructor
		parent::__construct();

		// Other settings

		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );





	}

    public function get_default_subject()
    {
        /* translators: placeholders site_title and number . */
        return __('{site_title} New customer quote request #({order_number}) on {order_date}', 'woo-rfq-for-woocommerce');
    }


    public function get_default_heading() {
        return __( 'New customer quote request', 'woo-rfq-for-woocommerce' );;
    }

	public function gpls_rfq_woocommerce_locate_template_dir($dir,$template)
	{

			return $dir;

	}

	/**
	 * Trigger.
	 */
	public function trigger( $order_id, $order = false  ) {



        if (defined("WC_Email_New_RFQ" . $order_id) || !$this->get_recipient()) return;

            define("WC_Email_New_RFQ" . $order_id, true);

            if ($order_id) {
                $this->object = wc_get_order($order_id);

                $this->placeholders['{order_date}'] = wc_format_datetime($this->object->get_date_created());
                $this->placeholders['{order_number}'] = $this->object->get_order_number();

            }



        $recipient =  apply_filters('wc_email_new_rfq_recipients',$this->get_recipient(),$this->object);


        if ($this->is_enabled() && $recipient) {
            $this->send($recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }


	}
    function replace_placeholders($order_id)
    {

        if (defined("WC_Email_New_RFQ_EMAIL" . $order_id)) return;

        define("WC_Email_New_RFQ_EMAIL" . $order_id, true);

        require_once(WC()->plugin_path() . '/includes/emails/class-wc-email.php');
        require_once(WC()->plugin_path() . '/includes/class-wc-emails.php');

        $WC_Emails = WC_Emails::instance();


        $this->setup_locale();

        if ($order_id) {
            $this->object = wc_get_order($order_id);
            $this->recipient = $this->object->get_billing_email();

            $this->placeholders['{order_date}'] = wc_format_datetime($this->object->get_date_created());
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
            $this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();

        }

        $this->restore_locale();

    }

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {

ob_start();

		wc_get_template( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
            'additional_content' => is_callable(array($this, 'get_additional_content'))?$this->get_additional_content():"",
			'sent_to_admin' => true,
			'plain_text'    => false,
            'email'			=> $this,
		) ,'',gpls_woo_rfq_DIR . 'woocommerce/');

        return ob_get_clean();
	}



	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {

        require_once(gpls_woo_rfq_DIR . 'includes/classes/emails/NP_Html2Text.php');

        $html_str= wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
            'additional_content' => is_callable(array($this, 'get_additional_content'))?$this->get_additional_content():"",
			'sent_to_admin' => true,
			'plain_text'    => true,
            'email'			=> $this,

		) ,'',gpls_woo_rfq_DIR . 'woocommerce/');

        $html = new NP_Html2Text($html_str);

        return $html->getText();
	}


	/**
	 * Initialise settings form fields
	 */
	public function init_form_fields() {
        /* translators: Available placeholders . */
        $placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woo-rfq-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );

        $this->form_fields = array(
			'enabled' => array(
				'title'         => __( 'Enable/Disable', 'woo-rfq-for-woocommerce' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'woo-rfq-for-woocommerce' ),
				'default'       => 'yes'
			),
			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'woo-rfq-for-woocommerce' ),
				'type'          => 'text',
                /* translators: recipients . */
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woo-rfq-for-woocommerce' ), esc_attr( get_option('admin_email') ) ),
				'placeholder'   => '',
				'default'       => get_option('admin_email')
			),
			'subject' => array(
				'title'         => __( 'Subject', 'woo-rfq-for-woocommerce' ),
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'css'         => 'width:600px',
				'default'       => ''
			),
            'heading'            => array(
                'title'       => __( 'Email heading', 'woo-rfq-for-woocommerce' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'css'         => 'width:600px',
                'default'     => '',
            ),
            'additional_content' => array(
                'title'       => __( 'Additional content', 'woo-rfq-for-woocommerce' ),
                'description' => __( 'Text to appear below the main email content.', 'woo-rfq-for-woocommerce' ),
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __( 'N/A', 'woo-rfq-for-woocommerce' ),
                'type'        => 'textarea',
                'default'     => '',
                'desc_tip'    => true,
            ),
			'email_type' => array(
				'title'         => __( 'Email type', 'woo-rfq-for-woocommerce' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woo-rfq-for-woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options()
			)
		);
	}
}

endif;

return new WC_Email_New_RFQ();
