<?php

use Automattic\WooCommerce\Utilities\OrderUtil;


if (!defined('ABSPATH'))
    exit;

/**
 * Main class
 *
 */
//   WordPress.WP.I18n.NoEmptyStrings
//   WordPress.WP.I18n.NonSingularStringLiteralText
if (!class_exists('gpls_woo_rfq_functions')) {

    class gpls_woo_rfq_functions
    {
        function __construct()
        {

        }
    }

    function is_hpos()
    {
        $is_hpos = OrderUtil::custom_orders_table_usage_is_enabled();

        return $is_hpos;
    }


    function gpls_woo_rfq_woocommerce_order_needs_payment($needs_payment, $cart)
    {

        return true;
    }


    function gpls_woo_add_rfq_cart_custom_css()
    {

        if (!is_admin()) {

            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));
            $custom_css = ".bundle_price { visibility: collapse !important; }";
            wp_add_inline_style('gpls_woo_rfq_css', $custom_css);
        }

    }

    function gpls_woo_add_rfq_cart_custom_js()
    {
        if (!is_admin()) {
            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);


        }
    }


    function gpls_woo_add_rfq_change_text_custom_js()
    {
        /* if (!is_admin()) {
             $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
             $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
             wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10,100000), true);

             $update_rfq_cart_button = get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button', __('Update Cart', 'woo-rfq-for-woocommerce'));

             $custom_js = "jQuery( window ).on('load', function() {";

             $custom_js .= "jQuery(\".actions [name='update_cart']\").text('" . $update_rfq_cart_button . "');";
             $custom_js .= "jQuery(\".actions [name='update_cart']\").val('" . $update_rfq_cart_button . "');";
             $custom_js .= "});";

             wp_add_inline_script('gpls_woo_rfq_js', $custom_js);

         }*/
    }

    function gpls_woo_add_rfq_change_text_footer_rfq()
    {
        if (!is_admin()) {


            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/rfq_dummy.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/rfq_dummy.js';
            wp_enqueue_script('rfq_dummy_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $update_rfq_cart_button = get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button', '');


            if ($update_rfq_cart_button != '') {

                $custom_js = "jQuery( document ).bind('ready ajaxComplete', function() {";
                $custom_js .= "jQuery(\".actions [name='update_cart']\").text('" . $update_rfq_cart_button . "');";
                $custom_js .= "jQuery(\".actions [name='update_cart']\").val('" . $update_rfq_cart_button . "');";
                $custom_js .= "jQuery(\".actions [name='update_cart']\").show();";
                $custom_js .= "});";

            } else {
                $custom_js = "jQuery( document ).bind('ready ajaxComplete', function() {";
                $custom_js .= "jQuery(\".actions [name='update_cart']\").show();";
                $custom_js .= "});";
            }

            $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', '');


            if ($view_your_cart_text != '') {

                $custom_js .= "jQuery(document).bind('wc_fragments_loaded ajaxComplete', function(){
        jQuery('.added_to_cart').text('" . $view_your_cart_text . "');});";

                $custom_js .= " jQuery('.woocommerce-message .wc-forward').text('" . $view_your_cart_text . "');";

            }

            wp_add_inline_script('rfq_dummy_js', $custom_js);

        }


    }

    function gpls_woo_add_rfq_change_text_footer_normal()
    {
        if (!is_admin()) {

            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/rfq_dummy.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/rfq_dummy.js';
            wp_enqueue_script('rfq_dummy_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $update_rfq_cart_button =
                get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button'
                    , __('Update Quote Request', 'woo-rfq-for-woocommerce'));


            if ($update_rfq_cart_button != '') {
                $custom_js = "jQuery(document).bind('ready ajaxComplete', function() {";
                $custom_js .= "jQuery(\".actions [name='update_cart']\").show();";
                $custom_js .= "});";
            }

            wp_add_inline_script('rfq_dummy_js', $custom_js);

        }
    }


    function gpls_woo_add_rfq_mode_remove_subtotals_custom_css()
    {
        if (!is_admin()) {
            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));
            $custom_css = ".site-header .widget_shopping_cart p.total,.cart-subtotal,.tax-rate,.tax-total,.order-total,.product-price,.product-subtotal { visibility: collapse }";
            wp_add_inline_style('gpls_woo_rfq_css', $custom_css);
        }

    }

    function gpls_woo_add_rfq_cart_update_custom_js()
    {

        if (!is_admin()) {
            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $checkout_page_title_option = get_option('settings_gpls_woo_rfq_rfq_checkout_page_title_option');
            $custom_js = "jQuery(window).on('load',function(){jQuery('.rfq_checkout_form').hide();});";

            if (!is_user_logged_in()) {
                //ship-to-different-address-checkbox
                $custom_js = $custom_js . "";
            }

            wp_add_inline_script('gpls_woo_rfq_js', $custom_js);
        }

    }

    function gpls_woo_rfq_init_wp_enqueue_scripts()
    {
        if (!is_admin()) {
            //    add_action('wp_enqueue_scripts', 'rfq_cart_wordings_ajax_added_to_cart', 1000);
            //   add_action('wp_print_scripts', 'rfq_cart_wordings_ajax_added_to_cart', 1000);
            //      add_action('wp_print_footer_scripts', 'rfq_cart_wordings_ajax_added_to_cart', 1000);
        }

    }

    function rfq_cart_wordings_ajax_added_to_cart()
    {

        if (!is_admin()) {
            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View cart', 'woo-rfq-for-woocommerce'));


            $custom_js = "jQuery(document.body).on('wc_fragments_loaded', function(){
        jQuery('.added_to_cart').text('" . $view_your_cart_text . "');});";

            if (!is_user_logged_in()) {
                //ship-to-different-address-checkbox
                $custom_js = $custom_js . "";
            }

            wp_add_inline_script('gpls_woo_rfq_js', $custom_js);
        }

    }


    function gpls_woo_rfq_individual_price_hidden_tax($price, $qty, $product)
    {


        if ($product->get_type() == 'external') {
            return $price;
        }

        $temp_price = $price;

        $p = false;

        //    global $product;

        $rfq_enable = false;

        if (isset($product) && is_object($product)) {
            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }

            $type = $product->get_type();
            if ($type == 'simple' || $type == 'variable') {
                if (trim($this_price) === '') {
                    //  $temp_price = $p;
                }
            }

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {


                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {
                            // echo 'individual_price_hidden $price = 0'.'<br />';
                            $temp_price = $p;
                        } else {
                            if (!isset($price) || trim($price) == '' || $price == 0) {
                                //  $temp_price = $p;
                            }
                        }

                        break;
                }
            }

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {
                    // echo 'individual_price_hidden $price = 0'.'<br />';
                    $temp_price = $p;
                }

            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $temp_price = $p;

                }
            }

            $temp_price = apply_filters('gpls_woo_rfq_get_price_hidden_html', $temp_price, $price, $product, $rfq_enable);

        }

        return $temp_price;

    }


    function gpls_woo_rfq_individual_price_hidden($price, $product)
    {

        if ($product->get_type() == 'external') {
            return $price;
        }

        $temp_price = $price;

        $p = false;

        //  global $product;

        $rfq_enable = 'no';

        if (isset($product) && is_object($product)) {

            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }

            $type = $product->get_type();
            if ($type == 'simple' || $type == 'variable') {
                if (trim($this_price) === '') {
                    // $temp_price = $p;
                }
            }

            $rfq_enable = gpls_woo_get_rfq_enable($product);


            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {

                            $temp_price = $p;
                        } else {
                            if (!isset($price) || trim($price) == '' || $price == 0) {
                                //  $temp_price = $p;
                            }
                        }

                        break;
                }


            }

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] === "rfq") {


                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no') {

                    $temp_price = $p;

                }
            }
            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' && !is_user_logged_in()) {

                    $temp_price = $p;

                }
            }


            $temp_price = apply_filters('gpls_woo_rfq_get_individual_price_hidden', $temp_price, $price, $product, $rfq_enable);

            // $temp_price = apply_filters('gpls_woo_rfq_get_price', $temp_price,$product, $rfq_enable);
        }


        return $temp_price;

    }


    function gpls_woo_rfq_individual_price_html_from_to($price, $from, $to, $product)
    {


        if ($product->get_type() == 'external') {
            return $price;
        }

        if (isset($price)) {
            $temp_price = $price;
        } else {
            $temp_price = false;
        }

        $p = false;

        // global $product;

        if (isset($product) && is_object($product)) {


            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            //echo $product->id.' '.$rfq_enable.'<br />';

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {
                            // echo 'individual_price_hidden $price = 0'.'<br />';
                            $temp_price = $p;
                        } else {
                            if (!isset($price) || trim($price) == '' || $price == 0) {
                                //  $temp_price = $p;
                            }
                        }

                        break;
                }
            }

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {
                    // echo 'individual_price_hidden $price = 0'.'<br />';
                    $temp_price = $p;
                }
            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $temp_price = $p;
                }
            }


            $temp_price = apply_filters('gpls_woo_rfq_get_price_html_from_to', $temp_price, $price, $product, $rfq_enable);
            //  $temp_price = apply_filters('gpls_woo_rfq_get_price', $temp_price,$product, $rfq_enable);
        }


        return $temp_price;

    }


    function gpls_woo_rfq_woocommerce_stock_html($availability_html, $availability, $product)
    {
        return $availability_html;
    }


    function gpls_woo_rfq_individual_price_hidden_html($price, $product)
    {


        if ($product->get_type() == 'external') {
            return $price;
        }

        $temp_price = $price;
        $p = false;

        $rfq_enable = false;

        if (isset($product) && is_object($product)) {

            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }
            $type = $product->get_type();
            if ($type == 'simple' || $type == 'variable') {
                if (trim($this_price) === '') {
                    // $temp_price = $p;
                }
            }

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            //echo $product->get_id().' '.$rfq_enable.'<br />';


            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {
                            // echo 'individual_price_hidden $price = 0'.'<br />';
                            $temp_price = $p;

                        } else {
                            if (!isset($price) || trim($price) == '' || $price == 0) {
                                //  $temp_price = $p;
                            }
                        }

                        break;
                }
            }


            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                || (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) {

                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {
                    // echo 'individual_price_hidden $price = 0'.'<br />';
                    $temp_price = $p;
                } else {

                }

            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $temp_price = $p;

                }
            }


            $temp_price = apply_filters('gpls_woo_rfq_get_price_hidden_html', $temp_price, $price, $product, $rfq_enable);

            //$temp_price = apply_filters('gpls_woo_rfq_get_price', $temp_price,$product, $rfq_enable);

        }

        return $temp_price;

    }


    function gpls_woo_rfq_individual_price_hidden_variation_html($price, $product, $min_or_max, $display)
    {
        if (!is_admin()) {
            if (gpls_empty($price)) {
                //return 0;
            }
        }

        if ($product->get_type() == 'external') {
            return $price;
        }

        $temp_price = $price;
        $p = false;

        $rfq_enable = false;

        if (isset($product) && is_object($product)) {
            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }

            $type = $product->get_type();
            if ($type == 'simple' || $type == 'variable') {
                if (trim($this_price) === '') {
                    //  $temp_price = $p;
                }
            }

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            //echo $product->id.' '.$rfq_enable.'<br />';

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {
                            // echo 'individual_price_hidden $price = 0'.'<br />';
                            $temp_price = $p;
                        } else {
                            if (!isset($price) || trim($price) == '' || $price == 0) {
                                //  $temp_price = $p;
                            }
                        }

                        break;
                }
            }

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {
                    // echo 'individual_price_hidden $price = 0'.'<br />';
                    $temp_price = $p;
                }
            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                    $temp_price = $p;

                }
            }


            $temp_price = apply_filters('gpls_woo_rfq_get_price_hidden_variation_html', $temp_price, $price, $product, $rfq_enable);
            //$temp_price = apply_filters('gpls_woo_rfq_get_price', $temp_price,$product, $rfq_enable);

        }


        return $temp_price;

    }


    function gpls_woo_rfq_product_is_on_sale($is_on_sale, $product)
    {

        if ($product->get_type() == 'external') {
            return $is_on_sale;
        }

        $temp_is_on_sale = $is_on_sale;

        $rfq_enable = false;

        if (isset($product) && is_object($product)) {


            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);


            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                switch ($rfq_enable) {
                    case 'no':
                        break;
                    case '':
                        break;
                    case 'yes':
                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no') {

                            $temp_is_on_sale = false;
                        }
                        break;
                }


            }

            if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {

                    $temp_is_on_sale = false;
                }
            }

            $temp_is_on_sale = apply_filters('gpls_woo_rfq_is_on_sale', $temp_is_on_sale, $product, $rfq_enable);


        }


        return $temp_is_on_sale;

    }

    function gpls_woo_rfq_woocommerce_get_order_item_totals($total_rows, $order)
    {
        if (is_object($order) == false || $order == null || $order == false) {
            return $total_rows;
        }

        if ($order->get_status() == "gplsquote-req") {
            $total_rows = array();


        }

        $total_rows = apply_filters('gpls_woo_rfq_woocommerce_get_order_item_totals', $total_rows, $order);

        return $total_rows;

    }


    //
    function gpls_woo_rfq_get_formatted_order_total($formatted_total, $order)
    {

        if (is_object($order) == false || $order == null || $order == false) {
            return $formatted_total;
        }

        if ($order->get_status() == "gplsquote-req") {
            $formatted_total = '';

        }

        $formatted_total = apply_filters('gpls_woo_rfq_woocommerce_get_formatted_order_total', $formatted_total, $order);


        return $formatted_total;
    }

