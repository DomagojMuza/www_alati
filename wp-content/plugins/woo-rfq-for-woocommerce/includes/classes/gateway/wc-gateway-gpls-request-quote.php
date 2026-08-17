<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (!class_exists('WC_Gateway_GPLS_Request_Quote') && class_exists('WC_Payment_Gateway')) {


    class WC_Gateway_GPLS_Request_Quote extends WC_Payment_Gateway
    {
        private string $instructions;

        /**
         * Constructor for the gateway.
         */
        public function __construct()
        {
           $this->is_available();
            $this->id = 'gpls-rfq';
            $this->icon = apply_filters('gpls_rfq_icon','');
            $this->has_fields = false;
            $this->method_title = __('Quote Request', 'woo-rfq-for-woocommerce');
            $this->method_description = __('Allows RFQ to go through.', 'woo-rfq-for-woocommerce');

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title',$this->title);
            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions');

            $this->supports           = array(
                'products',
                'subscriptions',
                'subscription_cancellation',
                'subscription_suspension',
                'subscription_reactivation',
                'subscription_amount_changes',
                'subscription_date_changes',
                'multiple_subscriptions'
            );
            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));


              add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));


            // Customer Emails
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields()
        {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woo-rfq-for-woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Skip Payment For Quote Requests', 'woo-rfq-for-woocommerce'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('Title', 'woo-rfq-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woo-rfq-for-woocommerce'),
                    'default' => __('Request For Quote', 'woo-rfq-for-woocommerce'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'woo-rfq-for-woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woo-rfq-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'woo-rfq-for-woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'woo-rfq-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page()
        {
            $order=false;


            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (isset($_GET['key'])) // phpcs: WordPress.Security.NonceVerification.Recommended
            {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $order_key = sanitize_text_field( wp_unslash( $_GET['key'] ?? '' ) );// phpcs:ignore WordPress.Security.NonceVerification.Recommended

                $order_id = wc_get_order_id_by_order_key($order_key);
                $order = wc_get_order($order_id);

            }

            if ($order && !$order->get_meta('_gpls_thx_visited')&& $this->instructions) {
                echo wp_kses_post(wpautop(wptexturize($this->instructions)));
                $order->add_meta_data('_gpls_thx_visited','yes');
                $order->save_meta_data();

            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {
           // $order = new WC_Order();
            if ($this->instructions && !$sent_to_admin && 'gpls-rfq' === $order->get_payment_method() && $order->has_status('wc-gplsquote-req')) {
                echo wp_kses(wpautop(wptexturize($this->instructions)) . PHP_EOL,
                    wp_kses_allowed_html( 'post' ));
            }
        }

        /**
         * Process the payment and return the result
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {

            $order = wc_get_order($order_id);

            $order->update_status('wc-gplsquote-req', __('RFQ', 'woo-rfq-for-woocommerce'));

            // Reduce stock levels

            //TODO check here for the option to reduce stocks
            //wc_reduce_stock_levels($order_id);


            // Remove cart
            WC()->cart->empty_cart();

          //  gpls_woo_rfq_handle_checkout();
            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );


        }


    }


}
