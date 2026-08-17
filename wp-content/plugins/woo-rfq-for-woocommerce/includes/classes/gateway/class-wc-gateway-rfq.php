<?php

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;


/**
 * Created by PhpStorm.
 * User: cneah
 * Date: 1/10/2016
 * Time: 2:57 PM
 */

class WC_Gateway_RFQ
{
    /**
     * Constructor
     */
    public function __construct()
    {


        if (function_exists('is_user_logged_in')) {

            if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {

                $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
                $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";
                $GLOBALS["hide_for_visitor"] = "yes";

            } else {
                $GLOBALS["hide_for_visitor"] = "no";
            }
        } else {
            $GLOBALS[ "hide_for_visitor"] = "no";
        }

        if(  $GLOBALS["gpls_woo_rfq_checkout_option"] == 'rfq')
        {


            add_filter('woocommerce_payment_gateways', 'add_gpls_woo_rfq_class',1,1);

            add_action('init', 'init_gpls_rfq_payment_gateway');


             add_filter('woocommerce_available_payment_gateways', 'gpls_rfq_remove_other_payment_gateways', 1000, 1);
          //  add_filter('woocommerce_payment_gateways', 'gpls_rfq_remove_other_block_payment_gateways',1000,1);



        }

    }

}

?>