//
    function gpls_woo_rfq_order_formatted_line_subtotal($subtotal, $item, $order)
    {

        if (is_object($order) == false || $order == null || $order == false) {
            return $subtotal;
        }

        if ($order->get_status() == "gplsquote-req") {
            $subtotal = '';

        }


        $subtotal = apply_filters('gpls_woo_rfq_woocommerce_order_formatted_line_subtotal', $subtotal, $item, $order);


        return $subtotal;
    }


    function gpls_woo_rfq_woocommerce_ship_to_different_address_checked($ship_to_destination)
    {
        if (!is_user_logged_in()) {
            return false;
        }
    }


    if (!function_exists('gpls_woo_rfq_remove_http')) {
        function gpls_woo_rfq_remove_http($url)
        {
            $disallowed = array('http://', 'https://');
            foreach ($disallowed as $d) {
                if (strpos($url, $d) === 0) {
                    return str_replace($d, '', $url);
                }
            }
            return $url;
        }
    }

    function gpls_woo_rfq_before_main_content()
    {

//  WordPress.Security.NonceVerification.Recommended
        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            return;
        }


        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

        if (($gpls_woo_rfq_cart == false)) {
            return;
        }

        $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

        // $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));

        ob_start();

        wc_get_template('woo-rfq/link-to-cart-pages.php',
            array('link_to_rfq_page' => $link_to_rfq_page,
            ), '', gpls_woo_rfq_WOO_PATH);

        $link = ob_get_clean();
        echo $link;

        //echo <<< eod
//<div class="fqcart-link-div-shop  fqcart-link-div-shop-custom"><a  class="rfqcart-link-shop rfqcart-link-shop-custom" href="{$link_to_rfq_page}" >$view_your_cart_text</a></div>
//eod;

        ?>


        <?php


    }

    function gpls_woo_rfq_woocommerce_thankyou($orderid)
    {


        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            return;
        }


        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

        if (($gpls_woo_rfq_cart == false)) {
            return;
        }

        $link_to_rfq_page = (pls_woo_rfq_get_link_to_rfq());

        $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));

        $rfq_cart_reminder = __('You have items in your Request for Quote Cart', 'woo-rfq-for-woocommerce');

        echo("
<div class='fqcart-link-div-shop  fqcart-link-div-shop-custom'>{$rfq_cart_reminder}.
 <a  class='rfqcart-link-shop rfqcart-link-shop-custom' href='{$link_to_rfq_page}'' >$view_your_cart_text</a></div>
"
        );

        ?>


        <?php


    }

    function gpls_woo_rfq_get_rfq_cart()
    {

        global $woocommerce;

        if (is_admin() || $woocommerce == null) {
            return false;
        }
//  WordPress.Security.NonceVerification.Recommended
        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            return false;
        }


