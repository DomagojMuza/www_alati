<?php

/**

 * Author:   @Neah Plugins
 * Date: 1/10/2016
 * Time: 2:57 PM
 */
class  WC_Email_RFQ
{
    /**
     * Constructor
     */
    public function __construct()
    {
//   WordPress.WP.I18n.NonSingularStringLiteralText
        //add_action( 'plugins_loaded', array($this,'gpls_rfq_register_email') ,1000);
        add_action( 'init', array($this,'gpls_rfq_register_email') ,1000);
        $this->gpls_rfq_register_email();

    }

    public function gpls_rfq_register_email(){

        add_filter( 'woocommerce_locate_core_template',array( $this, 'gpls_woo_rfq_pay_woocommerce_locate_template' ), 1000, 4 );
        add_filter('woocommerce_locate_template', array($this, 'gpls_woo_rfq_locate_template'), 1, 3);

        add_filter('woocommerce_email_classes', array($this,'gpls_rfq_setup_new_requests_emails'),10000,1);
        add_filter( 'woocommerce_email_actions', array($this,'gpls_qbb_quote_request_filter_actions'),10000,1 );

        add_action( 'woocommerce_order_status_gplsquote-req', array( $this, 'send_transactional_email' ), 10000, 2 );

        add_action( 'gpls_woo_rfq_order_item_product_show_price', array( $this, 'filter_gpls_woo_rfq_order_item_product_show_price' ), 1, 2 );

// add the filter
        add_filter( 'woocommerce_resend_order_emails_available', array( $this,'filter_woocommerce_resend_order_emails_available'), 100, 1 );


        add_filter('woocommerce_email_order_meta_fields', array($this, 'woocommerce_email_order_meta_fields_notes'), 100, 3);


        add_filter('woocommerce_email_headers', array($this, 'gpls_rfq_add_reply_to_admin_order'), PHP_INT_MAX - 1, 3);




    }



    public function gpls_rfq_add_reply_to_admin_order($header, $id, $order)
    {
        $header = 'Content-Type: text/html' . "\r\n";

        if ($id == 'new_rfq' && $order) {

            $reply_to_email = $order->get_billing_email();

            if ($order && $order->get_billing_email() && ($order->get_billing_first_name() || $order->get_billing_last_name())) {
                $header .= 'Reply-to: ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . ' <' . $order->get_billing_email() . ">\r\n";
            }

        }


        return $header;
    }



public function woocommerce_email_order_meta_fields_notes($array, $sent_to_admin, $order){
    $fields = $array;

    if (is_object($order) && $order && $order->get_customer_note() ) {
        $fields['customer_note'] = array(
            'label' => __( 'Note', 'woo-rfq-for-woocommerce' ),
            'value' => wptexturize( $order->get_customer_note() ),
        );
    }
    return $fields;
}


public function filter_woocommerce_resend_order_emails_available( $array ) {
        // make filter magic happen here...
        array_push($array,'new_rfq','customer_rfq');
        return $array;
    }

    public function send_transactional_email( $args = array(), $message = '' ) {
        global $woocommerce;

        $woocommerce->mailer();

        do_action( current_filter() . '_notification', $args, $message );
    }

    /**
     * Register "woocommerce_order_status_pending_to_quote" as an email trigger
     */

    public function gpls_qbb_quote_request_filter_actions( $actions ){
       // $actions[] = "woocommerce_new_quote_created";
        return $actions;
    }

    public function gpls_rfq_setup_new_requests_emails($emails)
    {
        $emails['WC_Email_Customer_RFQ'] = include(gpls_woo_rfq_DIR . 'includes/classes/emails/class-wc-email-customer-rfq.php');
        $emails['WC_Email_New_RFQ'] = include(gpls_woo_rfq_DIR . 'includes/classes/emails/class-wc-email-new-rfq.php');

        return $emails;
    }
    public function gpls_woo_rfq_pay_woocommerce_locate_template($template, $template_name, $template_path,$id)
    {

        $ids = array('new_rfq','customer_rfq','new_pdf_request','admin_note'
        ,'new_vendor_rfq','gpls_prod_marketing','customer_rfq_sent','gpls_admin_upload');


        if(!in_array($id,$ids)){
            return $template;
        }

        $_template = $template;

        if ( ! $template_path )
            $template_path = WC()->template_url;

        $plugin_path  = gpls_woo_rfq_DIR  . 'woocommerce/';

        //
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if( ! $template && file_exists( $plugin_path . $template_name ) )
            $template = $plugin_path . $template_name;

        if ( ! $template )
            $template = $_template;
        // d($template);
        return $template;
        // Return what we found

    }


    public function gpls_woo_rfq_locate_template($template, $template_name, $template_path)
    {

        $template_names = array('admin-note.php','customer-rfq-sent.php','product-marketing.php'
        ,'rfqtk-email-order-items.php','vendor-new-rfq.php',

        'cart-pdf-admin.php',

        'admin-upload-note.php',

        'admin-new-rfq.php','customer-note.php','customer-rfq.php','subscription-info.php'
            );

        $cart_array= array('proceed-to-checkout-button.php');


        if(substr($template_name, 0, strlen('emails')) === 'emails')
        {
            if(!in_array(str_replace("emails/","",$template_name),$template_names)){

                return $template;
            }

        }

        else if(substr($template_name, 0, strlen('cart')) === 'cart')
        {
            if(!in_array(str_replace("cart/","",$template_name),$cart_array)){

                return $template;
            }

        }else{
            $len = strlen('woo-rfq');

            if(substr($template_name, 0, $len) !== 'woo-rfq')
            {

                return $template;
            }

        }
        $_template = $template;

        if ( ! $template_path )
            $template_path = WC()->template_url;

        $plugin_path  = gpls_woo_rfq_DIR  . 'woocommerce/';

        //
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if( ! $template && file_exists( $plugin_path . $template_name ) )
            $template = $plugin_path . $template_name;

        if ( ! $template )
            $template = $_template;
        // d($template);
        return $template;
        // Return what we found

    }


}
if(!function_exists('rfqtk_get_email_order_items')) {
    function rfqtk_get_email_order_items($order, $args = array())
    {
        ob_start();

        $defaults = array(
            'show_sku' => false,
            'show_image' => false,
            'image_size' => array(128, 128),
            'plain_text' => false,
            'sent_to_admin' => false,
            'hide_admin' => false,
        );

        $args = wp_parse_args($args, $defaults);
        $template = $args['plain_text'] ? 'emails/plain/rfqtk-email-order-items.php' : 'emails/rfqtk-email-order-items.php';

        wc_get_template($template, apply_filters('woocommerce_email_order_items_args', array(
            'order' => $order,
            'items' => $order->get_items(),
            'show_download_links' => $order->is_download_permitted() && !$args['sent_to_admin'],
            'show_sku' => $args['show_sku'],
            'show_purchase_note' => $order->is_paid() && !$args['sent_to_admin'],
            'show_image' => $args['show_image'],
            'image_size' => $args['image_size'],
            'plain_text' => $args['plain_text'],
            'sent_to_admin' => $args['sent_to_admin'],
            'hide_admin' => $args['hide_admin'],
        )),'', gpls_woo_rfq_DIR . 'woocommerce/');

        return apply_filters('woocommerce_email_order_items_table', ob_get_clean(), $order);
    }
}

?>