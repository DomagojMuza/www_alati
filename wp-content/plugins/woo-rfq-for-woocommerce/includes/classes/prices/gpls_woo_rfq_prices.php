<?php

/**
 * Main class
 *
 */


if (!class_exists('gpls_woo_rfq_prices')) {


    class gpls_woo_rfq_prices
    {


        public function __construct()
        {

            $proceed = "no";

            $proceed = apply_filters('gpls_woo_rfq_prices_proceed', $proceed);

            do_action('do_before_gpls_woo_rfq_prices', $proceed);

            if (!is_admin() || $proceed == "yes") {


                $rfq_check = false;
                $normal_check = false;

                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
                    add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);

                    $rfq_check = true;
                    $normal_check = false;

                    if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'yes') {
                        $rfq_check = false;
                        $normal_check = true;
                    }
                }

                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
                    $rfq_check = false;
                    $normal_check = true;
                }
                if (function_exists('is_user_logged_in')) {
                    if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                        $rfq_check = true;
                        $normal_check = false;

                    }
                }


                if ($rfq_check == true) {

                     $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";

                    //if(!is_admin())
                    {

                        add_action('init', 'gpls_woo_rfq_init_wp_enqueue_scripts', 1000);

                        if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no'
                            ||  $GLOBALS["hide_for_visitor"] === "yes") {
                            if (function_exists('wc_get_page_id')
                                && function_exists('gpls_woo_rfq_is_account_page')&& !gpls_woo_rfq_is_account_page()) {
                                add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_cart_custom_css', 1000);
                            }
                        }
                    }



                    //    add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2);

                    //    add_filter( 'woocommerce_variation_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2 );

                    //    add_filter( 'woocommerce_cart_needs_payment','gpls_woo_rfq_cart_needs_payment',1000,2);

                    $this->price_handling_function_for_rfq_only();

                }

                if ($normal_check == true) {

                    add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);
                    add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);
                }

                add_filter('woocommerce_coupons_enabled', 'gpls_woo_rfq_woocommerce_coupons_enabled', 10, 1);

                $this->price_handling_function_for_rfq_and_normal();



                if ((isset( $GLOBALS["gpls_woo_rfq_show_prices"])&& $GLOBALS["gpls_woo_rfq_show_prices"] == 'no')
                    || (isset( $GLOBALS["hide_for_visitor"])&& $GLOBALS["hide_for_visitor"] === "yes")
                    || (function_exists('wc_get_page_id')
                        && function_exists('gpls_woo_rfq_is_account_page')&&gpls_woo_rfq_is_account_page()))
                {

                    $this->subtotals_and_totals_for_rfq_and_normal();


                }


                //if(!is_admin())
                {
                    if (((isset( $GLOBALS["gpls_woo_rfq_show_prices"])&& $GLOBALS["gpls_woo_rfq_show_prices"] == 'no') ||
                        (isset( $GLOBALS["hide_for_visitor"])&& $GLOBALS["hide_for_visitor"] === "yes"))
                        && (isset( $GLOBALS["gpls_woo_rfq_checkout_option"])
                            &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal")
                    ) {

                        if (function_exists('wc_get_page_id') &&
                            function_exists('gpls_woo_rfq_is_account_page')&& !gpls_woo_rfq_is_account_page()) {
                            add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_cart_custom_css', 1000);
                        }
                    }
                }

                if (get_option('settings_gpls_woo_rfq_show_cart_link_archive_top') == "yes") {
                    add_action('woocommerce_archive_description', 'gpls_woo_rfq_before_main_content', 100);
                }

                if (get_option('settings_gpls_woo_rfq_show_cart_link_archive_end') == "yes") {
                    add_action('woocommerce_after_main_content', 'gpls_woo_rfq_before_main_content', 100);
                }

                if (get_option('settings_gpls_woo_rfq_show_cart_link_cart') == "yes") {
                    add_action('woocommerce_before_cart', 'gpls_woo_rfq_before_main_content', 1);
                    add_action('woocommerce_cart_is_empty', 'gpls_woo_rfq_before_main_content', 1);
                }


                if (get_option('settings_gpls_woo_rfq_show_cart_single_page') == "yes") {
                    add_action('woocommerce_before_single_product', 'gpls_woo_rfq_before_main_content', 100);
                }

                /*if (get_option('settings_gpls_woo_rfq_show_cart_thank_you_page') == "yes") {

                     add_action('woocommerce_thankyou', 'gpls_woo_rfq_woocommerce_thankyou', 100, 1);

                 }*/

                add_action('woocommerce_cart_emptied', 'gpls_woo_rfq_filter_check', 100);
                add_action('gpls_woo_rfq_before_thankyou', 'gpls_woo_rfq_filter_check', 100);

                //checking that nonce does NOT exist//
                // phpcs:disable
                if (// phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    !isset($_POST['gpls-woo-rfq_checkout']) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    &&
                    !isset($_POST['woocommerce-process-checkout-nonce']))// phpcs:ignore WordPress.Security.NonceVerification.Recommended
                {
                    add_filter('woocommerce_get_price_excluding_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);
                    add_filter('woocommerce_get_price_including_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);
                }
                // phpcs:enable
            }

            do_action('do_after_gpls_woo_rfq_prices', $proceed);
        }

        public function price_handling_function_for_rfq_only()
        {


            if (((isset($GLOBALS["gpls_woo_rfq_show_prices"]) && $GLOBALS["gpls_woo_rfq_show_prices"] == 'no')
                    || (isset($GLOBALS["hide_for_visitor"])&&$GLOBALS["hide_for_visitor"] === "yes"))
            && (isset($GLOBALS["gpls_woo_rfq_checkout_option"])&&$GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) { // it was normal_checkout




                add_filter('woocommerce_cart_totals_order_total_html', 'gpls_woo_rfq_total_prices', 1000);

                add_filter('woocommerce_cart_item_price', 'gpls_woo_rfq_hide_cart_prices', 1000, 3);

                add_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_hide_woocommerce_cart_product_price', 1000, 2);

                add_filter('woocommerce_cart_product_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_product_subtotal', 1000, 3);

                add_filter('woocommerce_cart_item_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_item_subtotal', 1000, 3);

                add_filter('woocommerce_cart_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_subtotal', 1000, 3);
                add_filter('woocommerce_cart_totals_taxes_total_html', 'gpls_woo_rfq_total_prices');
                add_filter('woocommerce_cart_totals_fee_html', 'gpls_woo_rfq_woocommerce_cart_totals_fee_html', 1000, 2);

            }


            //add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_woocommerce_order_needs_payment', 1000, 2);
            add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);


            add_filter('wc_add_to_cart_message_html', 'gpls_woo_rfq_remove_cart_notices', 1, 3);

            $remove_totals = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
                //if (get_option('settings_gpls_woo_rfq_show_prices','no') == 'no' )
                {

                    $remove_totals = true;
                }
            }
            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $remove_totals = true;

                }
            }


            if ($remove_totals == true) {

                //  remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10);
                //  add_action('woocommerce_after_cart', 'woocommerce_button_proceed_to_checkout', 20);

            }


            add_action('woocommerce_before_calculate_totals', 'gpls_woo_rfq_remove_filters', -1000);


        }


        public function price_handling_function_for_rfq_and_normal()
        {

            add_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_get_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);


            add_filter('woocommerce_bundle_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_bundle_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_grouped_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_bundled_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);


            add_filter('woocommerce_variation_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_variable_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_free_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
            add_filter('woocommerce_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);            //
            add_filter('woocommerce_get_variation_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);

            add_filter('woocommerce_product_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
            add_filter('woocommerce_bundle_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);

            add_filter('woocommerce_get_variation_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
            add_filter('woocommerce_get_variation_sale_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
            add_filter('woocommerce_get_variation_regular_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);

        }

        public function subtotals_and_totals_for_rfq_and_normal()
        {
            if (class_exists('GPLS_WOO_RFQ_PLUS'))
            {
                $show_prices_in_my_account = get_option('settings_gpls_woo_rfq_show_prices_in_my_account', 'no');

                if ($show_prices_in_my_account == 'yes' || gpls_woo_rfq_plus_check_staff_mode() == "yes" ) {
                  return;
                }
            }
            // if (( $GLOBALS["gpls_woo_rfq_show_prices"] == 'no' ||  $GLOBALS["hide_for_visitor"] === "yes") )
            {

                add_filter('woocommerce_order_formatted_line_subtotal', 'gpls_woo_rfq_order_formatted_line_subtotal', 100, 3);
                add_filter('woocommerce_get_formatted_order_total', 'gpls_woo_rfq_get_formatted_order_total', 100, 2);
                add_filter('woocommerce_get_order_item_totals', 'gpls_woo_rfq_woocommerce_get_order_item_totals', 100, 2);
            }

        }

        /**
         * Hides the 'Free!' price notice
         */
        public function hide_simple_free_price_notice($price, $product)
        {
            $price = false;
            return $price;
        }

        public function hide_free_price_notice($product)
        {

            return false;
        }

    }
}