//  WordPress.Security.NonceVerification.Recommended
        //if (isset($_REQUEST['order_id']) && ($_REQUEST['order_id'] != false))


        if (

            isset($_REQUEST['gpls_woo_rfq_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce')
            &&
            isset($_REQUEST['key']) && ($_REQUEST['key'] != false)
        ) {


            ob_start();

            $order_factory = new WC_Order_Factory();
            // $order = $order_factory->get_order($_REQUEST['order_id']);
//  WordPress.Security.NonceVerification.Recommended
            //  $order = $order_factory->get_order(sanitize_text_field(wp_unslash($_REQUEST['order_id'])));
            if (
                wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce')
            ) {
                $order_id = wc_get_order_id_by_order_key(sanitize_text_field(wp_unslash($_REQUEST['key'])));
                $order = $order_factory->get_order(sanitize_text_field(wp_unslash($order_id)));
            }
            do_action('gpls_woo_rfq_before_thankyou');

            $default_temp = 'checkout/thankyou.php';

            $normal_template = apply_filters('gpls_woo_rfq_normal_thankyou_template', $default_temp, $order);

            wc_get_template($normal_template, array('order' => $order));

            // wc_get_template('woo-rfq/customer-respond.php',
            // array('order' => $order), '', gpls_woo_rfq_plus_DIR . 'woocommerce/');

            return ob_get_clean();
        }


        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');


        //if (($gpls_woo_rfq_cart != false))
        if (($gpls_woo_rfq_cart != false)) {

            ob_start();
            $confirmation_message = get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button',
                __('Update Quote Request', 'woo-rfq-for-woocommerce'));
            $confirmation_message = __($confirmation_message, 'woo-rfq-for-woocommerce');

            wc_get_template('woo-rfq/rfq-cart.php',
                array('confirmation_message' => $confirmation_message),
                '', gpls_woo_rfq_WOO_PATH);

            gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart_notices');
            // return;

            return ob_get_clean();

        } else {

            ob_start();

            wc_get_template('woo-rfq/rfq-cart-empty.php',
                array('confirmation_message' => ''),
                '', gpls_woo_rfq_WOO_PATH);
            //return;

            return ob_get_clean();
        }
    }

    function gpls_woo_rfq_get_fav_cart()
    {

        /**
         * WOO-RFQ-List
         */
// phpcs:ignoreFile
        if (!defined('ABSPATH')) {
            exit; // Exit if accessed directly
        }
        $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
        $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

        if (($allow_favs=="yes" && is_user_logged_in()) ||
            (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
            ob_start();

            wc_get_template('woo-rfq/fav-cart.php',
                array('confirmation_message' => ''),
                '', gpls_woo_rfq_WOO_PATH);
            //return;

            echo ob_get_clean();
            //   exit;
        }

    }


    function gpls_woo_rfq_woocommerce_coupons_enabled($enable_coupon)
    {

        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {
                $enable_coupon = get_option('woocommerce_enable_coupons', 'no') == 'yes' && get_option('settings_gpls_woo_rfq_allow_coupon_entry', 'no') == 'yes';
            }
        }
        return $enable_coupon;

    }

    //hide prices
    function gpls_woo_rfq_hide_prices($price, $product)
    {

        if ($product->get_type() == 'external') {
            return $price;
        }

        $price = '';
        $price = apply_filters('gpls_woo_rfq_hide_prices', $price, $product);

        return $price;
    }


    function gpls_woo_rfq_total_prices($value)
    {

        $value = false;
        $value = apply_filters('gpls_woo_rfq_total_prices', $value);
        return $value;
    }

    function gpls_woo_rfq_hide_cart_prices($_product, $cart_item, $cart_item_key)
    {

        $price = false;
        $price = apply_filters('gpls_woo_rfq_hide_cart_prices', $price, $_product, $cart_item, $cart_item_key);
        return $price;
    }

    function gpls_woo_rfq_hide_woocommerce_cart_product_price($product_price, $product)
    {


        if ($product->get_type() == 'external') {
            return $product_price;
        }

        $temp_product_price = false;
        $temp_product_price = apply_filters('gpls_woo_rfq_hide_woocommerce_cart_product_price', $temp_product_price, $product_price, $product);
        return $temp_product_price;
    }

    function gpls_woo_rfq_hide_woocommerce_cart_product_subtotal($product_subtotal, $product, $quantity)
    {

        if ($product->get_type() == 'external') {
            return $product_subtotal;
        }

        $temp_get_product_subtotal = '';
        $temp_get_product_subtotal = apply_filters('gpls_woo_rfq_hide_woocommerce_cart_product_subtotal', $temp_get_product_subtotal, $product_subtotal, $product, $quantity);
        return $temp_get_product_subtotal;
    }


    function gpls_woo_rfq_hide_woocommerce_cart_item_subtotal($product_subtotal, $cart_item, $cart_item_key)
    {


        $temp_get_product_subtotal = false;
        $temp_get_product_subtotal = apply_filters('gpls_woo_rfq_hide_woocommerce_cart_item_subtotal', $temp_get_product_subtotal, $product_subtotal, $cart_item, $cart_item_key);


        return $temp_get_product_subtotal;

    }


    function gpls_woo_rfq_hide_woocommerce_cart_subtotal($cart_subtotal, $compound, $cart)
    {

        global $cart;
        $temp_cart_subtotal = false;
        $temp_cart_subtotal = apply_filters('gpls_woo_rfq_hide_woocommerce_cart_subtotal', $temp_cart_subtotal, $cart_subtotal, $compound, $cart);
        return $temp_cart_subtotal;
    }


    function gpls_rfq_update_rfq_cart()
    {
        if (isset($_POST['gpls_woo_rfq_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_POST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce') &&
            isset($_POST['gpls-woo-rfq_update']) && ($_POST['gpls-woo-rfq_update'] == "true")
            && !isset($_REQUEST['remove_rfq_item'])
        ) {


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if ($gpls_woo_rfq_cart == false) return;

            if (($gpls_woo_rfq_cart != false)) {

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    $_product = $values['data'];

//  WordPress.Security.NonceVerification.Missing
                    if (!isset($_POST['cart'][$cart_item_key]) || !isset($_POST['cart'][$cart_item_key]['qty'])) {
                        continue;
                    }

                    $cart_item = $values;

                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    $quantity = sanitize_text_field(wp_unslash($_POST['cart'][$cart_item_key]['qty']));

                    $quantity = apply_filters('gpls_rfq_stock_amount_cart_item', $quantity, $product_id, $cart_item_key, $values);
                    //$quantity = apply_filters( 'gpls_rfq_stock_amount_cart_item', $quantity, $cart_item_key,$values );

                    if ($quantity != $_POST['cart'][$cart_item_key]['qty']) {

                        $qty_message = apply_filters('gpls_rfq_stock_amount_cart_item_message', '', $product_id);

                        if ($qty_message != '') {
                            gpls_woo_rfq_add_notice($qty_message, 'info');
                        }
                    }


                    if ('' === $quantity || $quantity == $values['quantity'])
                        continue;

                    if ($quantity == 0 || $quantity < 0) {

                        unset($gpls_woo_rfq_cart[$cart_item_key]);

                    } else {
                        $old_quantity = $gpls_woo_rfq_cart[$cart_item_key]['quantity'];
                        $gpls_woo_rfq_cart[$cart_item_key]['quantity'] = $quantity;

                    }


                }

            }


            gpls_woo_rfq_cart_set('gpls_woo_rfq_cart', $gpls_woo_rfq_cart);

            $return_url = pls_woo_rfq_get_link_to_rfq();


        }
    }

    function gpls_rfq_remove_rfq_cart_item()
    {
//
        if (isset($_REQUEST['gpls_woo_rfq_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce')
            && isset($_REQUEST['remove_rfq_item']) && isset($_REQUEST['man-deleted'])) {

            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {

                unset($gpls_woo_rfq_cart[$_REQUEST['man-deleted']]);

            }


            gpls_woo_rfq_cart_set('gpls_woo_rfq_cart', $gpls_woo_rfq_cart);


            //  header('Content-type: text/html; charset=utf-8'); // make sure this is set

            //  header('Location: ' . $return_url, true, 307);
            //wp_safe_redirect($return_url);

            //  exit;

        }

    }


    function gpls_rfq_remove_fav_cart_item()
    {
        if (isset($_REQUEST['gpls_woo_fav_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['gpls_woo_fav_nonce'])), 'gpls_woo_rfq_handle_rfq_fav_cart_nonce')
            && isset($_REQUEST['remove_fav_item']) && isset($_REQUEST['man-deleted'])) {

            if (is_user_logged_in()) {
                 np_delete_fav_rec_by_user_id_product_id(get_current_user_id(),$_REQUEST['man-deleted']);

            }
            else {

                $gpls_woo_fav_cart = gpls_woo_rfq_get_item('gpls_woo_fav_array');
                if (($gpls_woo_fav_cart != false)) {
                    unset($gpls_woo_fav_cart[$_REQUEST['man-deleted']]);
                }

                if (!is_user_logged_in()) {
                    gpls_woo_rfq_cart_set('gpls_woo_fav_array', $gpls_woo_fav_cart);
                }
            }


        }

    }


    function gpls_rfq_add_fav_cart_item()
    {


        if (isset($_REQUEST['single_fav_id']) && isset($_REQUEST['_wpnonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['_wpnonce'])), 'fav_id_nonce')
            && isset($_REQUEST['single_fav_id'])) {

            $product_id = $_REQUEST['product_id'];

            if (!is_user_logged_in()) {

                $gpls_woo_fav_array = gpls_woo_rfq_get_item('gpls_woo_fav_array');
            }

            if (!$gpls_woo_fav_array) {

                $gpls_woo_fav_array = array();
            }

            $new_arr = array();
            $gpls_woo_fav_array[$product_id] = $new_arr;

            if (is_user_logged_in()) {

                $user=new WP_User(get_current_user_id());

                $first='';
                $last='';
                if(empty($user->user_firstname)){
                    $first=$user->display_name;
                }else{
                    $first=$user->user_firstname;
                }
                if(empty($user->user_lastname)){
                    $last=$user->display_name;
                }else{
                    $last=$user->user_lastname;
                }
                $product=wc_get_product($product_id);
                $image_id = $product->get_image_id();
                $product_image = wp_get_attachment_image_url( $image_id, 'thumbnail' );
                $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
                if ( $image_url ) {
                    $product_image= '<img style="max-width:100px;max-height:100px" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" />';
                }
                $product_url=$product->get_permalink();

                np_add_fav_rec($user->ID, $product_id,$first,
                    $last,$user->user_email,
                    $product_url,$product_image,null,null,
                    null);
            }else{

                gpls_woo_rfq_cart_set('gpls_woo_fav_array', $gpls_woo_fav_array);
            }


        }

    }


    function gpls_woo_rfq_get_product($id)
    {
        $product_id = 0;
        $variation_id = 0;
        $variation = array();

        $variable_product = wc_get_product($id);
        if (!$variable_product)
            return false;


        if ($variable_product->get_type() === "variable") {
            $variation = $variable_product->get_available_variations();
            $product_id = $variable_product->get_id();
            $variation_id = $id;
        } else {
            $product_id = $id;
        }


        return array("product-id" => $product_id, "variation-id" => $variation_id, "variation" => $variation);
    }

    function gpls_woo_rfq_handle_checkout()
    {

        do_action('gpls_woo_rfq_before_normal_checkout_proceed');

        $proceed = true;

        $proceed = apply_filters('gplswoo_before_normal_checkout_proceed', $proceed);

        do_action('gpls_woo_rfq_after_normal_checkout_proceed', $proceed);

        if (!$proceed) {
            return;
        }

        if (is_user_logged_in()) {
            gpls_woo_rfq_handle_customer_checkout();

        } else {
            gpls_woo_rfq_handle_anon_checkout();
        }


    }

    function gpls_woo_rfq_handle_customer_checkout()
    {

        try {


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {


                if (!isset($_POST['gpls_woo_rfq_nonce'])
                    || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce')) {
                    return;
                }

                if (count($gpls_woo_rfq_cart) == 0) {
                    gpls_woo_rfq_add_notice(get_option('rfq_cart_wordings_rfq_cart_is_empty', __('You RFQ cart is empty and was not submitted', 'woo-rfq-for-woocommerce'), 'error'));
                    return;
                }
//
                //  $cart_totals = isset($_POST['cart']) ? $_POST['cart'] : '';

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    $_product = $values['data'];

                    if (!isset($_POST['cart'][$cart_item_key]) || !isset($_POST['cart'][$cart_item_key]['qty'])) {
                        continue;
                    }
                    $quantity = sanitize_text_field(wp_unslash($_POST['cart'][$cart_item_key]['qty']));
                    //  $quantity = $cart_totals[$cart_item_key]['qty'];


                    if ('' === $quantity || $quantity == $values['quantity'])
                        continue;


                    if ($quantity == 0 || $quantity < 0) {

                        unset($gpls_woo_rfq_cart[$cart_item_key]);
                    } else {
                        $old_quantity = $gpls_woo_rfq_cart[$cart_item_key]['quantity'];
                        $gpls_woo_rfq_cart[$cart_item_key]['quantity'] = $quantity;

                    }

                }


                if (count($gpls_woo_rfq_cart) == 0) {
                    gpls_woo_rfq_add_notice(get_option('rfq_cart_wordings_rfq_cart_is_empty', __('You RFQ cart is empty and was not submitted', 'woo-rfq-for-woocommerce'), 'error'));
                    return;
                }


                $customer_id = apply_filters('woocommerce_checkout_customer_id', get_current_user_id());
                $user = get_user_by('id', $customer_id);

                $billing_email = $user->user_email;


                $order = wc_create_order(array(
                    'status' => 'pending',
                    'customer_id' => $customer_id,
                    'billing_email' => $billing_email
                ));


                $pf = new WC_Product_Factory();

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $cart_item) {


                    $ids = gpls_woo_rfq_get_product($cart_item['product_id']);

                    $product_id = $cart_item['product_id'];

                    $_product = $pf->get_product($product_id);

                    if (!$_product) {
                        continue;
                    }

                    if (isset($cart_item['variation_id'])) {
                        $variation_id = $cart_item['variation_id'];
                    }
                    if (isset($ids['variations'])) {
                        $variations = $ids['variations'];
                    }

                    $quantity = $cart_item['quantity'];

                    if (!isset($variations)) {

                        if ($_product->get_type() === 'variable') {

                            $var_product = new WC_Product_Variable($product_id);
                            $variations = $var_product->get_available_variations();
                        } else {
                            $variations = array();
                        }
                    }

                    $cart_item = apply_filters('gpls_woo_rfq_cart_item', $cart_item, $quantity, $ids, $product_id, $_product, $variation_id, $variations);


                    if (!empty($cart_item['addons']) && class_exists('GPLS_WOO_RFQ_PLUS')) {

                        if (!isset($cart_item['bundled_by']) && !isset($cart_item['composite_parent'])) {
                            $item_id = $order->add_product(
                                $cart_item['data'],
                                $cart_item['quantity'],
                                array('subtotal' => $cart_item['line_subtotal'], 'total' => $cart_item['line_total'],
                                    'variation' => $cart_item['variation']
                                )
                            );
                        }

                    } else {

                        if (!isset($cart_item['bundled_by']) && !isset($cart_item['composite_parent'])) {
                            $item_id = $order->add_product(
                                $cart_item['data'],
                                $cart_item['quantity'],
                                array('variation' => $cart_item['variation']
                                )
                            );
                        }
                    }


                    $item = $order->get_item($item_id);

                    do_action('gpls_woo_rfq_add_to_order_custom_products', $_product, $cart_item, $cart_item_key, $item_id, $item, $order);

                    do_action('gpls_woo_rfq_order_item_meta', $item_id, $cart_item, $cart_item_key);


                    if (version_compare(WC()->version, '3.0', ">=")) {

                        $cart_item_temp = new WC_Order_Item($cart_item_key);

                        do_action('woocommerce_new_order_item', $item_id, $cart_item_temp, $order->get_id());

                        do_action('gpls_woo_rfq_woocommerce_new_order_item', $item_id, $cart_item, $order->get_id());
                    }
                    if (version_compare(WC()->version, '3.0', "<") || class_exists('VPC_Public')) {
                        // do_action('woocommerce_add_order_item_meta', $item_id, $cart_item, $cart_item_key);
                        do_action('gpls_woo_rfq_woocommerce_new_order_item', $item_id, $cart_item, $order->get_id());
                    }

                    //do_action('rfqtk_woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $values, $item_id, $cart_item);
                    do_action('rfqtk_woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $gpls_woo_rfq_cart[$cart_item_key], $item_id, $cart_item);

                }


                $order->calculate_shipping();

                $order->calculate_taxes();

                $order->calculate_totals(true);

                $name_billing = 'billing';
                $address_billing = apply_filters('woocommerce_my_account_my_address_formatted_address', array(
                    'first_name' => get_user_meta($customer_id, $name_billing . '_first_name', true),
                    'last_name' => get_user_meta($customer_id, $name_billing . '_last_name', true),
                    'phone' => get_user_meta($customer_id, $name_billing . '_phone', true),
                    'company' => get_user_meta($customer_id, $name_billing . '_company', true),
                    'address_1' => get_user_meta($customer_id, $name_billing . '_address_1', true),
                    'address_2' => get_user_meta($customer_id, $name_billing . '_address_2', true),
                    'city' => get_user_meta($customer_id, $name_billing . '_city', true),
                    'state' => get_user_meta($customer_id, $name_billing . '_state', true),
                    'postcode' => get_user_meta($customer_id, $name_billing . '_postcode', true),
                    'country' => get_user_meta($customer_id, $name_billing . '_country', true)
                ), $customer_id, $name_billing);

                $name = 'shipping';
                $address_shipping = apply_filters('woocommerce_my_account_my_address_formatted_address', array(
                    'first_name' => get_user_meta($customer_id, $name . '_first_name', true),
                    'last_name' => get_user_meta($customer_id, $name . '_last_name', true),
                    'company' => get_user_meta($customer_id, $name . '_company', true),
                    'phone' => get_user_meta($customer_id, $name . '_phone', true),
                    'address_1' => get_user_meta($customer_id, $name . '_address_1', true),
                    'address_2' => get_user_meta($customer_id, $name . '_address_2', true),
                    'city' => get_user_meta($customer_id, $name . '_city', true),
                    'state' => get_user_meta($customer_id, $name . '_state', true),
                    'postcode' => get_user_meta($customer_id, $name . '_postcode', true),
                    'country' => get_user_meta($customer_id, $name . '_country', true)
                ), $customer_id, $name);

                $order->set_address($address_billing, 'billing');

                $order->set_address($address_shipping, 'shipping');

                $order->set_date_created(current_time('mysql', 0));


                $order->set_payment_method("gpls-rfq");

                if (isset($_POST['rfq_message'])) {
                    $message = trim(sanitize_text_field(wp_unslash($_POST['rfq_message'])));
                } else {
                    $message = "";
                }

                if (isset($message) && trim($message) != "") {
                    //$order->add_order_note($message, 1, false);


                    $order->set_customer_note($message);
                    //  $order->set_props('post_excerpt',$message);


                    $user = get_user_by('id', get_current_user_id());
                    $comment_author = $user->display_name;
                    $comment_author_email = $user->user_email;


                    $comment_post_ID = $order->get_id();
                    $comment_author_url = '';
                    $comment_content = $message;
                    $comment_agent = 'Customer';
                    $comment_type = 'order_note';
                    $comment_parent = 0;
                    $comment_approved = 1;
                    $commentdata = apply_filters('woocommerce_new_order_note_data', compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved'), array('order_id' => $order->get_id(), 'is_customer_note' => 1));

                    $comment_id = wp_insert_comment($commentdata);


                    add_comment_meta($comment_id, 'is_customer_note', 1);

                    add_comment_meta($comment_id, 'note_added_by_customer', 1);


                }


                do_action('woocommerce_checkout_update_order_meta', $order->get_id(), $_POST);

                gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart');

                $confirmation_message = get_option('gpls_woo_rfq_quote_submit_confirm_message', __('Your quote request has been successfully submitted!', 'woo-rfq-for-woocommerce'));

                gpls_woo_rfq_add_notice($confirmation_message, 'success');
                wc_add_notice($confirmation_message, 'success');

                $order_id = $order->get_id();

                do_action('gpls_woo_rfq_customer_checkout_end', $order_id, $_POST);

                $order->update_status("gplsquote-req");


                // $order->save();

                if (isset($_REQUEST['global_product_id'])
                    && isset($_REQUEST['rfqform_location'])
                    && sanitize_text_field(wp_unslash($_REQUEST['rfqform_location'])) === "product") {

                    $global_product = wc_get_product(sanitize_text_field(wp_unslash($_REQUEST['global_product_id'])));
                    $return_url = $global_product->get_permalink();

                } else {

                    $return_url = pls_woo_rfq_get_link_to_rfq();
                }


                do_action('gpls_woo_rfq_after_normal_checkout', $order_id);

                $key = $order->get_order_key();

                $customer_unique_id = $customer_id;

                if (isset(WC()->session)) {
                    $customer_id = WC()->session->get_customer_unique_id();
                }


                //nonce verified already on top at the top.
                $order_unique_id = wp_generate_uuid4();
                $hashed_order_unique_id = wp_hash($order_unique_id);
                $nonce = wp_create_nonce('gpls_woo_rfq_handle_rfq_cart_nonce');
                gpls_woo_rfq_cart_set('gpls_woo_rfq_LQ', array('order_id' => $order_id,
                    'nonce' => sanitize_key(wp_unslash($nonce)),
                    'customer_id' => $customer_id,
                    'anon' => 0,
                    'order_unique_id' => $order_unique_id
                ));
                // $gpls_woo_rfq_LQ= gpls_woo_rfq_get_item('gpls_woo_rfq_LQ');


                wp_safe_redirect($return_url . '?gpls_woo_rfq_nonce=' . trim(sanitize_key(wp_unslash($nonce))) . '&key=' . $key . ' &ukey=' . $hashed_order_unique_id);

                exit;


            }
        } catch (Exception $ex) {
            np_write_log($ex->getMessage(), __FILE__, __LINE__);
        }


    }


    function gpls_woo_rfq_handle_anon_checkout()
    {

        if (!isset($_POST['gpls_woo_rfq_nonce'])
            || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce')) {
            return;
        }


        try {

            // global $gpls_woo_rfq_cart;
            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {


                $name_billing = 'billing';

                $billing_state = "";

                if (isset($_POST['rfq_state_select'])) {
                    if ($_POST['rfq_state_select'] == "" && isset($_POST['rfq_state_text'])) {
                        $billing_state = sanitize_text_field(wp_unslash($_POST['rfq_state_text']));
                    } else {
                        $billing_state = sanitize_text_field(wp_unslash($_POST['rfq_state_select']));
                    }
                } else {
                    $billing_state = '';
                }

                if (!is_user_logged_in() && (!isset($_POST['rfq_fname']) || !isset($_POST['rfq_lname']) || !isset($_POST['rfq_email_customer']))) {

                    gpls_woo_rfq_add_notice(__('Please enter first name, last name and a valid email', 'woo-rfq-for-woocommerce'), 'error');

                    return;
                }

                $valid = true;

                if (isset($_POST['rfq_email_customer'])) {


                    if (!is_email(sanitize_text_field(wp_unslash(($_POST['rfq_email_customer']))))) {

                        gpls_woo_rfq_add_notice(__('Invalid email address.', 'woo-rfq-for-woocommerce'), 'error');

                        $valid = false;
                    }
                }

                if (isset($_POST['rfq_phone'])) {

                    if (!WC_Validation::is_phone(sanitize_text_field(wp_unslash($_POST['rfq_phone'])))) {

                        gpls_woo_rfq_add_notice(__('Invalid phone number.', 'woo-rfq-for-woocommerce'), 'error');

                        $valid = false;
                    }
                }

                if ($valid == false) {
                    return;
                }


                $address_billing = array(
                    'first_name' => sanitize_text_field(wp_unslash($_POST['rfq_fname'])),
                    'last_name' => sanitize_text_field(wp_unslash($_POST['rfq_lname'])),
                    'company' => sanitize_text_field(isset($_POST['rfq_company']) ? sanitize_text_field(wp_unslash($_POST['rfq_company'])) : ""),
                    'email' => sanitize_text_field(isset($_POST['rfq_email_customer']) ? sanitize_text_field(wp_unslash($_POST['rfq_email_customer'])) : ""),
                    'phone' => sanitize_text_field(isset($_POST['rfq_phone']) ? sanitize_text_field(wp_unslash($_POST['rfq_phone'])) : ""),
                    'address_1' => sanitize_text_field(isset($_POST['rfq_address']) ? sanitize_text_field(wp_unslash($_POST['rfq_address'])) : ""),
                    'address_2' => sanitize_text_field(isset($_POST['rfq_address2']) ? sanitize_text_field(wp_unslash($_POST['rfq_address2'])) : ""),
                    'city' => sanitize_text_field(isset($_POST['rfq_city']) ? sanitize_text_field(wp_unslash($_POST['rfq_city'])) : ""),
                    'state' => sanitize_text_field(wp_unslash($billing_state)),
                    'postcode' => sanitize_text_field(isset($_POST['rfq_zip']) ? sanitize_text_field(wp_unslash($_POST['rfq_zip'])) : ""),
                    'country' => sanitize_text_field(isset($_POST['rfq_billing_country']) ? sanitize_text_field(wp_unslash($_POST['rfq_billing_country'])) : "")
                );

                if (count($gpls_woo_rfq_cart) == 0) {
                    gpls_woo_rfq_add_notice(get_option('rfq_cart_wordings_rfq_cart_is_empty', __('You RFQ cart is empty and was not submitted', 'woo-rfq-for-woocommerce'), 'error'));
                    return;
                }


                // $cart_totals = isset($_POST['cart']) ? $_POST['cart'] : '';

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    $_product = $values['data'];

                    if (!isset($cart_totals[$cart_item_key]) || !isset($cart_totals[$cart_item_key]['qty'])) {
                        continue;
                    }

                    //  $quantity = $cart_totals[$cart_item_key]['qty'];
                    if (isset($_POST['cart'][$cart_item_key]['qty'])) {
                        $quantity = sanitize_text_field(wp_unslash($_POST['cart'][$cart_item_key]['qty']));
                    }

                    if ('' === $quantity || $quantity == $values['quantity'])
                        continue;


                    if ($quantity == 0 || $quantity < 0) {

                        unset($gpls_woo_rfq_cart[$cart_item_key]);
                    } else {
                        $old_quantity = $gpls_woo_rfq_cart[$cart_item_key]['quantity'];
                        $gpls_woo_rfq_cart[$cart_item_key]['quantity'] = $quantity;

                    }

                }

                if (count($gpls_woo_rfq_cart) == 0) {
                    gpls_woo_rfq_add_notice(get_option('rfq_cart_wordings_rfq_cart_is_empty', __('You RFQ cart is empty and was not submitted', 'woo-rfq-for-woocommerce'), 'error'));
                    return;

                }


                $customer_id = apply_filters('woocommerce_checkout_customer_id', get_current_user_id());

                if (!is_user_logged_in() && (isset($_POST['rfq_createaccount']) ||
                        get_option('rfq_cart_sc_section_rfq_page_create_accounts', "yes") != "no")) {

                    $rfq_email_customer = sanitize_text_field(wp_unslash($_POST['rfq_email_customer']));
                    if (isset($rfq_email_customer)) {
                        $parts = explode("@", sanitize_text_field(wp_unslash($_POST['rfq_email_customer'])));
                        $username = $parts[0];
                    } else {
                        $username = sanitize_text_field(wp_unslash($_POST['rfq_fname'])
                            . '_' . sanitize_text_field(wp_unslash($_POST['rfq_lname'])));
                    }

                    if (isset($_POST['account_password'])) {
                        $password = sanitize_text_field(wp_unslash($_POST['account_password']));
                    } else {
                        $password = wp_generate_password();
                    }

                    $new_customer = wc_create_new_customer(sanitize_text_field(wp_unslash($_POST['rfq_email_customer'])), $username, $password);

                    if (is_wp_error($new_customer)) {

                        // throw new Exception($new_customer->get_error_message());
                        gpls_woo_rfq_add_notice($new_customer->get_error_message(), 'error');
                    } else {
                        $customer_id = absint($new_customer);


                    }

                    //  wp_new_user_notification($customer_id);
                    //  wc_set_customer_auth_cookie($customer_id);

                    // As we are now logged in, checkout will need to refresh to show logged in data
                    //WC()->session->set('reload_checkout', true);

                    global $woocommerce;

                    update_user_meta($customer_id, "first_name", $address_billing["first_name"]);
                    update_user_meta($customer_id, "last_name", $address_billing["last_name"]);

                    update_user_meta($customer_id, "billing_first_name", $address_billing["first_name"]);
                    update_user_meta($customer_id, "billing_last_name", $address_billing["last_name"]);
                    update_user_meta($customer_id, "billing_address_1", $address_billing["address_1"]);
                    update_user_meta($customer_id, "billing_address_2", $address_billing["address_2"]);
                    update_user_meta($customer_id, "billing_city", $address_billing["city"]);
                    update_user_meta($customer_id, "billing_postcode", $address_billing["postcode"]);
                    update_user_meta($customer_id, "billing_country", $address_billing["country"]);
                    update_user_meta($customer_id, "billing_state", $address_billing["state"]);
                    update_user_meta($customer_id, "billing_email", $address_billing["email"]);
                    update_user_meta($customer_id, "billing_phone", $address_billing["phone"]);
                    update_user_meta($customer_id, "billing_company", $address_billing["company"]);

                    update_user_meta($customer_id, "shipping_first_name", $address_billing["first_name"]);
                    update_user_meta($customer_id, "shipping_last_name", $address_billing["last_name"]);
                    update_user_meta($customer_id, "shipping_address_1", $address_billing["address_1"]);
                    update_user_meta($customer_id, "shipping_address_2", $address_billing["address_2"]);
                    update_user_meta($customer_id, "shipping_city", $address_billing["city"]);
                    update_user_meta($customer_id, "shipping_postcode", $address_billing["postcode"]);
                    update_user_meta($customer_id, "shipping_country", $address_billing["country"]);
                    update_user_meta($customer_id, "shipping_state", $address_billing["state"]);
                    update_user_meta($customer_id, "shipping_email", $address_billing["email"]);
                    update_user_meta($customer_id, "shipping_phone", $address_billing["phone"]);
                    update_user_meta($customer_id, "shipping_company", $address_billing["company"]);


                }

                //    global $order;

                $order = wc_create_order(array(
                    'status' => 'pending',
                    'customer_id' => $customer_id,
                    'billing_email' => sanitize_text_field(wp_unslash($_POST['rfq_email_customer'] ?? ''))
                ));


                $pf = new WC_Product_Factory();

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $cart_item) {


                    $ids = gpls_woo_rfq_get_product($cart_item['product_id']);


                    $product_id = $cart_item['product_id'];

                    $_product = $pf->get_product($product_id);
                    if (!$_product) {
                        continue;
                    }

                    if (isset($cart_item['variation_id'])) {
                        $variation_id = $cart_item['variation_id'];
                    }
                    if (isset($ids['variations'])) {
                        $variations = $ids['variations'];
                    }
                    $quantity = $cart_item['quantity'];


                    if (!isset($variations)) {

                        if ($_product->get_type() === 'variable') {

                            $var_product = new WC_Product_Variable($product_id);
                            $variations = $var_product->get_available_variations();
                        } else {
                            $variations = array();
                        }
                    }


                    $cart_item = apply_filters('gpls_woo_rfq_cart_item', $cart_item, $quantity, $ids, $product_id, $_product, $variation_id, $variations);


                    if (!empty($cart_item['addons']) && class_exists('GPLS_WOO_RFQ_PLUS')) {

                        if (!isset($cart_item['bundled_by']) && !isset($cart_item['composite_parent'])) {
                            $item_id = $order->add_product(
                                $cart_item['data'],
                                $cart_item['quantity'],
                                array('subtotal' => $cart_item['line_subtotal'], 'total' => $cart_item['line_total'],
                                    'variation' => $cart_item['variation']
                                )
                            );
                        }

                    } else {

                        if (!isset($cart_item['bundled_by']) && !isset($cart_item['composite_parent'])) {
                            $item_id = $order->add_product(
                                $cart_item['data'],
                                $cart_item['quantity'],
                                array('variation' => $cart_item['variation']
                                )
                            );
                        }
                    }


                    $item = $order->get_item($item_id);

                    do_action('gpls_woo_rfq_add_to_order_custom_products', $_product, $cart_item, $cart_item_key, $item_id, $item, $order);

                    do_action('gpls_woo_rfq_order_item_meta', $item_id, $cart_item, $cart_item_key);

                    //do_action('woocommerce_new_order_item', $item_id, $cart_item, $cart_item_key);

                    if (version_compare(WC()->version, '3.0', ">=")) {

                        $cart_item_temp = new WC_Order_Item($cart_item_key);

                        do_action('woocommerce_new_order_item', $item_id, $cart_item_temp, $order->get_id());
                        //do_action('woocommerce_new_order_item', $item_id, $_product, $order->get_id());

                        do_action('gpls_woo_rfq_woocommerce_new_order_item', $item_id, $cart_item, $order->get_id());

                    }

                    if (version_compare(WC()->version, '3.0', "<") || class_exists('VPC_Public')) {
                        //  do_action('woocommerce_add_order_item_meta', $item_id, $cart_item, $cart_item_key);
                        do_action('gpls_woo_rfq_woocommerce_new_order_item', $item_id, $cart_item, $order->get_id());
                    }

                    //  do_action('rfqtk_woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $values, $item_id, $cart_item);

                    do_action('rfqtk_woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $gpls_woo_rfq_cart[$cart_item_key], $item_id, $cart_item);
                }


                $name = 'shipping';

                $order->set_address($address_billing, 'billing');
                $order->set_address($address_billing, 'shipping');


                $order->set_billing_email(sanitize_text_field(wp_unslash($_POST['rfq_email_customer'] ?? '')));
                $order->set_billing_first_name(sanitize_text_field(wp_unslash($_POST['rfq_fname'] ?? '')));
                $order->set_billing_last_name(sanitize_text_field(wp_unslash($_POST['rfq_lname'] ?? '')));


                $order->set_date_created(current_time('mysql', 0));

                $order->calculate_shipping();

                $order->calculate_taxes();
                $order->calculate_totals();

                $order->set_payment_method("gpls-rfq");

                if (isset($_POST['rfq_message'])) {
                    $message = sanitize_text_field(wp_unslash($_POST['rfq_message']));
                } else {
                    $message = "";
                }

                if (isset($message) && trim($message) != "") {
                    //$order->add_order_note($message, 1, false);


                    $order->set_customer_note($message);
                    //  $order->set_props('post_excerpt',$message);


                    $comment_author = $username;
                    $comment_author_email = sanitize_text_field(wp_unslash($_POST['rfq_email_customer'] ?? ''));

                    $comment_post_ID = $order->get_id();
                    $comment_author_url = '';
                    $comment_content = $message;
                    $comment_agent = 'Customer';
                    $comment_type = 'order_note';
                    $comment_parent = 0;
                    $comment_approved = 1;
                    $commentdata = apply_filters('woocommerce_new_order_note_data', compact('comment_post_ID',
                        'comment_author', 'comment_author_email', 'comment_author_url',
                        'comment_content', 'comment_agent', 'comment_type', 'comment_parent',
                        'comment_approved'), array('order_id' => $order->get_id(), 'is_customer_note' => 1));

                    $comment_id = wp_insert_comment($commentdata);


                    add_comment_meta($comment_id, 'is_customer_note', 1);

                    add_comment_meta($comment_id, 'note_added_by_customer', 1);


                }


                do_action('woocommerce_checkout_update_order_meta', $order->get_id(), $_POST);

                gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart');

                $confirmation_message = get_option('gpls_woo_rfq_quote_submit_confirm_message',
                    __('You RFQ has been successfully submitted!', 'woo-rfq-for-woocommerce'));

                gpls_woo_rfq_add_notice($confirmation_message, 'success');
                wc_add_notice($confirmation_message, 'success');
                $order_id = $order->get_id();

                do_action('gpls_woo_rfq_customer_checkout_end', $order_id, $_POST);

                $order->update_status("gplsquote-req");

                // $order->save();

                if (isset($_REQUEST['global_product_id'])
                    && isset($_REQUEST['rfqform_location']) && sanitize_text_field(wp_unslash($_REQUEST['rfqform_location'])) === "product") {

                    $global_product = wc_get_product(sanitize_text_field(wp_unslash($_REQUEST['global_product_id'])));
                    $return_url = $global_product->get_permalink();

                } else {

                    $return_url = pls_woo_rfq_get_link_to_rfq();
                }


                do_action('gpls_woo_rfq_after_normal_checkout', $order_id);

                $key = $order->get_order_key();


                $nonce = wp_create_nonce('gpls_woo_rfq_handle_rfq_cart_nonce');
                $order_unique_id = wp_generate_uuid4();
                $hashed_order_unique_id = wp_hash($order_unique_id);
                gpls_woo_rfq_cart_set('gpls_woo_rfq_LQ', array('order_id' => $order_id,
                    'nonce' => sanitize_key(wp_unslash($nonce)),
                    'customer_id' => $customer_id,
                    'anon' => 0,
                    'order_unique_id' => $order_unique_id
                ));
                //   $gpls_woo_rfq_LQ= gpls_woo_rfq_get_item('gpls_woo_rfq_LQ');


                wp_safe_redirect($return_url . '?gpls_woo_rfq_nonce=' . trim(sanitize_key(wp_unslash($nonce))) . '&key=' . $key . ' &ukey=' . $hashed_order_unique_id);


                exit;

            }
        } catch (Exception $ex) {
            np_write_log($ex->getMessage(), __FILE__, __LINE__);
        }
    }

    function gpls_woo_rfq_woocommerce_before_checkout_process()
    {


    }


    function gpls_woo_rfq_woocommerce_cart_emptied()
    {


    }

    function gpls_woo_rfq_order_received()
    {

    }


    function gpls_woo_rfq_woocommerce_RFQ_load_payment_gateway()
    {
        //   add_action( 'woocommerce_pre_payment_complete', 'gpls_woo_rfq_woocommerce_pre_payment_complete',100,1 );

        //  $value = apply_filters( 'woocommerce_data_get_price', $value,$wc_data );
        //  add_filter('woocommerce_product_get_price','gpls_woo_rfq_woocommerce_data_get_price',1000,2);

        // add_action('init', 'init_gpls_rfq_payment_gateway');

        //  add_filter('woocommerce_payment_gateways', 'add_gpls_woo_rfq_class',1,1);

        // add_filter('woocommerce_available_payment_gateways', 'gpls_rfq_remove_other_payment_gateways',1000,1);
    }


    function gpls_woo_rfq_woocommerce_RFQ_only_add_to_cart()
    {
        if (!isset($_POST['_wpnonce'])
            || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'woocommerce-process_checkout')) {
            return;
        }


        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) &&
            sanitize_text_field(wp_unslash($GLOBALS["gpls_woo_rfq_checkout_option"] ?? '')) == 'rfq') {

            gpls_woo_rfq_remove_filters();

        }


    }

    function gpls_woo_rfq_filter_check()
    {
        if ($GLOBALS["gpls_woo_rfq_show_prices"] == 'yes') {

            gpls_woo_rfq_remove_filters();
            gpls_woo_rfq_remove_filters_normal_checkout();
        }
    }


    function gpls_woo_rfq_remove_filters()
    {
        // ini_set('display_errors', 'Off');

        remove_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_cart_totals_order_total_html', 'gpls_woo_rfq_total_prices');
        remove_filter('woocommerce_cart_item_price', 'gpls_woo_rfq_hide_cart_prices', 10, 3);
        remove_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_hide_woocommerce_cart_product_price', 10, 2);
        remove_filter('woocommerce_cart_product_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_product_subtotal', 10, 3);
        remove_filter('woocommerce_cart_item_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_item_subtotal', 10, 3);
        remove_filter('woocommerce_cart_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_subtotal', 10, 3);
        remove_filter('woocommerce_get_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);

        remove_filter('woocommerce_get_price_excluding_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);
        remove_filter('woocommerce_get_price_including_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);

        remove_filter('woocommerce_product_get_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_product_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        remove_filter('woocommerce_bundle_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        remove_filter('woocommerce_bundle_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_bundle_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_grouped_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_bundled_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_variation_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_variable_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_free_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);            //
        remove_filter('woocommerce_get_variation_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_get_price_html_from_to', 'gpls_woo_rfq_individual_price_html_from_to', 1000, 4);
        remove_filter('woocommerce_get_variation_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_get_variation_sale_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_get_variation_regular_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_order_formatted_line_subtotal', 'gpls_woo_rfq_order_formatted_line_subtotal', 100, 3);
        remove_filter('woocommerce_get_formatted_order_total', 'gpls_woo_rfq_get_formatted_order_total', 100, 2);
        remove_filter('woocommerce_get_order_item_totals', 'gpls_woo_rfq_woocommerce_get_order_item_totals', 100, 2);


    }


    function gpls_woo_rfq_is_purchasable($purchasable, $product)
    {

        $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];

        if ($checkout_option == "rfq") {
            return true;
        } else {
            return gpls_woo_rfq_normal_is_purchasable($purchasable, $product);
        }


    }


    function gpls_woo_rfq_normal_is_purchasable($purchasable, $product)
    {

        $product = wc_get_product();

        $rfq_enable = 'no';

        if (isset($product) && is_object($product)) {


            $pf = new WC_Product_Factory();

            $product = $pf->get_product($product->get_id());

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            //echo $product->id.' '.$rfq_enable.'<br />';
            if ($rfq_enable == 'no') {

                return $purchasable;

            }

            if ($rfq_enable == "yes") {
                return true;
            }

        }

        return $purchasable;


    }

    function gpls_woo_rfq_get_availability($availability, $product)
    {
        return true;


    }


    function gpls_woo_rfq_cart_item_remove_link($link, $cart_item_key)
    {

        //$gpls_woo_rfq_cart = get_transient(rfq_cart_tran_key() . '_' . 'gpls_woo_rfq_cart');
        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

        if (($gpls_woo_rfq_cart = false)) {
            return '';
        }


        if (isset($gpls_woo_rfq_cart[$cart_item_key])) {
            $cart_item = $gpls_woo_rfq_cart[$cart_item_key];
            if (isset($cart_item['bundled_by']) && isset($cart_item['bundled_by'])) {

                //if ( $this->get_bundled_cart_item_container( $cart_item ) )
                {

                    return '';
                }
            }
        }

        return $link;
    }

    /**
     * @return mixed|void
     */


    function gpls_woo_rfq_create_page()
    {
        $create_page_once = get_option('gpls_woo_rfq_qr_page_check');

        if ($create_page_once != "yes") {

            $checkout_option = 0;
            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == 'rfq') {
                $checkout_option = 1;
            }

            $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', NULL);

            if ($rfq_page == false) {

                if ($checkout_option == 1) {
                    try {

                        $page = get_page_params();

                        $pageid = wp_insert_post($page, false);

                        if ($pageid == true) {

                            $new_page = get_post($pageid);
                            update_option('rfq_cart_sc_section_show_link_to_rfq_page', $new_page->guid);
                        }


                    } catch (Exception $e) {

                    }
                }
            }

            update_option('gpls_woo_rfq_qr_page_check', "yes");
        }


        $allow_favorites = get_option('settings_gpls_woo_rfq_allow_favorites', 'no');

        $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
        $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

        if (($allow_favs=="yes" && is_user_logged_in()) ||
            (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {

            $create_page_once = get_option('gpls_woo_fav_qr_page_check');
            if ($create_page_once != "yes") {

                $checkout_option = 1;

                $rfq_page = get_option('rfq_cart_sc_section_link_to_favorites_page', NULL);

                if ($rfq_page == false) {

                    if ($checkout_option == 1) {
                        try {

                            $page = get_fav_page_params();

                            $pageid = wp_insert_post($page, false);

                            if ($pageid == true) {

                                $new_page = get_post($pageid);
                                update_option('rfq_cart_sc_section_link_to_favorites_page', $new_page->guid);
                            }


                        } catch (Exception $e) {

                        }
                    }
                }

                update_option('gpls_woo_fav_qr_page_check', "yes");
            }

        }


    }

    function gpls_woo_rfq_hide_rfq_page($items, $menu, $args)
    {



        $page_post = null;

        $checkout_option = 0;
        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == 'rfq') {
            $checkout_option = 1;
        }


        if (function_exists('is_user_logged_in')) {
            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == 'normal_checkout'
                && (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in())
            ) {
                // $checkout_option = 1;
            }
        }

        if ($checkout_option == 1) {

            $home = home_url() . '/quote-request/';

            $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);

            if (isset($rfq_page) &&!is_admin()) {

                $link_to_rfq_page_url = trim($rfq_page);

                if ($link_to_rfq_page_url != false && isset($link_to_rfq_page_url)) {
                    $link_to_rfq_page_url = trim(preg_replace('"' . gpls_woo_rfq_remove_http(get_site_url()) . '"', '',
                        gpls_woo_rfq_remove_http($link_to_rfq_page_url)));

                    $link_to_rfq_page_path = $link_to_rfq_page_url;

                    $page_post = get_page_by_path($link_to_rfq_page_path);
                }


                if ($page_post != null) {

                    foreach ($items as $key => $item) {
                        if ($item->object_id == $page_post->ID)
                            unset($items[$key]);
                    }

                }
            }
        }

        $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
        $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');


        if (get_option('settings_gpls_woo_rfq_allow_favorites', 'no') == 'no'
        ||get_option('settings_gpls_woo_rfq_allow_menu', 'no') == 'no'
        ||  (get_current_user_id() ==0 &&  $allow_favs_anon=='no')
        )
        {

                $rfq_page = get_option('rfq_cart_sc_section_link_to_favorites_page', NULL);

                if (isset($rfq_page)) {

                    $link_to_rfq_page_url = trim($rfq_page);

                    if ($link_to_rfq_page_url != false && isset($link_to_rfq_page_url)) {
                        $link_to_rfq_page_url = trim(preg_replace('"' . gpls_woo_rfq_remove_http(get_site_url()) . '"', '',
                            gpls_woo_rfq_remove_http($link_to_rfq_page_url)));

                        $link_to_rfq_page_path = $link_to_rfq_page_url;

                        $page_post = get_page_by_path($link_to_rfq_page_path);
                    }


                    if ($page_post != null &&!is_admin()) {

                        foreach ($items as $key => $item) {
                            if ($item->object_id == $page_post->ID)
                                unset($items[$key]);
                        }

                    }
                }

        }


        return $items;
    }

    function gpls_woo_rfq_check_menu()
    {

    //if(!is_admin())return;

        $page_post = null;
        $checkout_option = 0;

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == 'normal_checkout') {
            $checkout_option = 1;
        }

        if ($checkout_option == 11) {

            $run_once = get_option('gpls_woo_rfq_menu_check');

            if ($run_once == false) {

                $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', NULL);

                if (trim($rfq_page) != '' && $rfq_page != NULL) {

                    // $page_post_id = gpls_woo_rfq_get_id_from_guid($rfq_page);
                    $page_post_id = url_to_postid($rfq_page);

                    if ($page_post_id == null) {
                        return;
                    }

                    $page_request = get_post($page_post_id);

                    if ($page_request != null && $page_request->post_status == 'publish') {

                        $pageid = $page_request->ID;

                        try {
                            $menu_name = 'primary';
                            $locations = get_nav_menu_locations();

                            if (count($locations) == 0) {
                                return;
                            }

                            $menu_id = $locations[$menu_name];

                            $mymenu = wp_get_nav_menu_object($menu_id);
                            $menuID = (int)$mymenu->term_id;

                            $itemData = array(
                                'menu-item-object-id' => $pageid,
                                'menu-item-parent-id' => 0,
                                'menu-item-position' => 100,
                                'menu-item-object' => 'page',
                                'menu-item-type' => 'post_type',
                                'menu-item-status' => 'publish'
                            );


                            wp_update_nav_menu_item($menuID, 0, $itemData);

                            update_option('gpls_woo_rfq_menu_check', true);

                        } catch (Exception $e) {

                        }

                    }
                }


            }
        }


        /*$checkout_option = 0;
        if (get_option('settings_gpls_woo_rfq_allow_favorites', 'no') == 'yes' &&
            get_option('settings_gpls_woo_rfq_allow_menu', 'no') == 'yes'
        ) {
            $checkout_option = 1;
        }

        if ($checkout_option == 1) {

            $run_once = get_option('gpls_woo_fav_menu_check');

            if ($run_once == false) {

                $rfq_page = get_option('rfq_cart_sc_section_link_to_favorites_page', NULL);

                if (trim($rfq_page) != '' && $rfq_page != NULL) {

                    // $page_post_id = gpls_woo_rfq_get_id_from_guid($rfq_page);
                    $page_post_id = url_to_postid($rfq_page);

                    if ($page_post_id == null) {
                        return;
                    }

                    $page_request = get_post($page_post_id);

                    if ($page_request != null && $page_request->post_status == 'publish') {

                        $pageid = $page_request->ID;

                        try {
                            $menu_name = 'primary';
                            $locations = get_nav_menu_locations();

                            if (count($locations) == 0) {
                                return;
                            }

                            $menu_id = $locations[$menu_name];

                            $mymenu = wp_get_nav_menu_object($menu_id);
                            $menuID = (int)$mymenu->term_id;

                            $itemData = array(
                                'menu-item-object-id' => $pageid,
                                'menu-item-parent-id' => 0,
                                'menu-item-position' => 100,
                                'menu-item-object' => 'page',
                                'menu-item-type' => 'post_type',
                                'menu-item-status' => 'publish'
                            );


                            wp_update_nav_menu_item($menuID, 0, $itemData);
                            update_option('gpls_woo_fav_menu_check', true);


                        } catch (Exception $e) {

                        }

                    }
                }


            }
        }*/
    }

    function gpls_woo_rfq_get_id_from_guid($guid)
    {
        global $wpdb;

        return ($guid);

    }


    function gpls_woo_rfq_woocommerce_cart_totals_fee_html($total, $fee)
    {

        $temp_total = false;


        // $temp_total = apply_filters($temp_total,$total, $compound, $display, $cart);

        return $temp_total;

    }

    function gpls_woo_rfq_woocommerce_woocommerce_product_is_in_stock($status)
    {


        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
            $status = 'instock';

        }

        return $status;


    }

    /**
     * @return array
     */
    function get_page_params()
    {
        $page = array();
        $page['post_type'] = 'page';
        $page['post_content'] = '[gpls_woo_rfq_get_cart_sc]';
        $page['post_parent'] = 0;
        $page['post_status'] = 'publish';
        $page['post_title'] = 'Quote Request';
        $page['post_author'] = 1;
        $page['post_name'] = 'quote-request';
        $page['comment_status'] = 'closed';
        $page['ping_status'] = 'closed';

        return $page;
    }


    /**
     * @return array
     */
    function get_fav_page_params()
    {
        $page = array();
        $page['post_type'] = 'page';
        $page['post_content'] = '[gpls_woo_rfq_get_favs_sc]';
        $page['post_parent'] = 0;
        $page['post_status'] = 'publish';
        $page['post_title'] = 'Favorites';
        $page['post_author'] = 1;
        $page['post_name'] = 'product-favorites';
        $page['comment_status'] = 'closed';
        $page['ping_status'] = 'closed';

        return $page;
    }


    function gpls_woo_rfq_print_script_init()
    {

        if (!is_admin()) {


            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));


            $is_product_page = false;


            // $url = get_site_url() . $_SERVER['REQUEST_URI'];
            if (isset($_SERVER['REQUEST_URI'])) {
                $url = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));

            }


            $path = wp_parse_url($url, PHP_URL_PATH);
            $path_array = explode("/", trim($path, "/"));


            if ($path_array[0] == "product") {
                $is_product_page = true;
            }

            global $wp_query;
            if (isset($wp_query)) {
                if (function_exists('is_product')) {
                    if (is_product()) {
                        $is_product_page = true;
                    }
                }
            }


            if ($is_product_page == true) {

                global $product;

                if (get_the_ID() == false) return;

                if (!is_object($product) && !function_exists('wc_get_product')) return;

                if (!is_object($product)) $product = wc_get_product(get_the_ID());

                if (!isset($product) || !is_object($product)) {
                    return;
                }

                if ($product->get_type() == 'external') {
                    return;
                }

                //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
                // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                /*  if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                      $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                      DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
                  }
                  $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

                $rfq_product_script = "";

                //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
                // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                $rfq_check = false;
                $normal_check = false;
                //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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

                if ($rfq_enable != 'yes' ||
                    (isset($GLOBALS["gpls_woo_rfq_checkout_option"])
                        && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) return;

                if (($normal_check == false || get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') != 'no')) return;


                $custom_js =
                    "      
               if (typeof jQuery != 'undefined') {


    jQuery(document ).ready( function() {
    
     jQuery('.gpls_rfq_set' ).on( 'click', function() {
    // jQuery('.qty').filter(':visible').focus();
 // 
});

 jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*).show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');

    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','opacity: 1 !important;');



            jQuery( '#woo_pp_ec_button_product' ).hide();
        jQuery( '#woo_pp_ec_button_product' ).attr('style','display: none ');
        jQuery( '#single_add_to_cart_button' ).hide();
        jQuery( '#single_add_to_cart_button' ).attr('style','display: none ');
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','display: none ');

     //   jQuery('.gpls_rfq_set:input[type=\"submit\"]').focus();
    //    jQuery(document).ready(function(){jQuery('.qty').filter(':visible').focus(); });

        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','visibility: collapse');

       

    });
    
     jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');

    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','opacity: 1 !important;');
    
}
";
                wp_add_inline_script('gpls_woo_rfq_js', $custom_js);

                $custom_css = "
                .related .woocommerce-Price-amount >*,
.related .from >*,
.related .price >*,
.related .total >*
.related .amount >*
.related .bundle_price >*{
    visibility: visible !important ;
    

}

.related .woocommerce-Price-amount,
.related .price,
.related .total,
.related .bundle_price,
.related .amount{
    
    visibility:visible !important;
}
                
                .woocommerce-Price-amount,.from, .price, .product-selector__price,
                  .total, .bundle_price,.wc-pao-col2{visibility: collapse}
#woo_pp_ec_button_product {display:none !important}
.single-product div.product form.cart .single_add_to_cart_button{visibility: hidden}
.gpls_rfq_set{visibility:visible !important}

.related .woocommerce-Price-amount >*,
.related .from >*,
.related .price >*,
.related .total >*
.related .amount >*
.related .bundle_price >*{
    visibility: visible !important ;
    

}

.related .woocommerce-Price-amount,
.related .price,
.related .total,
.related .bundle_price,
.related .amount{
   
    visibility:visible !important;
}


";

                wp_add_inline_style('gpls_woo_rfq_css', $custom_css);

            }


        }

    }


    function gpls_woo_rfq_print_script()
    {

        if (!is_admin()) {


            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));


            $is_product_page = false;

