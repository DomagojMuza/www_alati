<?php
/**
 * Functions used by the plugin
 */

// phpcs:disable
//WordPress.PHP.DevelopmentFunctions.error_log_error_log
//this is for customer support and debugging
if (!function_exists('np_write_log')) {
    function np_write_log($log, $file, $line)
    {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {

            if (is_resource($log)) {
                $log = "resource variable ";
            }

            //   WordPress.PHP.DevelopmentFunctions

            error_log('');
            error_log('*******************************************************************');
            error_log('BEGIN ' . $file . ' ' . $line);
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
            error_log('END ' . $file . ' ' . $line);
            error_log('*******************************************************************');
            error_log('');

        }
    }
}
// phpcs:enable
if (!function_exists('gpls_woo_is_checkout_block')) {
    function gpls_woo_is_checkout_block()
    {
        return \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default();
    }
}


if (!function_exists('np_pls_qr_kses_allowed_html')) {

    function np_pls_qr_kses_allowed_html($allowed_html, $context)
    {
        if ($context === 'post') {
            $allowed_html = array(
                'input' => array(
                    'type' => array(),
                    'name' => array(),
                    'value' => array(),
                    'checked' => array(),
                    'min' => array(),
                    'max' => array()
                ),
            );
        }
        return $allowed_html;
    }
    //  add_filter('wp_kses_allowed_html', 'np_pls_qr_kses_allowed_html', 1000, 2);
}
//   WordPress.WP.I18n.NoEmptyStrings


add_filter('woocommerce_valid_order_statuses_for_payment_complete', 'rfqtk_statuses_for_payment', 100, 2);
add_filter('woocommerce_valid_order_statuses_for_payment', 'rfqtk_statuses_for_payment', 100, 2);
//apply_filters( 'woocommerce_data_get_stock_quantity', $value, 'WC_Data' );

add_filter('woocommerce_product_get_price', 'gpls_woo_rfq_woocommerce_data_get_price', 1000, 2);
add_filter('woocommerce_variation_prices_price', 'gpls_woo_rfq_woocommerce_data_variation_get_price', 1000, 3);

add_action('woocommerce_payment_complete', 'gpls_woo_rfq_woocommerce_pre_payment_complete', 100, 1);

add_filter('woocommerce_can_reduce_order_stock', 'rfqtk_can_reduce_order_stock', 1000, 2);


if (!function_exists('rfqtk_can_reduce_order_stock')) {
    function rfqtk_can_reduce_order_stock($flag, $order)
    {


        $statuses = array('wc-gplsquote-sent', 'gplsquote-sent', 'wc-gplsquote-req', 'gplsquote-req');

        $status = $order->get_status();

        $status = 'wc-' === substr($status, 0, 3) ? substr($status, 3) : $status;

        if (in_array($status, $statuses)) {
            return false;
        }

        return $flag;

    }
}


if (!function_exists('np_get_option')) {
    function np_get_option($option_name, $default = null)
    {


        $option = get_option($option_name, $default);

        if ((is_null($option) || empty($option)) && $default != null) {
            return $default;
        }
        return $option;
    }
}

if (!function_exists('np_is_array')) {
    function np_is_array($array, $key)
    {

        return is_array($array) && isset($array[$key]) && count($array) > 0;
    }
}


if (!function_exists('np_check_array_element')) {
    function np_check_array_element($array, $key)
    {

        return is_array($array) && isset($array[$key]) && $array[$key] != null;
    }
}


if (!function_exists('np_is_array')) {
    function np_is_array($array, $key)
    {

        return is_array($array) && isset($array[$key]) && count($array) > 0;
    }
}

if (!function_exists('rfqtk_first_main')) {

    function rfqtk_first_main()
    {

        $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', '');


        // This runs before WooCommerce or post types is loaded. Can't use wp_query functions either.
        //get info see if this is thank you page for a quote( maybe hide prices or a real payment(show payments.

        $exit = false;
        //  WordPress.Security.NonceVerification.Recommended
//WooCommerce payment page no nonce
        // phpcs:disable

        if (isset($_REQUEST['pay_for_order']) && (isset($_REQUEST['key'])
                //  WordPress.Security.NonceVerification.Recommended
                && strpos(sanitize_key(wp_unslash($_REQUEST['key'])), 'wc_order_', 0) === 0))//  WordPress.Security.NonceVerification.Recommended
        {

            $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
            $GLOBALS["hide_for_visitor"] = "no";

            // $exit = true;
            return true;
        }

        $url = '';

        if (function_exists('get_site_url')) {
            //   WordPress.Security.NonceVerification.Recommended
            //WooCommerce payment page no nonce

            if (isset($_SERVER['REQUEST_URI'])) {
                $url = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));

            }

        }

        $order_id = false;
        $post_status = false;

        $has_string = strpos($url, 'order-received');

        $hops = get_option('woocommerce_custom_orders_table_enabled');


//   WordPress.Security.NonceVerification.Recommended
        //WooCommerce payment page no nonce
        if ($has_string !== false && (isset($_REQUEST['key'])
                && strpos(sanitize_key(wp_unslash($_REQUEST['key'])), 'wc_order_', 0) === 0))//   WordPress.Security.NonceVerification.Recommended
        {

            global $wpdb;
            //  
            if ($hops !== "yes") {

                $order_id = $wpdb->get_var($wpdb->prepare("SELECT post_id 
            FROM {$wpdb->prefix}postmeta WHERE meta_key = '_order_key' AND meta_value = %s", sanitize_key(wp_unslash($_REQUEST['key']))));

                //   WordPress.DB.DirectDatabaseQuery
                $post_status = $wpdb->get_var($wpdb->prepare("SELECT post_status FROM {$wpdb->prefix}posts WHERE ID = %s", $order_id));
            } else {

                //   WordPress.DB.DirectDatabaseQuery
                $order_id = $wpdb->get_var($wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}wc_order_operational_data
                WHERE order_key = %s", sanitize_key(wp_unslash($_REQUEST['key']))));//db call ok

                //   WordPress.DB.DirectDatabaseQuery
                $post_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$wpdb->prefix}wc_orders WHERE id = %s", $order_id));

            }


            if (class_exists('GPLS_WOO_RFQ_PLUS') && get_option('rfq_cart_sc_section_hide_price_to_thankyou_page', 'no') == 'yes'
                && ($post_status == 'wc-gplsquote-req'
                    || $post_status == 'gplsquote-req')) {

                $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
                $GLOBALS["hide_for_visitor"] = "yes";
            }


            if ($post_status !== 'wc-gplsquote-req') {
                $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
                $GLOBALS["hide_for_visitor"] = "no";

                //$exit = true;
                return true;
            }
        }

        return $exit;

    }
// phpcs:enable

}


