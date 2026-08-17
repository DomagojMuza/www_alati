<?php

/**
 * Main class
 *
 */


if (!class_exists('gpls_woo_rfq_shipping')) {

    class gpls_woo_rfq_shipping
    {
        public function __construct()
        {




            //add_filter('woocommerce_cart_ready_to_calc_shipping', array($this,'gpls_woo_rfq_enable_shipping_calc'),1000,1);

            //add_filter( 'option_woocommerce_enable_shipping_calc', array($this,'gpls_woo_rfq_enable_shipping_calc'),1000,1 );

            //add_filter( 'woocommerce_cart_needs_shipping', array($this,'gpls_woo_rfq_enable_shipping_calc'),1000,1 );

//            add_filter( 'woocommerce_validate_postcode', array($this,'gpls_woo_rfq_validate_postcode'),1000,3 );




        }




        public function gpls_woo_rfq_enable_shipping_calc($show_shipping)
        {


            $temp_show_shipping = $show_shipping;

            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                $temp_show_shipping = false;

            }
            if(function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $temp_show_shipping = false;
                }
            }

            $temp_show_shipping = apply_filters('gpls_woo_rfq_show_shipping',$temp_show_shipping, $show_shipping);

            return $temp_show_shipping;

        }
    }
}