// $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];
            // $url = get_site_url() . $_SERVER['REQUEST_URI'];
            if (isset($_SERVER['REQUEST_URI'])) {
                $url = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));

            }

            $path = wp_parse_url($url, PHP_URL_PATH);
            $path_array = explode("/", trim($path, "/"));


            if ($path_array[0] == "product") {
                $is_product_page = true;
            }

            global $wp_query;
            if (isset($wp_query)) {
                if (function_exists('is_product')) {
                    if (is_product()) {
                        $is_product_page = true;
                    }
                }
            }


            if ($is_product_page == true) {

                $custom_js =
                    "      
              if (typeof jQuery != 'undefined') {

    jQuery(document ).ready( function() {
     jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');

    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','opacity: 1 !important;');
        jQuery('.gpls_rfq_set' ).on( 'click', function() {
  //
  //jQuery('.qty').filter(':visible').focus();
});
       
        jQuery( '#woo_pp_ec_button_product' ).hide();
        jQuery( '#woo_pp_ec_button_product' ).attr('style','display: none ');
        jQuery( '#single_add_to_cart_button' ).hide();
        jQuery( '#single_add_to_cart_button' ).attr('style','display: none ');
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','visibility: collapse ');

      //  jQuery('.gpls_rfq_set:input[type=\"submit\"]').focus();
      //  jQuery(document).ready(function(){jQuery('.qty').filter(':visible').focus(); });

        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
        jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','visibility: collapse');

   

    });
     jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >').show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');

    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','opacity: 1 !important;');
}
";
                wp_add_inline_script('gpls_woo_rfq_js', $custom_js);

                echo "<div class='gpls_script' style='display: none'><script> "
                    . ($custom_js) . '</script></div>';


                esc_html("<div class='gpls_script'
                 style='display: none'><style>.single_add_to_cart_button 
                 { visibility: hidden} 
                 #woo_pp_ec_button_product {display:none !important}
                  .gpls_rfq_set{visibility: visible !important;}</style></div>");

                $custom_css = "
                
                .related .woocommerce-Price-amount >*,
.related .from >*,
.related .price >*,
.related .total >*
.related .amount >*
.related .bundle_price >*{
    visibility: visible !important ;
   

}

.related .woocommerce-Price-amount,
.related .price,
.related .total,
.related .bundle_price,
.related .amount{
     
     visibility: visible !important ;
}
                
                .woocommerce-Price-amount,.from, .price, .product-selector__price,  .total, .bundle_price,.wc-pao-col2 {visibility: collapse}
#woo_pp_ec_button_product {visibility: collapse}

.single-product div.product form.cart .single_add_to_cart_button{visibility: hidden}
.gpls_rfq_set{visibility:visible !important}

.related .woocommerce-Price-amount >*,
.related .from >*,
.related .price >*,
.related .total >*
.related .amount >*
.related .bundle_price >*{
    visibility: visible !important ;
     

}

.related .woocommerce-Price-amount,
.related .price,
.related .total,
.related .bundle_price,
.related .amount{
    
     visibility: visible !important ;
}
";

                wp_add_inline_style('gpls_woo_rfq_css', $custom_css);

                echo '<div class="gpls_script" style="display: none"><style>'
                    . ($custom_css) . '</style></div>';


            }


        }

    }


    function gpls_woo_rfq_print_script_show_single_add()
    {


        if (!is_admin()) {


            $rfq_product_script = "jQuery(document ).ready( function() { jQuery( '.single_add_to_cart_button' ).show();
                jQuery( '.single_add_to_cart_button' ).attr('style','visibility: visible');
                 jQuery('.single_add_to_cart_button').prop('disabled',false);;
                 jQuery('.gpls_rfq_set').prop('disabled', false);  
                  
                });";


            echo "<div class='gpls_script' style='display: none'><script> " .
                $rfq_product_script . '</script></div>';

            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
            wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);


            $custom_js =
                "jQuery(document ).ready( function() { jQuery( '.single_add_to_cart_button' ).show();
                jQuery( '.single_add_to_cart_button' ).attr('style','visibility: visible');
                 jQuery('.single_add_to_cart_button').prop('disabled',false);;
                 jQuery('.gpls_rfq_set').prop('disabled', false);
                 
                
                });";

            wp_add_inline_script('gpls_woo_rfq_js', $custom_js);


            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));

            $custom_css = ".single_add_to_cart_button {visibility:visible;} ";
            wp_add_inline_style('gpls_woo_rfq_css', $custom_css);
        }

    }