if (!function_exists('gpls_woo_get_rfq_enable')) {
    function gpls_woo_get_rfq_enable($product)
    {
        if (!$product) return "no";

        $product_id = $product->get_id();


        $rfq_enable = "no";

        $rfq_enable = get_post_meta($product_id, '_gpls_woo_rfq_rfq_enable', true);

        if ($rfq_enable != "yes" || empty($rfq_enable)) {
            if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_plus_get_rfqc_enable')) {
                $rfq_enable = gpls_woo_plus_get_rfqc_enable($product_id);
            }
        }

        $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product_id);

        if (empty($rfq_enable)) {
            $rfq_enable = "no";
        }
        return $rfq_enable;
    }
}


if (!function_exists('gpls_woo_rfq_get_hide_price')) {
    function gpls_woo_rfq_get_hide_price($product)
    {
        $product_id = $product->get_id();

        $hide_price = get_post_meta($product_id, '_gpls_woo_rfq_hide_price', true);

        if ($hide_price != "yes" || empty($hide_price)) {
            if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_plus_get_gpls_woo_plus_getc_hide_price')) {
                $hide_price = gpls_woo_plus_get_gpls_woo_plus_getc_hide_price($product_id);
            }
        }

        $hide_price = apply_filters('gpls_hide_product_price', $hide_price, $product_id);

        if (empty($hide_price)) {
            $hide_price = "no";
        }

        return $hide_price;
    }
}

if (!function_exists('np_is_array')) {
    function np_is_array($array, $key)
    {

        return is_array($array) && isset($array[$key]) && count($array) > 0;
    }
}

if (!function_exists('np_is_array')) {
    function np_is_array($array, $key)
    {

        return is_array($array) && isset($array[$key]) && count($array) > 0;
    }
}


if (!function_exists('np_check_array_element')) {
    function np_check_array_element($array, $key)
    {

        return is_array($array) && isset($array[$key]) && $array[$key] != null;
    }
}


function gpls_woo_rfq_woocommerce_pre_payment_complete($orderid)
{

    $order = WC_Order_Factory::get_order($orderid);

    if ($order->get_payment_method() == 'gpls-rfq' && $order->get_status() != 'wc-gplsquote-req'
        && $order->get_status() != 'gplsquote-req'
    ) {
        $order->update_status('wc-gplsquote-req', __('RFQ', 'woo-rfq-for-woocommerce'));
        // $order->save();
    }
}


function gpls_woo_rfq_woocommerce_data_get_price($base_price, $_product)
{

    if (!is_admin()) {

        $rfq_enable = 'no';
        $quote = "no";

        $checkout_option = "normal_checkout";
        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && !empty($GLOBALS["gpls_woo_rfq_checkout_option"])) {
            $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];
        }

        if ($checkout_option == "rfq") {
            $quote = "yes";
        }

        if ($checkout_option == "normal_checkout") {

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($_product);
            //echo $product->id.' '.$rfq_enable.'<br />';
            if ($rfq_enable == 'yes') {
                $quote = "yes";
            } else {
                $quote = "no";
            }


        }

        if (empty($base_price) && $quote == "yes") {
            return 0;
        }
    }
    return $base_price;
}

function gpls_woo_rfq_woocommerce_data_variation_get_price($base_price, $variation, $product)
{


    if (!is_admin()) {

        $rfq_enable = 'no';
        $quote = "no";

        $checkout_option = "normal_checkout";
        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && !empty($GLOBALS["gpls_woo_rfq_checkout_option"])) {
            $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];
        }

        if ($checkout_option == "rfq") {
            $quote = "yes";
        }

        if ($checkout_option == "normal_checkout") {

            //$rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            // $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            //echo $product->id.' '.$rfq_enable.'<br />';
            if ($rfq_enable == 'yes') {
                $quote = "yes";
            } else {
                $quote = "no";
            }


        }

        if (empty($base_price) && $quote == "yes") {
            return 0;
        }
    }

    return $base_price;
}


if (!function_exists('rfqtk_statuses_for_payment')) {

    function rfqtk_statuses_for_payment($array, $order)
    {

        array_push($array, 'gplsquote-sent');
        array_push($array, 'wc-gplsquote-sent');
        return $array;
    }
}


if (!function_exists('gpls_woo_rfq_get_mode')) {
    function gpls_woo_rfq_get_mode(&$rfq_check, &$normal_check)
    {
        $rfq_check = false;
        $normal_check = false;

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
            add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);
            $rfq_check = true;
            $normal_check = false;
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

    }
}

//add_filter( 'woocommerce_get_price_html','gpls_woo_rfq_woocommerce_empty_price_html',10,2 );

if (!function_exists('gpls_woo_rfq_woocommerce_empty_price_html')) {
    function gpls_woo_rfq_woocommerce_empty_price_html($html, $product)
    {

        if (isset($product) && is_object($product)) {

            if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {


                $data = $product->get_data();

                $this_price = $data["price"];

                if (trim($data["sale_price"]) != '') {
                    $this_price = $data["sale_price"];
                }

                $type = $product->get_type();
                if ($type == 'simple' || $type == 'variable') {
                    if (trim($this_price) === '') {

                        //  return false;
                    }
                }


            }
        }
        return $html;
    }
}


if (!function_exists('gpls_woo_rfq_plus_startsWith')) {
    function gpls_woo_rfq_plus_startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('gpls_woo_rfq_plus_endsWith')) {
    function gpls_woo_rfq_plus_endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}

