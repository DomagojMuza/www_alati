<?php

/**
 * Main class
 *
 */

if (!class_exists('gpls_woo_rfq_coupons')) {

    class gpls_woo_rfq_coupons
    {
        public function __construct()
        {



           // if(  $GLOBALS["gpls_woo_rfq_checkout_option"] == 'rfq')
            {
              //  add_filter('woocommerce_checkout_coupon_message', array($this, 'gpls_woo_rfq_hide_woocommerce_checkout_coupon_message'));
            }



        }

        public function gpls_woo_rfq_hide_woocommerce_checkout_coupon_message()
        {

            if (get_option( 'settings_gpls_woo_rfq_show_applied_coupons','no' ) === 'no' ) {
                return '';
            }
        }









    }
}