//
// $product_id, $quantity
    function gpls_woo_rfq_woocommerce_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        $request = $_REQUEST;
        $is_set = false;

        gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart_notices');

        //  ini_set('display_errors', 'Off');


        global $woocommerce;

        gpls_woo_rfq_remove_filters_normal_checkout();

        $checkout_option = "normal_checkout";

        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"])) {
            $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];
        }


        $product = wc_get_product($product_id);

        $rfq_enable_rfq_checkout = gpls_woo_get_rfq_enable($product);

        $rfq_enable_rfq_checkout = apply_filters('rfq_enable_rfq_checkout_filter',
            $rfq_enable_rfq_checkout, $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data);

        if ($checkout_option == "rfq") {

            add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2);
            add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2);

            add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);
            add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);

            do_action('gpls_woo_rfq_woocommerce_add_to_woo_cart_action',
                $rfq_enable_rfq_checkout, $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data);

            return;
        }

        if (!(isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key(wp_unslash($_POST['_wpnonce'])), 'rfq_id_nonce')
        )

        ) {

            if ($checkout_option == "normal_checkout") {

                if ($rfq_enable_rfq_checkout == 'yes') {

                    WC()->cart->remove_cart_item($cart_item_key);

                    return;
                }
            }

        } else {
           // $request = $_REQUEST;
            $is_set = false;
        }


        $product = wc_get_product($product_id);
        $rfq_enable = gpls_woo_get_rfq_enable($product);

        $is_an_rfq = false;


        if (($rfq_enable == 'yes'
                && isset($_REQUEST["rfq_product_id"]) && $_REQUEST["rfq_product_id"] != "-1")
            || ($rfq_enable == 'yes' && isset($_GET["add-to-cart"]))) {
            $is_an_rfq = true;
        }
        if (($rfq_enable == 'yes' && isset($_GET["add-to-cart"]))
            && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'
            && !isset($_REQUEST["rfq_product_id"])
        ) {
            $is_an_rfq = false;
        }


        $is_an_rfq = apply_filters('gpls_woo_rfq_is_an_rfq_add_to_cart', $is_an_rfq, $_REQUEST, $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $rfq_enable);


        $product = wc_get_product($product_id);

        do_action('gpls_woo_rfq_woocommerce_before_add_to_rfq_cart', $is_an_rfq, $request, $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data);

        $quantity = apply_filters('gpls_woo_rfq_woocommerce_quantity_add_to_rfq_cart', $quantity, $cart_item_key, $product_id);


        do_action('gpls_woo_rfq_woocommerce_add_to_cart_is_normal', $is_an_rfq, $request, $cart_item_key, $product_id, $quantity, $variation_id, $variation,
            $cart_item_data, $rfq_enable);

        if ($is_an_rfq) {

            WC()->cart->cart_contents[$cart_item_key]['keep'] = 'no';


            add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);
            add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);

            add_filter('wc_add_to_cart_message_html', 'gpls_woo_rfq_remove_cart_notices', 1, 3);


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart == false)) {
                $gpls_woo_rfq_cart = array();
            }

            if (isset($gpls_woo_rfq_cart[$cart_item_key])) {
                $old_qty = $gpls_woo_rfq_cart[$cart_item_key]['quantity'];
            } else {
                $old_qty = 0;
            }

            $new_quantity = $old_qty + $quantity;

            $new_quantity = apply_filters('gpls_woo_rfq_woocommerce_quantity_add_to_rfq_cart', $new_quantity, $cart_item_key, $product_id);

            $gpls_woo_rfq_cart[$cart_item_key] = WC()->cart->cart_contents[$cart_item_key];

            $gpls_woo_rfq_cart[$cart_item_key]['quantity'] = $new_quantity;

            $gpls_woo_rfq_cart[$cart_item_key]['product'] = $product;


            do_action('gpls_woo_rfq_add_custom_products', $product, $cart_item_key);

            $cart = WC()->cart;

            do_action('gpls_woo_rfq_add_to_cart_set_transient', $gpls_woo_rfq_cart, $cart, $product, $cart_item_key);

            $gpls_woo_rfq_cart_do_set = true;

            $gpls_woo_rfq_cart_do_set = apply_filters('gpls_woo_rfq_cart_do_set', $gpls_woo_rfq_cart_do_set, $is_an_rfq, $gpls_woo_rfq_cart, $cart, $product, $cart_item_key);

            if ($gpls_woo_rfq_cart_do_set) {

                $get_record = gpls_woo_rfq_cart_set('gpls_woo_rfq_cart', $gpls_woo_rfq_cart);

                if ($get_record != false) {
                    $is_set = "yes";
                }
            }


        } else {

            // exit;
            WC()->cart->cart_contents[$cart_item_key]['keep'] = 'yes';


        }

        if ($is_an_rfq) {
            if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes') {


                WC()->cart->set_quantity($cart_item_key, WC()->cart->get_cart_item_quantities()[$product_id] - $quantity);

                if (isset(WC()->cart->get_cart_item_quantities()[$product_id]) && WC()->cart->get_cart_item_quantities()[$product_id] == 0) {
                    WC()->cart->remove_cart_item($cart_item_key);
                }
                WC()->cart->persistent_cart_update();
                WC()->cart->get_totals();
            } else {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        gpls_woo_rfq_add_filters_normal_checkout();

        if ($is_set) {

            do_action('gpls_woo_rfq_woocommerce_after_add_to_rfq_cart_finished', $product_id, $is_set);
        }


    }


    /**
     * @param $product
     * @return bool
     */


    function gpls_woo_rfq_remove_filters_normal_checkout()
    {
        remove_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_cart_totals_order_total_html', 'gpls_woo_rfq_total_prices');
        remove_filter('woocommerce_cart_item_price', 'gpls_woo_rfq_hide_cart_prices', 10, 3);
        remove_filter('woocommerce_cart_product_price', 'gpls_woo_rfq_hide_woocommerce_cart_product_price', 10, 2);
        remove_filter('woocommerce_cart_product_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_product_subtotal', 10, 3);
        remove_filter('woocommerce_cart_item_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_item_subtotal', 10, 3);
        remove_filter('woocommerce_cart_subtotal', 'gpls_woo_rfq_hide_woocommerce_cart_subtotal', 10, 3);

        remove_filter('woocommerce_get_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_get_price_excluding_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);//remove at checkout
        remove_filter('woocommerce_get_price_including_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);//remove at checkout
        remove_filter('woocommerce_product_get_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);//remove at checkout
        remove_filter('woocommerce_product_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        remove_filter('woocommerce_bundle_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        remove_filter('woocommerce_bundle_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_bundle_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_grouped_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_bundled_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_variation_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_variable_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_free_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);            //
        remove_filter('woocommerce_get_variation_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        remove_filter('woocommerce_get_price_html_from_to', 'gpls_woo_rfq_individual_price_html_from_to', 1000, 4);//remove at checkout
        remove_filter('woocommerce_get_variation_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_get_variation_sale_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_get_variation_regular_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        remove_filter('woocommerce_order_formatted_line_subtotal', 'gpls_woo_rfq_order_formatted_line_subtotal', 100, 3);
        remove_filter('woocommerce_get_formatted_order_total', 'gpls_woo_rfq_get_formatted_order_total', 100, 2);
        remove_filter('woocommerce_get_order_item_totals', 'gpls_woo_rfq_woocommerce_get_order_item_totals', 100, 2);


    }

    function gpls_woo_rfq_add_filters_normal_checkout()
    {


        add_filter('woocommerce_get_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);


        if (
            isset($_POST['gpls_woo_rfq_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_POST['gpls_woo_rfq_nonce'])), 'gpls_woo_rfq_handle_rfq_cart_nonce') &&
            !isset($_POST['gpls-woo-rfq_checkout']) && !isset($_POST['woocommerce-process-checkout-nonce'])) {

            add_filter('woocommerce_get_price_excluding_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);
            add_filter('woocommerce_get_price_including_tax', 'gpls_woo_rfq_individual_price_hidden_tax', 1000, 3);

        }

        add_filter('woocommerce_product_get_price', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_product_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        add_filter('woocommerce_bundle_is_on_sale', 'gpls_woo_rfq_product_is_on_sale', 1000, 2);
        add_filter('woocommerce_bundle_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_bundle_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_grouped_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_bundled_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_variation_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_variable_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_free_sale_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_free_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);            //
        add_filter('woocommerce_get_variation_price_html', 'gpls_woo_rfq_individual_price_hidden_html', 1000, 2);
        add_filter('woocommerce_get_price_html_from_to', 'gpls_woo_rfq_individual_price_html_from_to', 1000, 4);
        add_filter('woocommerce_get_variation_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        add_filter('woocommerce_get_variation_sale_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        add_filter('woocommerce_get_variation_regular_price', 'gpls_woo_rfq_individual_price_hidden_variation_html', 1000, 4);
        add_filter('woocommerce_order_formatted_line_subtotal', 'gpls_woo_rfq_order_formatted_line_subtotal', 100, 3);
        add_filter('woocommerce_get_formatted_order_total', 'gpls_woo_rfq_get_formatted_order_total', 100, 2);
        add_filter('woocommerce_get_order_item_totals', 'gpls_woo_rfq_woocommerce_get_order_item_totals', 100, 2);


    }

    function gpls_woo_rfq_remove_cart_notices($message, $products, $show_qty)
    {


        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            $link_to_rfq_page = wc_get_cart_url();
        } else {
            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
        }

        $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));

        $product_was_added_to_quote_request = gpls_woo_rfq_get_option('rfq_cart_wordings_product_was_added_to_quote_request', "Product was successfully added to quote request.");
        $product_was_added_to_quote_request = __($product_was_added_to_quote_request, 'woo-rfq-for-woocommerce');

        $link = wc_get_template_html('woo-rfq/link-to-cart-shop.php', array('link_to_rfq_page' => $link_to_rfq_page,), '', gpls_woo_rfq_WOO_PATH);


        $message = $product_was_added_to_quote_request . $link;
        //$message = $product_was_added_to_quote_request . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  class="rfqcart-link-shop rfqcart-link-shop-custom" href="' . $link_to_rfq_page . '" >' . $view_your_cart_text . '</a>';


        //$notice = get_transient('gpls_woo_rfq_auction_notices');
        $notice = gpls_woo_rfq_get_item('gpls_woo_rfq_cart_notices');

        //  $notice = __($notice, 'woo-rfq-for-woocommerce');

        $notice_message = sprintf(
        /* translators: notice label. */
            esc_html(isset($notice["message"]) ? $notice["message"] : "")
        );

//d($all_notices);
        if (isset($notice["type"])) {
            $message = $message . '<br />' . $notice_message;
        }

        //delete_transient('gpls_woo_rfq_auction_notices');
        gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart_notices');

        return $message;

    }


    function gpls_woo_rfq_get_option($string, $default)
    {

        $option = get_option($string, __($default, 'woo-rfq-for-woocommerce'));


        return $option;

    }


    function gpls_woo_rfq_cart_needs_payment($needs_payment, $cart)
    {

        if (WC()->cart != null) {

            $totals = WC()->cart->get_totals();

            if (get_option('settings_gpls_woo_rfq_plus_skip_zero_cart') == "yes"
                && $totals['total'] == 0) {

                return false;
            }
        }

        return true;
    }


    function gpls_woo_create_an_account_function()
    {
        $home = home_url() . '/quote-request/';

        $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);

        //
        // $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['REQUEST_URI'])) {
            $actual_link = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));

        }

        if ((strtolower(wp_parse_url(trim($rfq_page))['path'])) !==
            (strtolower(wp_parse_url(trim($actual_link))['path']))) {
            return;
        }
        $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_password.js';
        $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_password.js';
        wp_enqueue_script('gpls_woo_password_js', $url_js, array('jquery', 'password-strength-meter'), wp_rand(10, 100000), true);

        $account_options = get_option('rfq_cart_sc_section_rfq_page_create_accounts');

        $localized_script = array(
            'account_option' => $account_options,
        );

        wp_localize_script('gpls_woo_password_js', 'localized_script', $localized_script);


        wc_get_template('woo-rfq/account_password.php',
            array(), '', gpls_woo_rfq_WOO_PATH);

    }


    function filter_gpls_woo_rfq_add_show_prices_to_wc_emails($args)
    {


        $args['show_prices'] = true;
        return $args;
    }


    function filter_gpls_woo_rfq_add_hide_prices_to_wc_emails($args)
    {

        $args['show_prices'] = false;
        return $args;
    }

    function is_rfq_enabled($_product_id)
    {
        //WC()->init();
        $_product = wc_get_product($_product_id);


        //  $rfq_enable = get_post_meta($_product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
        //  $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $_product->get_id());

        $rfq_enable = gpls_woo_get_rfq_enable($_product);

        return $rfq_enable;
    }


    if (!function_exists('gpls_woo_rfq_is_view_order_page')) {
        function gpls_woo_rfq_is_view_order_page()
        {


            //  $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];

            if (isset($_SERVER['REQUEST_URI'])) {
                $actual_link = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));

            }

            $current_page = gpls_woo_rfq_remove_http((trim(preg_replace('{/$}', '', $actual_link))));
            $page_id = wc_get_page_id('myaccount');
            $perma_link = gpls_woo_rfq_remove_http(wc_get_page_permalink('myaccount'));
            $result = (gpls_woo_rfq_plus_startsWith($current_page, $perma_link) && isset($wp->query_vars['view-order']));

            return $result;
        }
    }

    add_filter('bulk_actions-edit-product', 'gpls_woo_rfq_product_bulk_actions', 100);
    add_filter('handle_bulk_actions-edit-product', 'gpls_woo_rfq_handle_product_bulk_actions', 100, 3);
    add_filter('manage_posts_columns', 'gpls_woo_rfqtk_columns_head');


    add_action('manage_posts_custom_column', 'gpls_woo_rfqtk_columns_content', 10, 2);

    add_filter('manage_edit-product_sortable_columns', 'gpls_woo_rfqtk_sortable_columns');
    add_filter('pre_get_posts', 'gpls_woo_rfqtk_column_orderby');

    add_filter('manage_edit-product_columns', 'gpls_woo_rfq_basic_column', 20);
    add_action('manage_posts_custom_column', 'gpls_woo_rfq_basic_values');


    function gpls_woo_rfq_basic_values($column_name)
    {

        if ($column_name == '_gpls_woo_rfq_rfq_enable') {
            // if you suppose to display multiple brands, use foreach();

            $col = get_post_meta(get_the_ID(), '_gpls_woo_rfq_rfq_enable', true);
            if ($col == "yes") {
                echo "Y";
            } else {
                echo "N";
            }

        }


    }


    function gpls_woo_rfq_basic_column($columns_array)
    {

        if (!$columns_array) return $columns_array;
        return array_slice($columns_array, 0, 11, true)
            + array('_gpls_woo_rfq_rfq_enable' => 'RFQ')

            + array_slice($columns_array, 11, NULL, true);


    }


    function gpls_woo_rfqtk_columns_content($column_name, $post_ID)
    {
        //  WordPress.Security.NonceVerification.Recommended

        if (!isset($_GET['post_type']) || $_GET['post_type'] != 'shop_order')// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return $column_name;
        }

        if ($column_name == '_gpls_woo_rfq_rfq_enable') {
            $size = get_post_meta($post_ID, '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = get_post_meta($post_ID, '_gpls_woo_rfq_rfq_enable', true);
            //  $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $post_ID);
            $size = !isset($size) ? '' : $rfq_enable;
            if ($size == 'yes') {
                $size = 'X';
            } else {
                $size = '';
            }

            echo esc_html($size);
        }
    }


    function gpls_woo_rfqtk_column_orderby($query)
    {

        $orderby = $query->get('orderby');

        if ('RFQ' == $orderby) {

            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key' => '_gpls_woo_rfq_rfq_enable',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => '_gpls_woo_rfq_rfq_enable',
                    'compare' => 'NOT EXISTS'
                ),
            );

            $query->set('meta_query', $meta_query);
            $query->set('orderby', 'meta_value');

        }
    }


    function gpls_woo_rfqtk_sortable_columns($columns)
    {


        $columns['_gpls_woo_rfq_rfq_enable'] = 'RFQ';

        return $columns;
    }

    function gpls_woo_rfqtk_columns_head($defaults)
    {


        if (!isset($_GET['post_type']) || $_GET['post_type'] != 'shop_order')// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return $defaults;
        }
        if (get_query_var('post_type')) {
            $defaults['_gpls_woo_rfq_rfq_enable'] = 'RFQ';
        }

        return $defaults;
    }


    function gpls_woo_rfq_handle_product_bulk_actions($redirect_to, $action, $ids)
    {

        $product_types = array('_gpls_woo_rfq_rfq_enable', '_gpls_woo_rfq_rfq_enable_remove'
        );

        if (false === strpos($action, '_gpls_woo_rfq_') && !in_array($action, $product_types)) {
            return $redirect_to;
        }

        foreach ($ids as $id) {

            $product = wc_get_product($id);

            switch ($action) {

                case '_gpls_woo_rfq_rfq_enable':
                {
                    update_post_meta($id, '_gpls_woo_rfq_rfq_enable', 'yes');

                    $type_array = array('variable-subscription', 'variable');
                    if (in_array($product->get_type(), $type_array)) {
                        $product_children = $product->get_available_variations();

                        if (count($product_children) > 0) {
                            foreach ($product_children as $key => $val) {

                                update_post_meta($val["variation_id"], '_gpls_woo_rfq_rfq_enable', 'yes');
                            }
                        }

                    }


                    break;
                }

                case '_gpls_woo_rfq_rfq_enable_remove':
                {
                    update_post_meta($id, '_gpls_woo_rfq_rfq_enable', 'no');

                    $type_array = array('variable-subscription', 'variable');
                    if (in_array($product->get_type(), $type_array)) {
                        $product_children = $product->get_available_variations();

                        if (count($product_children) > 0) {
                            foreach ($product_children as $key => $val) {

                                update_post_meta($val["variation_id"], '_gpls_woo_rfq_rfq_enable', 'no');
                            }
                        }

                    }


                    break;
                }


            }

            $changed++;
        }

        $redirect_to = add_query_arg(array(
            'post_type' => 'product',
            'changed' => $changed,
            'ids' => join(',', $ids),
        ), $redirect_to);

        return esc_url_raw($redirect_to);
    }

    function gpls_woo_rfq_product_bulk_actions($actions)
    {

        $actions['_gpls_woo_rfq_rfq_enable'] = __('RFQ: enable RFQ', 'woo-rfq-for-woocommerce');
        $actions['_gpls_woo_rfq_rfq_enable_remove'] = __('RFQ: Undo enable RFQ', 'woo-rfq-for-woocommerce');
        return $actions;
    }
}