if (!function_exists('gpls_empty')) {
    function gpls_empty($var)
    {
        if (!isset($var) || $var == false) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('gpls_woo_rfq_add_notice')) {
    function gpls_woo_rfq_add_notice($message, $type = 'info')
    {
        //$all_notices  = array();
        $notice = array('message' => $message, 'type' => $type, 'expired' => false);

        $gpls_woo_rfq_cart_notices = gpls_woo_rfq_get_item('gpls_woo_rfq_cart_notices');

        if (is_array($gpls_woo_rfq_cart_notices)) {
            array_push($gpls_woo_rfq_cart_notices, $gpls_woo_rfq_cart_notices);
        }

        gpls_woo_rfq_cart_set('gpls_woo_rfq_cart_notices', $notice);

    }
}

if (!function_exists('gpls_woo_rfq_print_notices')) {
    function gpls_woo_rfq_print_notices()
    {

        $notice = gpls_woo_rfq_get_item('gpls_woo_rfq_cart_notices');


        if (isset($notice['type']) && trim($notice['message']) != "") {
            ?>

            <?php if ($notice['type'] == 'error') : ?>
                <div class="woocommerce-error">
                    <?php esc_html(trim(wp_kses_post($notice['message']))); ?>
                </div>
            <?php endif; ?>
            <?php if ($notice['type'] == 'info') : ?>
                <div class="woocommerce-info">
                    <?php esc_html(trim(wp_kses_post($notice['message']))); ?>
                </div>
            <?php endif; ?>
            <?php if ($notice['type'] == 'notice') : ?>
                <div class="woocommerce-notice">
                    <?php esc_html(trim(wp_kses_post($notice['message']))); ?>
                </div>
            <?php endif; ?>


            <?php

        }
        gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart_notices');

    }
}

if (!function_exists('rfq_cart_get_item_data')) {
    function rfq_cart_get_item_data($cart_item, $flat = false)
    {
        $item_data = array();


        if ($cart_item['data']->is_type('variation') && is_array($cart_item['variation'])) {
            foreach ($cart_item['variation'] as $name => $value) {
                if (is_array($name)) continue;


                $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                if (taxonomy_exists($taxonomy)) {

                    $term = get_term_by('slug', $value, $taxonomy);
                    if (!is_wp_error($term) && $term && $term->name) {
                        $value = $term->name;
                    }
                    $label = wc_attribute_label($taxonomy);
                } else {

                    $value = apply_filters('woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data']);
                    $label = wc_attribute_label(str_replace('attribute_', '', $name), $cart_item['data']);
                }


                if ('' === $value || wc_is_attribute_in_product_name($value, $cart_item['data']->get_name())) {
                    // continue;
                }

                $item_data[] = array(
                    'key' => $label,
                    'value' => $value,
                );
            }
        }


        $item_data = apply_filters('woocommerce_get_item_data', $item_data, $cart_item);

        foreach ($item_data as $key => $data) {
            // Set hidden to true to not display meta on cart.
            if (!empty($data['hidden'])) {
                unset($item_data[$key]);
                continue;
            }

            $item_data[$key]['key'] = !empty($data['key']) ? $data['key'] : $data['name'];
            $item_data[$key]['display'] = !empty($data['display']) ? $data['display'] : $data['value'];
        }


        if (count($item_data) > 0) {
            ob_start();

            if ($flat) {
                foreach ($item_data as $data) {
                    echo esc_html($data['key']) . ': ' . wp_kses_post($data['display']) . "\n";
                }
            } else {
                wc_get_template('cart/cart-item-data.php',
                    array('item_data' => $item_data)
                );
            }

            return ob_get_clean();
        }

        return '';
    }

}

if (!function_exists('rfq_cart_get_item_data_old')) {
    function rfq_cart_get_item_data_old($cart_item, $flat = false)
    {
        $item_data = array();

        // Variation data
        if (isset($cart_item['data']->variation_id) && is_array($cart_item['variation'])) {

            foreach ($cart_item['variation'] as $name => $value) {

                if ('' === $value)
                    continue;

                $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                // If this is a term slug, get the term's nice name
                if (taxonomy_exists($taxonomy)) {
                    $term = get_term_by('slug', $value, $taxonomy);
                    if (!is_wp_error($term) && $term && $term->name) {
                        $value = $term->name;
                    }
                    $label = wc_attribute_label($taxonomy);

                    // If this is a custom option slug, get the options name
                } else {
                    $value = apply_filters('woocommerce_variation_option_name', $value);
                    $label = wc_attribute_label(str_replace('attribute_', '', $name), $cart_item['data']);
                }

                $item_data[] = array(
                    'key' => $label,
                    'value' => $value
                );
            }
        }

        // Filter item data to allow 3rd parties to add more to the array
        $item_data = apply_filters('woocommerce_get_item_data', $item_data, $cart_item);


        // Format item data ready to display
        foreach ($item_data as $key => $data) {
            // Set hidden to true to not display meta on cart.
            if (isset($data['hidden'])) {
                unset($item_data[$key]);
                continue;
            }

            $item_data[$key]['key'] = isset($data['key']) && $data['key'] != "" ? $data['key'] : $data['name'];
            $item_data[$key]['display'] = isset($data['display']) && $data['display'] != "" ? $data['display'] : $data['value'];
        }

        // Output flat or in list format
        if (sizeof($item_data) > 0) {
            //ob_start();

            if ($flat) {
                foreach ($item_data as $data) {

                    echo esc_html($data['key']) . ': ' . ($data['display']) . "\n";
                }
            } else {
                wc_get_template('cart/cart-item-data.php',
                    array('item_data' => $item_data)
                );

                return;
            }

            //return ob_get_clean();
        }

        return '';


    }
}

add_action('woocommerce_before_calculate_totals', 'gpls_woo_rfq_remove_warnings', -1000);
add_action('woocommerce_remove_cart_item', 'gpls_woo_rfq_remove_cart_item_warnings', -1000, 2);
if (!function_exists('gpls_woo_rfq_remove_warnings')) {

    function gpls_woo_rfq_remove_warnings()
    {
        // ini_set('display_errors', 'Off');


    }
}
if (!function_exists('gpls_woo_rfq_remove_cart_item_warnings')) {

    function gpls_woo_rfq_remove_cart_item_warnings($cart_item_key, $cart)
    {
        // ini_set('display_errors', 'Off');


    }
}


if (!function_exists('gpls_woo_rfq_order_needs_shipping')) {
    function gpls_woo_rfq_order_needs_shipping($order_id)
    {

        $order = new WC_Order($order_id);
        foreach ($order->get_items() as $order_item) {

            $product = wc_get_product($order_item->get_product_id());

            if ($product->get_type() == 'variable') {
                $variation_id = $order_item->get_variation_id();

                $variation = new WC_Product_Variation($variation_id);
                if ($variation->needs_shipping() && !$variation->is_virtual() && !$variation->is_downloadable()) {
                    return true;

                }
            } else {
                if ($product->needs_shipping() && !$product->is_virtual() && !$product->is_downloadable()) {
                    return true;
                }
            }

        }

        return false;
    }
}
if (!function_exists('gpls_woo_rfq_get_rfq_cart_quantity')) {
    function gpls_woo_rfq_get_rfq_cart_quantity()
    {
        $wp_session = gpls_woo_get_session();

        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');
        $total_quantity = 0;
        if (is_array($gpls_woo_rfq_cart)) {
            foreach ($gpls_woo_rfq_cart as $cart_item_key => $cart_item) {
                $total_quantity = $total_quantity + $cart_item['quantity'];
            }
        }


        return $total_quantity;
    }
}

if (!function_exists('pls_woo_rfq_get_link_to_rfq')) {

    function pls_woo_rfq_get_link_to_rfq()
    {

        $home = home_url() . '/quote-request/';

        $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);

        if (is_ssl()) {

            $rfq_page = preg_replace("/^http:/i", "https:", $rfq_page);

        }

        return $rfq_page;


    }
}

if (!function_exists('pls_woo_rfq_get_link_to_favs')) {

    function pls_woo_rfq_get_link_to_favs()
    {

        $home = home_url() . '/favorites/';

        $fav_page = get_option('rfq_cart_sc_section_show_link_to_favorites_page', $home);

        if (is_ssl()) {
            $fav_page = preg_replace("/^http:/i", "https:", $fav_page);
        }

        return $fav_page;
    }
}


if (!function_exists('gpls_woo_rfq_show_order_status_for_reports')) {
    function gpls_woo_rfq_show_order_status_for_reports($order_statuses)
    {
        unset($order_statuses['wc-gplsquote-req']);
        unset($order_statuses['gplsquote-req']);
        unset($order_statuses['wc-gplsquote-sent']);
        unset($order_statuses['gplsquote-sent']);

        return $order_statuses;

    }
}


if (!function_exists('init_gpls_rfq_payment_gateway')) {
    function init_gpls_rfq_payment_gateway()
    {
        require(gpls_woo_rfq_DIR . 'includes/classes/gateway/wc-gateway-gpls-request-quote.php');


    }
}

if (!function_exists('add_gpls_woo_rfq_class')) {
//normal to rfq checkout
    function add_gpls_woo_rfq_class($methods)
    {

        $methods[] = 'WC_Gateway_GPLS_Request_Quote';

        return $methods;
    }
}
if (!function_exists('gpls_rfq_remove_other_payment_gateways')) {
    function gpls_rfq_remove_other_payment_gateways($available_gateways)
    {

        if (is_admin()) {
            return $available_gateways;
        }

//  WordPress.Security.NonceVerification.Recommended
        //WooCommerce payment page no nonce
        // phpcs:disable
        if (isset($_GET['pay_for_order'])) {
            unset($available_gateways['gpls-rfq']);
            return $available_gateways;
        }
// phpcs:enable

        $can_ask_quote = false;

        foreach ($available_gateways as $gateway_id => $gateway) {

            if ($gateway_id != 'gpls-rfq') {
                unset($available_gateways[$gateway_id]);
            } else {
                $can_ask_quote = true;
            }
        }

        if ($can_ask_quote && !WC()->session) {
            WC()->initialize_session();
        }

        if ($can_ask_quote && WC()->session != null) {
            WC()->session->set('chosen_payment_method', 'gpls-rfq');
        }

        return $available_gateways;
    }
}


if (!function_exists('gpls_rfq_remove_other_block_payment_gateways')) {
    function gpls_rfq_remove_other_block_payment_gateways($available_gateways)
    {


        if (is_admin()) {
            return $available_gateways;
        }
//  WordPress.Security.NonceVerification.Recommended
        //WooCommerce payment page no nonce
        // phpcs:disable
        if (isset($_GET['pay_for_order'])) {


            foreach ($available_gateways as $gateway_id => $gateway) {

                if ($gateway == 'WC_Gateway_GPLS_Request_Quote') {
                    unset($available_gateways[$gateway_id]);
                }
            }

            return $available_gateways;
        }


        $can_ask_quote = false;

        foreach ($available_gateways as $gateway_id => $gateway) {

            if ($gateway != 'WC_Gateway_GPLS_Request_Quote') {
                unset($available_gateways[$gateway_id]);
            } else {
                $can_ask_quote = true;
            }
        }


        if ($can_ask_quote && !WC()->session) {
            WC()->initialize_session();
        }

        if ($can_ask_quote && WC()->session != null) {
            WC()->session->set('chosen_payment_method', 'gpls-rfq');
        }

        return $available_gateways;
        // phpcs:enable
    }
}


if (!function_exists('gpls_woo_rfq_footer_admin')) {
    function gpls_woo_rfq_footer_admin($default)
    {

//  WordPress.Security.NonceVerification.Recommended
        //WooCommerce setting page no nonce
        // phpcs:disable
        if (is_admin() && isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'settings_gpls_woo_rfq') {
            ob_start();
            ?>

            <div style="clear:both"></div>
            <div style="position: absolute;left:0">
                <table>
                    <tr valign="top">

                        <td class="forminp">
                            <table style="background:white;">


                                <tr>
                                    <td>
                                        <div class="plus_options" style=" ">

                                            <ul class="plus_options_ul">

                                                <li class="plus_options_li" style="margin-top: 15px;">
                                                    <div>
                                                        <span class="plus_options-header"> <?php esc_html(__('Available in Premium Version:', 'woo-rfq-for-woocommerce')); ?></span>
                                                    </div>
                                                </li>

                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Buy or request a quote at woocommerce checkout', 'woo-rfq-for-woocommerce') ?></strong>:
                                                        <?php esc_html_e('Allow the choice to purchase or request a quote at WooCommerce checkout.', 'woo-rfq-for-woocommerce') ?>

                                                    </div>
                                                </li>

                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Buy or request a quote based on items in the cart', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('If the cart contains a "quote item", then customer can only request a quote.', 'woo-rfq-for-woocommerce') ?>

                                                    </div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Enable role based price visibility and checkout options at WooCommerce checkout:', 'woo-rfq-for-woocommerce') ?></strong>

                                                    </div>
                                                </li>


                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Bulk action for stores with large number of products:', 'woo-rfq-for-woocommerce') ?></strong>

                                                        <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php esc_html_e('Bulk enable/disable quote items, "Hide Add to Cart", "Hide Price"  by category.', 'woo-rfq-for-woocommerce'); ?>

                                                    </div>
                                                </li>


                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Use google recaptcha for quote request:', 'woo-rfq-for-woocommerce') ?></strong>

                                                    </div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Allow visitors to pay for an order without having to log on first (guest pay).', 'woo-rfq-for-woocommerce') ?></strong>

                                                    </div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Enable price visibility by IP address.', 'woo-rfq-for-woocommerce') ?></strong>


                                                    </div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('And more!', 'woo-rfq-for-woocommerce') ?></strong>

                                                    </div>
                                                </li>


                                            </ul>
                                            <ul class="plus_options_ul">
                                                <li class="plus_options_li plus_large">
                                                    <div style="margin-bottom:20px"><span> <a target="_blank"
                                                                                              class="get_plus"
                                                                                              href="https://neahplugins.com/product/woocommerce-quote-request-plus/"><?php esc_html_e('Get Quote Request Plus!', 'woo-rfq-for-woocommerce'); ?></a></span>
                                                    </div>
                                                </li>
                                                <li class="plus_options_li plus_small">
                                                    <div style="margin-bottom:20px"><span> <a target="_blank"
                                                                                              class="get_plus"
                                                                                              href="https://neahplugins.com/product/woocommerce-quote-request-plus/"><?php esc_html_e('Get Premium!', 'woo-rfq-for-woocommerce'); ?></a></span>
                                                    </div>
                                                </li>

                                                <li>
                                                    <div>&nbsp;</div>
                                                </li>
                                                <li>
                                                    <div>&nbsp;</div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="clear_narrow">&nbsp;</div>
                                        <div class="plus_narrow">
                                            <ul class="plus_options_ul">
                                                <li class="plus_options_li" style="margin-top: 15px;">
                                                    <div>
                                                        <span class="plus_options-header"> <?php esc_html_e('Other Quote Request Plugins:', 'woo-rfq-for-woocommerce'); ?></span>
                                                    </div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('PDF Plugin for Quote Request:', 'woo-rfq-for-woocommerce') ?></strong><?php esc_html_e('Send PDF attachments of the quote email to the customer.', 'woo-rfq-for-woocommerce') ?>

                                                    </div>
                                                </li>
                                                <li>
                                                    <div>&nbsp;</div>
                                                </li>
                                                <li>
                                                    <div>&nbsp;</div>
                                                </li>
                                                <li class="plus_options_li">
                                                    <div>
                                                        <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('File Upload Plugin for Quote Request:', 'woo-rfq-for-woocommerce') ?></strong><?php esc_html_e('Allow customers to upload files along with their quote request.', 'woo-rfq-for-woocommerce') ?>

                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>

                            </table>


                        </td>
                    </tr>
                </table>
                <p>
            </div>

            <?php
            $footer = ob_get_clean();
            return $footer;
        }
        return $default;
    }
    // phpcs:enable
}

if (!function_exists('gpls_get_rfq_cart_quantities')) {

    function gpls_get_rfq_cart_quantities()
    {
        $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');
        $quantities = array();

        foreach ($gpls_woo_rfq_cart as $cart_item_key => $cart_item) {


            if ($cart_item['data'] == null) continue;

            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
            $quantities[$product_id] = $cart_item['quantity'];

        }
        return $quantities;
    }
}
if (!function_exists('gpls_get_rfq_cart_product_quantity')) {
    function gpls_get_rfq_cart_product_quantity($product_id)
    {
        $quants = gpls_get_rfq_cart_quantities();

        if (!empty($quants) && is_array($quants) && isset($quants[$product_id])) {
            return $quants[$product_id];
        }

        return false;
    }
}


if (!function_exists('gpls_woo_rfq_main_after_setup_theme')) {
    function gpls_woo_rfq_main_after_setup_theme()
    {

        $reply_to_admin = get_option('settings_gpls_woo_rfq_admin_email_reply_to', 'no');

        if ($reply_to_admin == "yes") {
            add_filter('woocommerce_email_headers', 'gpls_rfq_add_reply_to_admin_order', PHP_INT_MAX - 1, 4);
        }

        function gpls_rfq_add_reply_to_admin_order($header, $id, $order, $email)
        {


            if ($id == 'new_rfq' && $order) {

                $reply_to_email = $order->get_billing_email();

                if ($order && $order->get_billing_email() && ($order->get_billing_first_name() || $order->get_billing_last_name())) {
                    $header .= 'Reply-to: ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . ' <' . $order->get_billing_email() . ">\r\n";
                }

            }


            return $header;
        }

        function gplswoo_handle_no_payment()
        {

            add_action('woocommerce_payment_complete_order_status', 'gplswoo_changing_order_status_before_payment', 10, 3);

            function gplswoo_changing_order_status_before_payment($status, $order_id, $order)
            {


                if (!$order) return;
                //  
                $no_payment = __('No payment', 'woo-rfq-for-woocommerce');
                $no_payment = get_option('settings_gpls_woo_rfq_no_payment_checkout_text', $no_payment);


                $order->add_order_note($no_payment, 0, 1);
                $order->update_status('pending');
                // $order->save();

                $email_new_order = WC()->mailer()->get_emails()['WC_Email_New_Order'];

                if (class_exists('WC_Email_New_Order')) {
                    $email_new_order->object = $order;
                    $gplswoo_subject = $email_new_order->get_subject() . $order->get_order_number() . ' ' . $no_payment;
                    global $gplswoo_heading;
                    $gplswoo_heading = $email_new_order->get_subject() . $order->get_order_number();

                    $email_new_order->heading = $gplswoo_subject;


                    if (!function_exists('gplswoo_heading')) {
                        function gplswoo_heading($heading, $order, $email)
                        {
                            global $gplswoo_heading;
                            return $gplswoo_heading;
                        }
                    }

                    add_filter('woocommerce_email_heading_new_order', 'gplswoo_heading', 100, 3);
                    //apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'heading', $this->get_default_heading() ) ), $this->object, $this );

                    $email_new_order->send($email_new_order->get_recipient(), $gplswoo_subject,
                        $email_new_order->get_content(), $email_new_order->get_headers(), $email_new_order->get_attachments());
                    // $email_new_order->trigger($order_id);

                }

            }
        }


        $needs_payment = get_option('settings_gpls_woo_rfq_no_payment_checkout', 'no');

        if ($needs_payment == "yes") {
            add_action('init', 'gplswoo_handle_no_payment', 100);
        }

        if (get_option('settings_gpls_woo_rfq_show_cart_thank_you_page') == "yes") {

            add_action('woocommerce_thankyou', 'gpls_woo_rfq_woocommerce_thankyou', 1000, 1);

        }

        add_filter('woocommerce_reports_order_statuses', 'gpls_woo_rfq_show_order_status_for_reports', 100, 1);

        if (function_exists('is_user_logged_in')) {

            if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {

                $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
                $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";
                $GLOBALS["hide_for_visitor"] = "yes";

            } else {
                $GLOBALS["hide_for_visitor"] = "no";
            }
        } else {
            $GLOBALS["hide_for_visitor"] = "no";
        }

        if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == 'rfq') {
            add_filter('woocommerce_payment_gateways', 'add_gpls_woo_rfq_class', 1, 1);

            add_action('init', 'init_gpls_rfq_payment_gateway');

            add_filter('woocommerce_available_payment_gateways', 'gpls_rfq_remove_other_payment_gateways', 1000, 1);
            // add_filter('woocommerce_payment_gateways', 'gpls_rfq_remove_other_block_payment_gateways',1000,1);

        }


        add_action("woocommerce_add_to_cart", "gpls_woo_rfq_woocommerce_add_to_cart", 1000, 6);


        // add_action('woocommerce_order_status_changed', function ($order_id, $status_from, $status_to) {


        add_action('woocommerce_order_status_changed', 'gpls_woo_rfq_status_changed_gpls_new_order_email_sent', 100, 3);

        if (!function_exists('gpls_woo_rfq_status_changed_gpls_new_order_email_sent')) {
            function gpls_woo_rfq_status_changed_gpls_new_order_email_sent($order_id, $status_from, $status_to)
            {

                $order = wc_get_order($order_id);

                // if (empty($order->get_meta('_is_admin')))
                {

                    $email_already_sent = $order->get_meta('_gpls_new_order_email_sent');


                    if ('true' === $email_already_sent && !apply_filters('woocommerce_new_order_email_allows_resend', false)) {
                        return;
                    }

                    $current_order_status = 'wc-' . $status_to;


                    if (in_array($current_order_status, array('wc-processing')) &&
                        in_array($status_from, array('gplsquote-sent', 'gplsquote-req'))) {

                        $np_email = WC()->mailer()->emails['WC_Email_Customer_Processing_Order'];
                        if ($np_email) {

                            $np_email->trigger($order_id);
                            update_post_meta($order_id, '_gpls_new_order_email_sent', 'yes');
                            //     remove_filter('woocommerce_new_order_email_allows_resend', '__return_true' );

                        }

                    }
                }

            }
        }


        add_action('woocommerce_order_status_changed', 'gpls_woo_rfq_status_transition', 100, 4);


        if (get_option('woocommerce_cart_redirect_after_add') == "yes" &&
            !in_array('rfqtk/rfqtk.php', apply_filters('active_plugins', get_option('active_plugins')))) {

            add_filter('woocommerce_add_to_cart_redirect', 'woocommerce_add_to_cart_redirect_func', 3, 2);

            if (!function_exists('woocommerce_add_to_cart_redirect_func')) {
                function woocommerce_add_to_cart_redirect_func($url, $adding_to)
                {
                    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') != "normal_checkout") {
                        return $url;
                    }


                    $quote = gpls_woo_get_rfq_enable($adding_to);

                    if ($quote == "yes" && !empty($adding_to)) {

                        $redirect = false;

                        if (!empty($_SERVER['QUERY_STRING']) && !empty($_SERVER['REQUEST_URI'])) {
                            $redirect = str_replace(sanitize_url(wp_unslash($_SERVER['QUERY_STRING'])),
                                '', sanitize_url(wp_unslash($_SERVER['REQUEST_URI'])));

                        } else {
                            if (!empty($_SERVER['REQUEST_URI'])) {
                                $redirect = sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));
                            }
                        }


                        $url = $redirect;


                    }

                    return $url;

                }
            }

        }


        if (function_exists('wc_get_cart_url')) {


            if (!function_exists('gpls_woo_rfq_is_cart_page')) {
                function gpls_woo_rfq_is_cart_page()
                {

                    // global $wp;
                    //  global $wp_query;
                    $result = false;
                    if (!empty($_SERVER['REQUEST_URI'])) {

                        $result = str_contains(wp_unslash($_SERVER['REQUEST_URI']), str_replace(home_url(), '', wc_get_cart_url()));
                    }

                    return $result;
                }
            }
        }
        if (function_exists('wc_get_account_endpoint_url')) {


            if (!function_exists('gpls_woo_rfq_is_account_page')) {
                function gpls_woo_rfq_is_account_page()
                {

                    // global $wp;
                    //  global $wp_query;
                    $result = false;
                    if (!empty($_SERVER['REQUEST_URI'])) {

                        $result = str_contains(wp_unslash($_SERVER['REQUEST_URI']), str_replace(home_url(), '', get_permalink(get_option('woocommerce_myaccount_page_id'))));

                    }


                    return $result;
                }
            }
        }

        if (function_exists('wc_get_checkout_url')) {

            if (!function_exists('gpls_woo_rfq_is_checkout_page')) {
                function gpls_woo_rfq_is_checkout_page()
                {

                    // global $wp;
                    //  global $wp_query;
                    $result = false;
                    if (!empty($_SERVER['REQUEST_URI'])) {

                        $result = str_contains((wp_unslash($_SERVER['REQUEST_URI'])), str_replace(home_url(), '', wc_get_checkout_url()));

                    }
                    return $result;
                }
            }

        }


        require_once(gpls_woo_rfq_DIR . 'includes/classes/prices/gpls_woo_rfq_prices.php');
        $GLOBALS["gpls_woo_rfq_prices"] = new gpls_woo_rfq_prices();


    }

}

if (!function_exists('gpls_woo_rfq_status_transition')) {

    function gpls_woo_rfq_status_transition($order_id, $from, $to, $order)
    {

        if ($to !== 'wc-gplsquote-req' && $to !== 'gplsquote-req') {


            if (function_exists('gpls_woo_rfq_remove_filters')) {
                gpls_woo_rfq_remove_filters();

            }
            if (function_exists('gpls_woo_rfq_remove_filters_normal_checkout')) {

                gpls_woo_rfq_remove_filters_normal_checkout();
            }
            if (function_exists('ip_based_options')) {

                ip_based_options();
            }
        }
    }

}


add_action('after_setup_theme', 'gpls_woo_rfq_main_after_setup_theme', 100);


if (!function_exists('gpls_woo_rfq_empty_price')) {
    function gpls_woo_rfq_empty_price($return, $price, $args, $unformatted_price, $original_price)
    {

        if (is_admin()) return $return;

        if (gpls_woo_rfq_is_checkout_page() && !empty(is_wc_endpoint_url('order-received'))) {
            if (trim($original_price) == "" || trim($original_price) == "0") {
                return 0;
            }
        } else {
            if (trim($original_price) == "") {
                return 0;
            }
        }
        return $return;
    }
}


if (!function_exists('gpls_woo_rfq_main_after_loaded')) {
    function gpls_woo_rfq_main_after_loaded()
    {
        // add_filter( 'wc_price', 'gpls_woo_rfq_empty_price',10000,5);

        // add_filter('admin_footer_text', 'gpls_woo_rfq_footer_admin');
        // phpcs:disable
        if (is_admin()
            //  WordPress.Security.NonceVerification.Recommended
            && isset($_REQUEST['tab'])
            //  WordPress.Security.NonceVerification.Recommended
            && $_REQUEST['tab'] == 'settings_gpls_woo_rfq'
            //  WordPress.Security.NonceVerification.Recommended
            && isset($_REQUEST['section']) && $_REQUEST['section'] == 'npoptions'
        ) {
            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/rfq_admin_misc.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/rfq_admin_misc.js';
            wp_enqueue_script('rfq_admin_misc', $url_js, array('jquery'), wp_rand(10, 100000), true);

        }
        if (is_admin()
            //  WordPress.Security.NonceVerification.Recommended
            //WooCommerce setting page no nonce

            && isset($_REQUEST['tab'])
            //  WordPress.Security.NonceVerification.Recommended
            && $_REQUEST['tab'] == 'settings_gpls_woo_rfq') {
            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/rfq_admin_basic.js';
            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/rfq_admin_basic.js';
            wp_enqueue_script('rfq_admin_basic', $url_js, array('jquery'), wp_rand(10, 100000), true);
            // phpcs:enable
        }

    }


    /*
     add_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) .
      '</span>', $cart_item, $cart_item_key);
 */


    function gpls_woo_rfq_woocommerce_widget_hide($product_id): bool
    {

        $hide = false;

        if (function_exists('is_user_logged_in')) {

            if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {
                $hide = true;

            }
        }

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
            $hide = false;

        }

        if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_plus_get_hide_price')) {
            $product = wc_get_product($product_id);
            if (gpls_woo_plus_get_hide_price($product) == "yes") {
                $hide = true;

            }
        }

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {

            if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no') {
                $hide = true;

            }

            if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'yes') {

                $product = wc_get_product($product_id);
                $rfq_enabled = gpls_woo_get_rfq_enable($product);

                if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_plus_is_rfq5c_hidden')) {

                    if (gpls_woo_plus_is_rfq5c_hidden() == true) {

                        if ($rfq_enabled == 'yes') {
                            $hide = true;

                        }

                    }
                }
            } else {
                $hide = true;

            }

        }

        return $hide;
    }


    function gpls_woo_rfq_woocommerce_widget_cart($html, $cart_item, $cart_item_key)
    {

        $hide = gpls_woo_rfq_woocommerce_widget_hide($cart_item['product_id']);


        if ($hide == 1) {
            //   return '<span class="quantity">' . $cart_item['quantity'] . '  </span>';
        }

        return $html;
    }

    function gpls_woo_rfq_woocommerce_widget_cart_init()
    {

        add_filter('woocommerce_widget_cart_item_quantity', 'gpls_woo_rfq_woocommerce_widget_cart', 100, 3);

    }

    add_action('init', 'gpls_woo_rfq_woocommerce_widget_cart_init');

}

add_action('wp_loaded', 'gpls_woo_rfq_main_after_loaded', 100);


/*function gpls_woo_rfq_favs_endpointx(){


    if (!is_admin()&&gpls_woo_rfq_is_account_page() && get_option('settings_gpls_woo_rfq_allow_favorites','no')=='yes')
    {

       add_filter('woocommerce_account_menu_items', 'add_my_menu_items');

        function gpls_woo_favs(){
            $favorites = get_option('settings_gpls_woo_rfq_my_acct_favorites_label', __('Favorites', 'woo-rfq-for-woocommerce'));
            $favorites = __($favorites, 'woo-rfq-for-woocommerce');
            return $favorites;
        }

        function add_my_menu_items($items)
        {

            $favorites = gpls_woo_favs();


            $my_items = array(strtolower($favorites)  => __($favorites, 'woo-rfq-for-woocommerce'),);
            $my_items = array_slice($items, 0, count($items) - 1, true) +
                $my_items +
                array_slice($items, 1, count($items), true);
            return $my_items;
        }



        $favorites = gpls_woo_favs();
        $endpoint = strtolower($favorites);

        add_action('init', 'gpls_woo_rfq_fav_endpoint');

        function gpls_woo_rfq_fav_endpoint()
        {
            add_rewrite_endpoint('favorites', EP_ROOT | EP_PAGES);
        }

        add_action('woocommerce_account_' . $endpoint . '_endpoint', 'gpls_woo_rfq_favs_endpoint_content');

        function gpls_woo_rfq_favs_endpoint_content()
        {



            wc_get_template('woo-rfq/fav-cart.php',
                array('list_class' => ''),
                '', gpls_woo_rfq_WOO_PATH);

        }

       add_filter('query_vars', 'gpls_woo_rfq_favs_query_vars', 10000);

        function gpls_woo_rfq_favs_query_vars($vars)
        {


            $favorites = gpls_woo_favs();
            $vars[] = $favorites;
            return $vars;
        }

        add_action('wp_loaded', 'my_custom_flush_rewrite_rules');

        function my_custom_flush_rewrite_rules()
        {
            flush_rewrite_rules();
        }
    }
}


add_action('after_setup_theme', 'gpls_woo_rfq_favs_endpointx', 100);
*/

$needs_payment = get_option('settings_gpls_woo_rfq_no_payment_checkout', 'no');

if ($needs_payment == "yes") {
    add_action('init', 'gplswoo_handle_no_payment', 100);
}


function gplswoo_get_submit_order_label()
{

//  


    if (!defined('gpls_woo_rfq_DIR')) {
        DEFINE('gpls_woo_rfq_DIR', plugin_dir_path(__FILE__));
        DEFINE('gpls_woo_rfq_URL', plugin_dir_url(__FILE__));
        DEFINE('gpls_woo_rfq_FILE_NAME', (__FILE__));
        DEFINE('gpls_woo_rfq_PLUGIN_PATH', untrailingslashit(plugin_basename(__FILE__)));
        DEFINE('gpls_woo_rfq_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/');
        DEFINE('gpls_woo_rfq_WOO_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/');
        DEFINE('gpls_woo_rfq_GLOBAL_NINJA_FORMID', get_option('settings_gpls_woo_ninja_form_option'));


        /*  if (!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
              $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
              $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option, 'woo-rfq-for-woocommerce');
              DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
          }*/

        $small_src = gpls_woo_rfq_URL . '/gpls_assets/img/favorite_small.png';
        $large_src = gpls_woo_rfq_URL . '/gpls_assets/img/favorite_large.png';
        $image_fav_small = '<image title="Add to your favorites" class="image_favgpls16" style="max-width:16px !important"  src="' . $small_src . '" />';
        $image_fav_large = '<image title="Add to your favorites" class="image_favgpls24" style="max-width:24px !important"  src="' . $large_src . '" />';

        DEFINE('gpls_woo_rfq_fav_image16', $image_fav_small);
        DEFINE('gpls_woo_rfq_fav_image24', $image_fav_large);
    }

    $GLOBALS["gpls_woo_rfq_checkout_option"] = get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout');

    if (trim($GLOBALS["gpls_woo_rfq_checkout_option"]) == '') {
        $GLOBALS["gpls_woo_rfq_checkout_option"] = 'normal_checkout';
        update_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout', true);
    }


    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
        if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') {

            $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";
        }
    }


    $run_option_old_shipping_coupon = get_option('settings_gpls_woo_rfq_old_shipping_coupon', false);

    if (!$run_option_old_shipping_coupon) {

        update_option('settings_gpls_woo_rfq_show_shipping', 'yes', true);
        update_option('settings_gpls_woo_rfq_allow_coupon_entry', 'yes', 'true');
        update_option('settings_gpls_woo_rfq_show_applied_coupons', 'yes', 'true');
        update_option('settings_gpls_woo_rfq_old_shipping_coupon', true);
        update_option('settings_gpls_woo_rfq_old_shipping_coupon', true);
    }

    $GLOBALS["gpls_woo_rfq_show_prices"] = "no";

    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
        && get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'yes'
    ) {
        $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
    }

    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
        && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'

    ) {
        $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
    }

    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
        && get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no'
    ) {

        $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
    }

    if (function_exists('is_user_logged_in')) {

        if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {

            $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
            $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";
            $GLOBALS["hide_for_visitor"] = "yes";

        } else {
            $GLOBALS["hide_for_visitor"] = "no";
        }
    } else {
        $GLOBALS["hide_for_visitor"] = "no";
    }

    do_action('gpls_rfq_setup_constants_action');

    $ajax_array = array();
    $ajax_array['rfq_checkout_mode'] = $GLOBALS["gpls_woo_rfq_checkout_option"];

    if ($GLOBALS["gpls_woo_rfq_checkout_option"] == 'rfq') {


        $order_button_text = get_option('rfq_cart_wordings_submit_your_rfq_text', __('Submit Your Request For Quote', 'woo-rfq-for-woocommerce'));
        $order_button_text = __($order_button_text, 'woo-rfq-for-woocommerce');

        $order_button_text = apply_filters('gpls_woo_rfq_rfq_submit_your_order_text', $order_button_text);

        $ajax_array['rfq_cart_wordings_submit_your_rfq_text'] = $order_button_text;

        //  require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
        // require_once(ABSPATH . 'wp-includes/class-phpass.php');


        $proceed_to_rfq = get_option('rfq_cart_wordings_proceed_to_rfq', __('Proceed To Submit Your RFQ', 'woo-rfq-for-woocommerce'));
        $proceed_to_rfq = __($proceed_to_rfq, 'woo-rfq-for-woocommerce');
        $proceed_to_rfq = apply_filters('gpls_woo_rfq_proceed_to_rfq', $proceed_to_rfq);
        $ajax_array['rfq_cart_wordings_proceed_to_rfq'] = $proceed_to_rfq;

        if (class_exists('GPLS_WOO_RFQ_PLUS')) {
            $plus_data = gpls_woo_rfq_plus_get_plus_data();

            //$plus_data['bid_form_array']
            $ajax_array['rfq_cart_bid'] = $plus_data['bid_form_array']['rfq_cart_bid'];
            $ajax_array['allow_bid'] = $plus_data['bid_form_array']['allow_bid'];
            $ajax_array['require_bid'] = $plus_data['bid_form_array']['require_bid'];

        }

        $ajax_array['cart_url'] = wc_get_cart_url();
        $ajax_array['checkout_url'] = wc_get_checkout_url();


        echo wp_json_encode($ajax_array);
    }

    wp_die();

}

add_action('wp_ajax_gplswoo_get_submit_order_label', 'gplswoo_get_submit_order_label');
add_action('wp_ajax_nopriv_gplswoo_get_submit_order_label', 'gplswoo_get_submit_order_label');




function np_add_fav_rec($user_id, $product_id,$user_first,$user_last
    ,$user_email,$product_url,$product_image,$misc_value,$misc_value2,
                        $misc_value3)
{


    global $wpdb;

    //custom table no wrappers or caching avaialable or needed
    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare("INSERT INTO 
        {$wpdb->base_prefix}npxyzfavs (`user_id`, `product_id`,`user_first`,`user_last`
        ,`user_email`,`product_url`,`product_image`,`misc_value`,`misc_value2`,
        `misc_value3`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)
         ON DUPLICATE KEY UPDATE 
         `user_first` = VALUES(`user_first`), 
         `user_last` = VALUES(`user_last`),
         `user_email` = VALUES(`user_email`),
        `product_url`= VALUES(`product_url`),
         `product_image`= VALUES(`product_image`),
         `misc_value`= VALUES(`misc_value`),
         `misc_value2`= VALUES(`misc_value2`),
         `misc_value3`= VALUES(`misc_value3`)",
        $user_id, $product_id,$user_first,$user_last
        ,$user_email,$product_url,$product_image,
        $misc_value,$misc_value2,$misc_value3)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery


    if (!$result) {
        return false;
    }
}

//fav_id	bigint(20) unsigned Auto Increment
//user_id	varchar(191)
//user_first	longtext NULL
//user_last	longtext NULL
//product_id	bigint(20) NULL
//user_email	bigint(20)
//`product_url` varchar(500) DEFAULT NULL,
//  `product_image` varchar(500) DEFAULT NULL,
 // `misc_value` varchar(1000) DEFAULT NULL,
 // `misc_value2` varchar(1000) DEFAULT NULL,
 // `misc_value3` varchar(1000) DEFAULT NULL,
//created	timestamp [current_timestamp()]
//updated	timestamp [0000-00-00 00:00:00]

function np_get_fav_rec($user_id)
{
    global $wpdb;

    if (empty($user_id)) {
        return false;
    }

    $values=false;

    //custom table no wrappers or caching avaialable or needed
    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $faves_result = $wpdb->get_results("SELECT *
        FROM {$wpdb->base_prefix}npxyzfavs 
        WHERE user_id = {$user_id}",ARRAY_A); //db call ok



    if (!empty($faves_result)) {
        $values = $faves_result;
    }

    return $values;

}

function np_delete_fav_rec_by_uid($user_id)
{
    global $wpdb;

    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare("delete FROM 
    {$wpdb->base_prefix}npxyzfavs
     where `user_id` = %s ",$user_id)); //db call ok

    // $result = $wpdb->query($sql);

    return $result;

}
function np_delete_fav_rec_by_fav_id($fav_id)
{
    global $wpdb;

    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyzfavs
     where `fav_id` = %s ",$fav_id)); //db call ok

    // $result = $wpdb->query($sql);

    return $result;

}

function np_delete_fav_rec_by_user_id_product_id($user_id,$product_id)
{
    global $wpdb;

    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare("delete
     FROM {$wpdb->base_prefix}npxyzfavs 
     where `user_id` = %s and `product_id` = %s ",$user_id,$product_id)); //db call ok



    return $result;

}

function np_delete_fav_rec_by_product_id($product_id)
{
    global $wpdb;

    //db call ok; no-cache ok
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $result = $wpdb->query($wpdb->prepare("delete
     FROM {$wpdb->base_prefix}npxyzfavs where  `product_id` = %s ",$product_id)); //db call ok

    // $result = $wpdb->query($sql);

    return $result;

}


function gplswoo_ajax_add_to_favs()
{

    $data = $_POST['data'];

    $parsed_url = parse_url($data);

    $query = $parsed_url['path'];


    if (!empty($query)) {
        parse_str($query, $query_params);

        $product_id = $query_params['product_id'];

        $_wpnonce = $query_params['_wpnonce'];

    }

    require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
    require_once(ABSPATH . 'wp-includes/class-phpass.php');

    if (!is_user_logged_in()) {

        $gpls_woo_fav_array = gpls_woo_rfq_get_item('gpls_woo_fav_array');
    }

    if (!$gpls_woo_fav_array) {

        $gpls_woo_fav_array = array();
    }

    $new_arr = array('date_added'=>date("Y-m-d H:i:s"));
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
      //  $product_image=$product->get_image();
        $product_url=$product->get_permalink();


        np_add_fav_rec($user->ID, $product_id,$first,
            $last,$user->user_email,
            $product_url,$product_image,null,null,
            null);
    }else{

        gpls_woo_rfq_cart_set('gpls_woo_fav_array', $gpls_woo_fav_array);
    }

    wp_send_json_success('true');

    wp_die();
}


add_action('wp_ajax_add_to_favs_action', 'gplswoo_ajax_add_to_favs');
add_action('wp_ajax_nopriv_add_to_favs_action', 'gplswoo_ajax_add_to_favs');

function gplswoo_add_user_login_action( $user_login, $user ) {


    $gpls_woo_fav_array = gpls_woo_rfq_get_item('gpls_woo_fav_array');

    if(empty($gpls_woo_fav_array))return;

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

    foreach ($gpls_woo_fav_array as $product_id => $cart_item) {

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
    }

    gpls_woo_rfq_cart_set('gpls_woo_fav_array',array());

    $gpls_woo_fav_array = gpls_woo_rfq_get_item('gpls_woo_fav_array');


}
add_action( 'wp_login', 'gplswoo_add_user_login_action', 100, 2 );








