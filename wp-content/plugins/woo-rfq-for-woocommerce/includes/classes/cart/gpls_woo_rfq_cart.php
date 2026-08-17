<?php

/**
 * Main class
 *
 */
//   WordPress.WP.I18n.NoEmptyStrings
//   WordPress.WP.I18n.NonSingularStringLiteralText
if (!class_exists('gpls_woo_rfq_CART')) {

    class gpls_woo_rfq_CART
    {
        public function __construct()
        {

          /*  if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option,'woo-rfq-for-woocommerce');
                DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
            }*/

            $rfq_product_script = "<div class='gpls_script' style='display: none'> 
                <script>jQuery( document ).ready( function() {jQuery( '.tax-rate' ).hide();
                 jQuery( '.cart-subtotal' ).hide(); jQuery( '.order-total' ).hide();jQuery( '.tax-total' ).hide();} );
                 </script></div>";


            $url_new = get_option('rfq_cart_sc_section_show_link_to_rfq_page', '');


            $purchase_only = false;




            add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'woo_custom_cart_button_text'), 100, 2);
            add_filter('woocommerce_product_add_to_cart_text', array($this, 'woo_custom_cart_button_text'), 100, 2);
            add_filter('woocommerce_loop_add_to_cart_link', array($this, 'gpls_woo_rfq_add_to_cart_link_shop'), 1000, 2);

            $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
            $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

            if (($allow_favs=="yes" && is_user_logged_in()) ||
                (get_current_user_id() ==0 && $allow_favs_anon=='yes' && $allow_favs=="yes") )
            {
                add_filter('woocommerce_loop_add_to_cart_link', array($this, 'gpls_woo_rfq_add_to_cart_link_shop_favorites'), 2000, 2);
            }
            //  add_action('woocommerce_after_add_to_cart_button', array($this, 'gpls_woo_rfq_after_add_to_cart_button'), 1000);
            //  add_action('woocommerce_before_add_to_cart_button', array($this, 'gpls_woo_rfq_after_add_to_cart_button'), 10000);

            $hook_add_to_cart = get_option('settings_gpls_woo_rfq_normal_checkout_quote_single_position', 'woocommerce_before_add_to_cart_button');
            add_action($hook_add_to_cart, array($this, 'gpls_woo_rfq_after_add_to_cart_button'), 100);

            $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
            $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

            if (($allow_favs=="yes" && is_user_logged_in()) ||
                (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
                add_action('woocommerce_after_add_to_cart_form', array($this, 'gpls_woo_rfq_after_add_to_cart_favorites'), 200);
            }
            //   add_action('woocommerce_before_add_to_cart_button', array($this, 'gpls_woo_rfq_before_add_to_cart_button'), 1000);


            $hook = get_option('settings_gpls_woo_rfq_normal_checkout_quote_position', 'woocommerce_after_shop_loop_item');
            add_action($hook, array($this, 'gpls_woo_rfq_after_after_shop_loop_item'), 100);

            //  add_action('woocommerce_after_shop_loop_item', array($this, 'gpls_woo_rfq_after_after_shop_loop_item'), 100);

            add_filter('woocommerce_cart_item_remove_link', 'gpls_woo_rfq_cart_item_remove_link', 100, 2);
            add_action("woocommerce_after_cart", array($this, "gpls_woo_rfq_woocommerce_after_cart"), 1000);
            add_action("woocommerce_after_cart_totals", array($this, "gpls_woo_rfq_woocommerce_after_cart"), 1000);


            $is_checkout_cart_routine = false;
            $checkout_option = "normal_checkout";

            if(isset( $GLOBALS["gpls_woo_rfq_checkout_option"])){
                $checkout_option =  $GLOBALS["gpls_woo_rfq_checkout_option"];
            }

            if (isset($_POST['_wpnonce']) &&  wp_verify_nonce(sanitize_key(wp_unslash($_POST['_wpnonce'])),'rfq_id_nonce')
                && isset($_POST["rfq_product_id"])) {

            }

            if ($checkout_option === "normal_checkout" || isset($_POST["rfq_product_id"])) {

                $is_checkout_cart_routine = true;

            }

            if ($checkout_option === "rfq") {

                $is_checkout_cart_routine = false;

            }



            //  WordPress.Security.NonceVerification.Recommended
            $is_checkout_cart_routine = apply_filters('gpls_woo_rfq_normal_cart_routine_filter', $is_checkout_cart_routine, $checkout_option, $_REQUEST);

            if ($is_checkout_cart_routine == true) {

                $this->gpls_woo_rfq_normal_checkout_cart_routine();
            }


            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_ajax_add_to_quote_print_script'), 1000);
            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_ajax_add_to_rfq_single_page'), 100);


            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_ajax_add_to_fav_print_script'), 1000);


            if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "yes" ) {

                add_filter('woocommerce_product_is_in_stock', array($this, 'gpls_woo_rfq_filter_product_is_in_stock'), 1000, 2);
                add_filter('woocommerce_product_backorders_allowed', array($this, 'gpls_woo_rfq_filter_products_backorders_allowed'), 1000, 3);
                add_filter( 'woocommerce_get_availability_text', array($this,'gpls_get_out_of_stock_text'), 999, 2);
                add_filter( 'woocommerce_out_of_stock_message', array($this,'gpls_get_out_of_stock_variable_message'), 999);

                add_filter( 'woocommerce_available_variation',array($this,'gpls_woo_rfq_woocommerce_available_variation'),1000,3 );
                add_filter( 'woocommerce_hide_invisible_variations', array($this,'gpls_woo_rfq_woocommerce_hide_invisible_variations'),1000,3 );

            }
        }

        function gpls_woo_rfq_woocommerce_hide_invisible_variations($bool, $Product_id, $variation)
        {


            $product = wc_get_product($Product_id);

            $rfq_check = false;
            $normal_check = false;

            gpls_woo_rfq_get_mode($rfq_check, $normal_check);

            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $rfq_check = false;
                    $normal_check = true;
                }
            }

            $is_in_stock = false;

            if($rfq_check){
                $is_in_stock = true;
            }

            if($normal_check){

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                if( $rfq_enable == "yes" ){
                    $is_in_stock = true;
                }
            }
            if(  $is_in_stock == true) {
                if (!$variation->get_stock_quantity() ||$variation->get_stock_quantity()<=0 ||
                    $variation->get_stock_quantity() <= $variation->get_low_stock_amount())
                {
                    $data['is_purchasable'] = 1;
                    $data['is_in_stock'] = 1;
                    $data['variation_is_visible'] = 1;
                }
            }
        }


        function gpls_woo_rfq_woocommerce_available_variation($data, $product, $variation)
        {


            $rfq_check = false;
            $normal_check = false;

            gpls_woo_rfq_get_mode($rfq_check, $normal_check);

            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $rfq_check = false;
                    $normal_check = true;
                }
            }
            $is_in_stock = false;

            if($rfq_check){
                $is_in_stock = true;
            }

            if($normal_check){

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                if( $rfq_enable == "yes" ){
                    $is_in_stock = true;
                }
            }
            if(  $is_in_stock == true) {
                //if (!$variation->get_stock_quantity()) {

                if (!$variation->get_stock_quantity() ||$variation->get_stock_quantity()<=0 ||
                    $variation->get_stock_quantity() <= $variation->get_low_stock_amount())
                {
                    $data['is_purchasable'] = 1;
                    $data['is_in_stock'] = 1;
                    $data['variation_is_visible'] = 1;
                }
            }


            return $data;
        }

        function gpls_get_out_of_stock_text($text, $product)
        {



            $rfq_check = false;
            $normal_check = false;
            $is_in_stock = false;

            gpls_woo_rfq_get_mode($rfq_check, $normal_check);

            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $rfq_check = false;
                    $normal_check = true;
                }
            }


            if($rfq_check){
                $is_in_stock = true;
            }

            if($normal_check){

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                if( $rfq_enable == "yes" ){
                    $is_in_stock = true;
                }
            }


            if(  $is_in_stock == true)
            {


                if ((!$product->get_stock_quantity() || $product->get_stock_quantity() <= 0 ||
                        $product->get_stock_quantity() <= $product->get_low_stock_amount())
                    && $product->get_manage_stock()
                ) {
                    $text = get_option('rfq_cart_wordings_outofstock_text');
                    $text = __($text,'woo-rfq-for-woocommerce');

                    $text = '<p class="stock in-stock">' . $text . '</p>';
                }
            }


            return $text;
        }



        function gpls_get_out_of_stock_variable_message($text)
        {


            $text = get_option('rfq_cart_wordings_outofstock_text');
            $text = __($text,'woo-rfq-for-woocommerce');


            return $text;
        }

        function gpls_woo_rfq_filter_product_is_in_stock( $is_in_stock, $product ){


            $rfq_check = false;
            $normal_check = false;

            gpls_woo_rfq_get_mode($rfq_check, $normal_check);

            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $rfq_check = false;
                    $normal_check = true;
                }
            }


            if($rfq_check){
                $is_in_stock = true;
            }

            if($normal_check){

                $rfq_enable = gpls_woo_get_rfq_enable($product);

                if( $rfq_enable == "yes" ){
                    $is_in_stock = true;
                }
            }

            return $is_in_stock;
        }


        function gpls_woo_rfq_filter_products_backorders_allowed( $backorder_allowed, $product_id, $product ){


            $rfq_check = false;
            $normal_check = false;
            gpls_woo_rfq_get_mode($rfq_check, $normal_check);



            if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $rfq_check = false;
                    $normal_check = true;
                }
            }



            if($rfq_check){
                $backorder_allowed = true;
            }


            if($normal_check){

                $rfq_enable = gpls_woo_get_rfq_enable($product);
                if( $rfq_enable == "yes" ){
                    $backorder_allowed = true;
                }
            }


            return $backorder_allowed;
        }


        public function gpls_woo_rfq_ajax_add_to_rfq_single_page()
        {

            global $wp_query;
            if (isset($wp_query) && function_exists('is_product')) {

                if (!is_product()) {
                    return;
                }
            }

            $is_ajax = get_option('settings_gpls_woo_rfq_product_page_ajax', 'no');

            if ($is_ajax=="no") return;


            global $product;


            if (!is_object($product) && !function_exists('wc_get_product')) return;

            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }


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


            ?>


            <script type="application/javascript">

                jQuery(".gpls_rfq_set").click(function (e) {

                    jQuery(window).ajaxComplete(function (event, xhr, settings) {

                        var called = false;

                        if (settings.url === '/?wc-ajax=add_to_cart' && called === false) {


                            called = true;

                            <?php if ($normal_check == true && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') != 'yes'): ?>

                            <?php

                            if(get_transient('redirect_to_quote_request') == 'yes')
                            {
                            $url_new = get_option('rfq_cart_sc_section_show_link_to_rfq_page', '');

                            ?>
                            window.location.replace('<?php echo wp_kses_post($url_new) ?>');
                            return;
                            <?php
                            }
                            ?>

                            var gpls_rfq_set_div = jQuery(".gpls_rfq_set_div").find('.added_to_cart').hide();
                            gpls_rfq_set_div.find('.added_to_cart').attr('style', 'display: none !important');

                            <?php endif; ?>

                            var rfqcart_link = ".rfqcart-link";
                            jQuery(rfqcart_link).show();
                            jQuery("rfqcart_link").attr('style', 'display: block');


                            <?php if(get_transient('rfq_page_plus_Widget') == "yes"): ?>

                            jQuery.ajax({
                                async: true,
                                type: 'GET',
                                url: '/?rfq_widget2=0',
                                error: function (xhr, status, error) {

                                },
                                success:
                                    function (msg) {

                                        // jQuery('.plus_widget_wrapper').html(msg);
                                        // jQuery('#rfq_page_plus_widget-2').html(msg_back);
                                        jQuery('.rfq_page_plus_Widget').html(msg_back);


                                    }
                            });
                            <?php endif; ?>

                        }
                    });

                });

            </script>


            <?php
        }


        public function gpls_woo_rfq_ajax_add_to_quote_print_script()
        {


            $home = home_url() . '/quote-request/';

            $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);



            if(isset($_SERVER['REQUEST_URI'])) {
                $actual_link = get_site_url() . sanitize_url(wp_unslash($_SERVER['REQUEST_URI']));
            }



            $rfq_page_path= isset(wp_parse_url(trim($rfq_page))['path'])?(strtolower(wp_parse_url(trim($rfq_page))['path'])):'';
            $actual_link_path= isset(wp_parse_url(trim($actual_link))['path'])?(strtolower(wp_parse_url(trim($actual_link))['path'])):'';


            if ($rfq_page_path ===$actual_link_path) {

                return false;
            }

            if ('yes' !== get_option('woocommerce_enable_ajax_add_to_cart')) return false;

            if (is_product() || is_admin()) return false;



            do_action('gpls_woo_rfq_before_ajax_add_to_quote');

            $return = "no";

            $return = apply_filters('gpls_woo_rfq_skip_ajax_add_to_quote', $return);

            if ($return == "yes") {
                return true;
            }




            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
            ob_start();
            wc_get_template('woo-rfq/link-to-cart.php',
                array('link_to_rfq_page' => $link_to_rfq_page,
                ), '', gpls_woo_rfq_WOO_PATH);
            $result = ob_get_clean();
// phpcs:disable

            ?>
            <script type="application/javascript">

                function handle_long_str(str) {
                    var lines = str.split(/\n/);
                    var output = [];
                    var outputText = [];
                    for (var i = 0; i < lines.length; i++) {

                        if (/\S/.test(lines[i])) {
                            outputText.push('"' + $.trim(lines[i]) + '"');
                            output.push($.trim(lines[i]));
                        }
                    }
                    return outputText;
                }


                jQuery(window).on("load",function () {
                    var image_div;

                    jQuery(".woo_rfq_after_shop_loop_button").submit(function (e) {

                        var form = jQuery(this); //wrap this in jQuery
                        var is_var = jQuery(form).find('input[name="rfq_var"]').val();
                        const simple_types = ["simple","subscription"];
                        if (!simple_types.includes(is_var)) {
                            return true;
                        }

                        e.preventDefault();

                        var rfq_button_id = "#rfq_button_" + jQuery(form).data('rfq-product-id');
                        var rfq_qty_id = "#quantity_" + jQuery(form).data('rfq-product-id');//new


                        image_div = "#image_" + jQuery(form).data('rfq-product-id');

                        jQuery(image_div).show();
                        var str = jQuery(this).serialize();


                        jQuery.ajax({
                            type: "POST",
                            url: form.attr('action'),
                            data: str,
                            success: function (msg) {
                                <?php
                                $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

                                ?>
                                if (typeof msg.data !== 'undefined' && typeof msg.data.location !== 'undefined') {


                                    jQuery(image_div).hide();
                                    window.location.replace(msg.data.location);
                                    return;

                                } else {

                                    var note_id = "#note_" + jQuery(form).data('rfq-product-id');


                                    jQuery(note_id).html(<?php echo '"'.$result.'"'; ?>);

                                    jQuery(image_div).hide();
                                    jQuery(rfq_button_id).addClass('gpls_hidden');
                                    jQuery(rfq_qty_id).addClass('gpls_hidden');

<!--                                    -->
                                    <?php if(1): ?>

                                    jQuery.ajax({
                                        async: true,
                                        type: 'GET',
                                        url: '/?rfq_widget=0',
                                        error: function (xhr, status, error) {
                                            console.log(error.Message);
                                        },
                                        success: function (msg_back) {

                                            jQuery('.plus_widget_wrapper').html(msg_back);

                                            jQuery('.rfq_page_plus_Widget').html(msg_back);


                                        }
                                    });

                                    <?php endif; ?>


                                }


                            }
                        });

                    });

                    jQuery(image_div).hide();


                });
            </script>


            <?php
// phpcs:enable

            do_action('gpls_woo_rfq_after_ajax_add_to_quote');


            return true;

        }

        public function gpls_woo_rfq_ajax_add_to_fav_print_script()
        {

            if (is_admin() || get_option('settings_gpls_woo_rfq_allow_favorites','no')=='no') return false;

            $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
            $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

            if (($allow_favs=="yes" && (is_user_logged_in())) ||
                ( $allow_favs=="yes" && $allow_favs_anon=='yes') ) {

                $link_to_fav_page = get_option('rfq_cart_sc_section_show_link_to_favorites_page','');

                ob_start();
                wc_get_template('woo-rfq/link-to-favs-page.php',
                    array('link_to_fav_page' => $link_to_fav_page,
                    ), '', gpls_woo_rfq_WOO_PATH);
                $result = ob_get_clean();
            }else{
                $result=false;
            }

            if(!$result)return;


        // phpcs:disable

            ?>
            <script type="application/javascript">

                function handle_long_str(str) {
                    var lines = str.split(/\n/);
                    var output = [];
                    var outputText = [];
                    for (var i = 0; i < lines.length; i++) {

                        if (/\S/.test(lines[i])) {
                            outputText.push('"' + $.trim(lines[i]) + '"');
                            output.push($.trim(lines[i]));
                        }
                    }
                    return outputText;
                }


                jQuery(window).on("load",function () {
                    var image_div;

                    jQuery(".link_to_favs_page_form").submit(function (e) {

                        var form = jQuery(this); //wrap this in jQuery

                        e.preventDefault();

                        var product_id = jQuery(form).find('input[name="product_id"]').val();;
                      //  alert(product_id);

                        image_div ="#image_" + product_id;
                        fav_link ="#fav_link_" + product_id;


                        jQuery(image_div).show();
                        var str = jQuery(this).serialize();


                        jQuery.ajax({
                            type: "POST",
                            url: `${window.location.origin}/wp-admin/admin-ajax.php`,
                            data: {
                                action: "add_to_favs_action",  // the action to fire in the server
                                data: str,         // any JS object
                            },

                            success: function (msg) {

                              //  console.log(msg);

                                if (typeof msg.data !== 'undefined' && typeof msg.data.location !== 'undefined') {

                                    jQuery(image_div).hide();
                                    window.location.replace(msg.data.location);
                                    return;

                                } else {

                                    var note_id = "#notefav_" + product_id;
//alert(note_id);
                                    jQuery(fav_link).hide();
                                    jQuery(note_id).html(<?php echo '"'.$result.'"'; ?>);

                                    jQuery(image_div).hide();

                                }

                            }
                        });

                    });

                    jQuery(image_div).hide();


                });
            </script>


            <?php
// phpcs:enable

            do_action('gpls_woo_rfq_after_ajax_add_to_quote');


            return true;

        }



        public function gpls_woo_rfq_normal_checkout_cart_routine()
        {
            //  if (!is_admin())
            {
                /*    */

                // gpls_woo_rfq_remove_filters_normal_checkout();
                //if (!is_admin())
                {
                    //    add_action('init', 'gpls_woo_rfq_print_script_init', 1000);
                }

                //   add_filter('woocommerce_add_cart_item_data', array($this, 'gpls_woo_rfq_add_cart_item_data'), 1000, 3);

                add_action("gpls_woo_rfq_before_cart", array($this, "gpls_woo_rfq_cart_before_cart"), 1000);

                add_filter('woocommerce_widget_cart_is_hidden', array($this, 'filter_woocommerce_widget_cart_is_hidden'), 1000, 1);


            }
        }


        public function filter_woocommerce_widget_cart_is_hidden($is_cart)
        {

            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

            $link_to_rfq_page = (trim(preg_replace('{/$}', '', $link_to_rfq_page)));

            if(isset($_SERVER['REQUEST_URI'])){
                $current_page = (trim(preg_replace('{/$}', '', (get_site_url()) . sanitize_url( wp_unslash($_SERVER['REQUEST_URI'])))));

            }else{
                $current_page = get_site_url();
            }


            //   WordPress.Security.NonceVerification.Recommended


            if (isset($_POST['gpls_woo_rfq_nonce'])
                && wp_verify_nonce(sanitize_key(wp_unslash($_POST['gpls_woo_rfq_nonce'])),'gpls_woo_rfq_handle_rfq_cart_nonce') &&
                (trim($link_to_rfq_page) == trim($current_page) || isset($_REQUEST['removed_item']))) {

                $is_cart = true;
            }



            return $is_cart;

//
        }

        public function gpls_woo_rfq_cart_before_cart()
        {
            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');
        }


        public function gpls_woo_rfq_woocommerce_cart_is_empty()
        {


        }

        public function gpls_woo_rfq_woocommerce_before_cart()
        {


        }


        public function gpls_woo_rfq_woocommerce_after_mini_cart()
        {
        }

        public function gpls_woo_rfq_woocommerce_before_mini_cart()
        {


        }

        public function gpls_woo_rfq_woocommerce_after_cart()
        {
            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq") {

                $rfq_check = true;
                $normal_check = false;
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' && !is_user_logged_in()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }


            if ($rfq_check && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no')
            {


                $rfq_product_script = "<div class='gpls_script' style='display: none'> 
                <script>jQuery( document ).ready( function() {jQuery( '.tax-rate' ).hide();
                 jQuery( '.cart-subtotal' ).hide(); jQuery( '.order-total' ).hide();jQuery( '.tax-total' ).hide();} );
                 </script></div>";
            } else {
                $rfq_product_script = '';
            }





            echo $rfq_product_script;// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }


        public function gpls_woo_rfq_woocommerce_cart_item_visible($visible, $cart_item, $cart_item_key)
        {

            if (isset($cart_item['rfq'])) {
                if ($cart_item['rfq'] === 'yes') {
                    $visible = false;
                }
            }

            return $visible;

        }




        public function gpls_woo_rfq_remove_rfq_cart_item()
        {
        }


        public function gpls_woo_rfq_after_after_shop_loop_item()
        {



            global $product;

            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }

            $hide_button = apply_filters('gplsrfq_hide_after_shop_loop_item', false, $product);

            if ($hide_button) {
                return;
            }

            if (function_exists('is_user_logged_in'))
            {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
                    && !is_user_logged_in()
                    && get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
                ) {
                    return ;
                }
            }

            /*$data_var = "no";
            if ($product->get_type() == 'variable') {
                $data_var = "yes";
            }*/
            // $data_var = "no";
            //if ($product->get_type() == 'variable')
            {
                $data_var = $product->get_type();
            }

            /*if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option, 'woo-rfq-for-woocommerce');
                DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
            }
            $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

            $rfq_product_script = "";


            $rfq_enable = gpls_woo_get_rfq_enable($product);


            if (function_exists('is_user_logged_in'))
            {

                if (!is_user_logged_in()
                    && get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
                    && get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote', 'no') == "yes"
                    && $rfq_enable=="yes"
                ) {

                    $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
                    $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

                    if (($allow_favs=="yes" && is_user_logged_in()) ||
                        (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
                        ob_start();
                        wc_get_template('woo-rfq/add-to-favs-form.php',
                            array('product' => $product,

                            ), '', gpls_woo_rfq_WOO_PATH);

                        $result_add_to_fav = ob_get_clean();
                        echo '<div>' . $result_add_to_fav . '</div>';return ;
                    }else{
                        return ;
                    }


                 //   return ;
                }
            }



            if (isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                $rfq_check = false;
                $normal_check = false;

                if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                        && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                    ) {
                        $rfq_check = false;
                        $normal_check = true;

                    }else{
                        $rfq_check = true;
                        $normal_check = false;
                    }
                }

                if($rfq_check){

                    if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                        'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                        && !$product->backorders_allowed()
                    ) {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                        $read_more = __($read_more, 'woo-rfq-for-woocommerce');


                        if ($read_more != "") {
                            $request_quote = $read_more;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);
                    }
                }else{

                    if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                        'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                        && !$product->backorders_allowed()
                        && $rfq_enable !="yes"
                    )
                    {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                        $read_more = __($read_more, 'woo-rfq-for-woocommerce');


                        if ($read_more != "") {
                            $request_quote = $read_more;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);

                    }
                }


            }


            if ($rfq_enable != 'yes' && isset( $GLOBALS["gpls_woo_rfq_checkout_option"])
                &&  $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                    'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                    && !$product->backorders_allowed()
                )
                {

                    $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                    $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                    $read_more = __($read_more, 'woo-rfq-for-woocommerce');


                    if ($read_more != "") {
                        $request_quote = $read_more;// "Request Quote"
                    }

                    $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);
                }

                global $rfq_cart;

                global $rfq_variations;

                $in_rfq = false;

                $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

                if (($gpls_woo_rfq_cart != false)) {

                    foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                        if (isset($values['product_id'])) {
                            $product_id = $values['product_id'];


                            //if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                            if (get_the_ID() == $product_id ) {
                                $in_rfq = true;
                            }
                        }
                    }

                }

                if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                    $request_quote = get_option('rfq_cart_wordings_in_rfq', __('In Quote List', 'woo-rfq-for-woocommerce'));//"In RFQ"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                    $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

                } else {
                    $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                    $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);
                }

                $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

                ?>

                <?php if (($in_rfq == true) && isset($link_to_rfq_page)) : ?>
                    <?php


                    $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
                    wc_get_template('woo-rfq/link-to-cart.php',
                        array('link_to_rfq_page' => $link_to_rfq_page,
                        ), '', gpls_woo_rfq_WOO_PATH);

                    $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
                    $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

                    if (($allow_favs=="yes" && is_user_logged_in()) ||
                        (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {

                        wc_get_template('woo-rfq/add-to-favs-form.php',
                            array('product' => $product,

                            ), '', gpls_woo_rfq_WOO_PATH);



                    }
                    ?>
                <?php endif; ?>

                <?php
            }


            ?>
            <?php if ($rfq_enable == 'yes'
            && isset( $GLOBALS["gpls_woo_rfq_checkout_option"])
            &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout") : ?>

            <?php

            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {

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
            if ($rfq_check) {
                $rfq_product_script = "";
            }


            $rfq_id = $product->get_id();

            global $rfq_cart;

            global $rfq_variations;

            $in_rfq = false;

            //if(!function_exists('gpls_woo_rfq_get_item'))
            {
             //   require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
              //  require_once(ABSPATH . 'wp-includes/class-phpass.php');
            }


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    if (isset($values['product_id'])) {
                        $product_id = $values['product_id'];


                        //if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                        if (get_the_ID() == $product_id ) {
                            $in_rfq = true;
                        }
                    }
                }

            }

            if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                $request_quote = get_option('rfq_cart_wordings_in_rfq', __('In Quote List', 'woo-rfq-for-woocommerce'));//"In RFQ"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

            } else {
                $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);
            }


            $no_add_to_cart = 'no';
            $no_add_to_cart = apply_filters('gpls_woo_rfq_no_add_to_cart', $no_add_to_cart, $product, $normal_check);

            if ($normal_check == true) {

                if (($in_rfq == false && $product->get_type() == 'simple')) {
                    //return;
                }

                if (($in_rfq == false && $product->get_type() == 'variable')) {

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'
                        && $no_add_to_cart == 'no') {
                        return;
                    }

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes' && $no_add_to_cart == 'yes') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');


                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);
                    }


                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no' && $no_add_to_cart == 'no') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);


                    }

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no' && $no_add_to_cart == 'yes') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);


                    }


                }
            }


            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();


            $proceed = apply_filters('gpls_woo_rfq_after_after_shop_loop_item_proceed', true);

            if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                && !$product->backorders_allowed()
            ) {

                $request_quote = __('Read more', 'woo-rfq-for-woocommerce', '');// "Request Quote"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $read_more = get_option('settings_gpls_woo_rfq_read_more', '');// "Request Quote"
                $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                if ($read_more != "") {
                    $request_quote = $read_more;// "Request Quote"
                }

                $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);
            }
            ?>

            <?php if ($in_rfq == false)  : ?>


                <?php if ($proceed == true) : ?>

                    <?php
                    $gpls_woo_rfq_file_add_to_quote_styles = array();
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_styles'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] = '';

                    $gpls_woo_rfq_file_add_to_quote_styles = apply_filters('gpls_woo_rfq_add_to_quote_styles', $gpls_woo_rfq_file_add_to_quote_styles);

                 ob_start();
                    wc_get_template('woo-rfq/add-to-quote.php',
                        array('rfq_id' => $rfq_id,
                            'product' => $product,
                            'rfq_check' => $rfq_check,
                            'data_var' => $data_var,
                            'product_type' => $product->get_type(),
                            'request_quote' => $request_quote,
                            'gpls_woo_rfq_file_add_to_quote_styles' => $gpls_woo_rfq_file_add_to_quote_styles,
                        ), '', gpls_woo_rfq_WOO_PATH);
                    $result_add_to_quote = ob_get_clean();
                    $result_add_to_fav='';


                    $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
                    $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

                    if (($allow_favs=="yes" && is_user_logged_in()) ||
                        (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
                        ob_start();
                        wc_get_template('woo-rfq/add-to-favs-form.php',
                            array('rfq_id' => $rfq_id,
                                'product' => $product,
                                'rfq_check' => $rfq_check,
                                'data_var' => $data_var,
                                'request_quote' => $request_quote,
                                'gpls_woo_rfq_file_add_to_quote_styles' => $gpls_woo_rfq_file_add_to_quote_styles,
                            ), '', gpls_woo_rfq_WOO_PATH);

                        $result_add_to_fav = ob_get_clean();
                    }
                    echo '<div>' . $result_add_to_quote . $result_add_to_fav . '</div>';
                    ?>


                <?php endif; ?>

            <?php elseif (($in_rfq == true) && isset($link_to_rfq_page)) : ?>
                <?php


                wc_get_template('woo-rfq/link-to-cart.php',
                    array('link_to_rfq_page' => $link_to_rfq_page,
                    ), '', gpls_woo_rfq_WOO_PATH);

                $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
                $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

                if (($allow_favs=="yes" && is_user_logged_in()) ||
                    (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
                    wc_get_template('woo-rfq/add-to-favs-form.php',
                        array('product' => $product,

                        ), '', gpls_woo_rfq_WOO_PATH);
                }
                ?>
            <?php endif; ?>


        <?php endif; ?>

            <?php



        }


        public function gpls_woo_rfq_before_add_to_cart_button()
        {


            global $product;

            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }


            $rfq_enable = gpls_woo_get_rfq_enable($product);

            if (!is_admin()) {


                $rfq_check = false;
                $normal_check = false;
                //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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


                if ($rfq_check == false) {

                    if ($rfq_enable == 'no' && $product->get_price() == 0) {

                        //   exit();
                    }


                }

            }


        }

        public function gpls_woo_rfq_after_add_to_cart_favorites(){

            global $product;

            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }
           ob_start();

            wc_get_template('woo-rfq/add-to-favs-form-single.php',
                array('product' => $product,

                ), '', gpls_woo_rfq_WOO_PATH);

            $result_add_to_fav = ob_get_clean();

            echo $result_add_to_fav;



             }
        public function gpls_woo_rfq_after_add_to_cart_button()
        {


            global $product;
            //  WC()->init();
            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }


            $rfq_enable = gpls_woo_get_rfq_enable($product);

            if (function_exists('is_user_logged_in'))
            {

                if (!is_user_logged_in()
                    && get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
                    && get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote', 'no') == "yes"
                    && $rfq_enable=="yes"
                ) {
                    $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
                    $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

                    if (($allow_favs=="yes" && is_user_logged_in()) ||
                        (get_current_user_id() ==0 && $allow_favs=="yes" && $allow_favs_anon=='yes') ) {
                        ob_start();
                        wc_get_template('woo-rfq/add-to-favs-form.php',
                            array('product' => $product,

                            ), '', gpls_woo_rfq_WOO_PATH);

                        $result_add_to_fav = ob_get_clean();
                        echo '<div>' . $result_add_to_fav . '</div>';return ;
                    }else{
                        return ;
                    }
                }
            }

           /* if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option, 'woo-rfq-for-woocommerce');

                DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
            }
            $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

            $rfq_product_script = "";



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

            ?>


            <?php if ($rfq_enable == 'yes' && isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") : ?>

            <?php

            //test
            if (($normal_check && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no')) {


                if (!is_admin()) {
                    add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script', 1000);
                    add_action('wp_add_inline_script', 'gpls_woo_rfq_print_script', 1000);
                    add_action('wp_enqueue_script', 'gpls_woo_rfq_print_script', 1000);

                    $rfq_product_script = "jQuery(document ).ready( function() {
                 
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
                     
                     jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).hide();
                    jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).attr('style','visibility: hidden !important');                   
jQuery( '.gpls_rfq_set' ).show();jQuery( '.gpls_rfq_set' ).attr('style','visibility: visible !important');
jQuery( '.amount,.bundle_price' ).hide();jQuery( '.amount,.bundle_price' ).attr('style','visibility: collapse');

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
} ); ";

                    echo "<div class='gpls_script' style='display: none'><script> " .
                        ($rfq_product_script). '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//end test


                    $rfq_product_script = "jQuery(document ).ready( function() {
                    
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
                    
                    
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','visibility: collapse'); 

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
} ); ";


                    echo "<div class='gpls_script' style='display: none'><script> " .
                        $rfq_product_script. '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


                }
            }


            $rfq_id = $product->get_id();

            global $rfq_cart;

            global $rfq_variations;

            $in_rfq = false;


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    if (isset($values['product_id'])) {
                        $product_id = $values['product_id'];


                        //if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                        if (get_the_ID() == $product_id ) {
                            $in_rfq = true;
                        }
                    }
                }

            }

            if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                $request_quote = get_option('rfq_cart_wordings_in_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));//"In RFQ"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

            } else {
                $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);

            }


            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

            $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));
            $view_your_cart_text = __($view_your_cart_text, 'woo-rfq-for-woocommerce');
            $view_your_cart_text = apply_filters('gpls_woo_rfq_request_quote_text', $view_your_cart_text);

            $gpls_woo_rfq_file_add_to_quote_styles = array();
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_styles'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] = '';

            $gpls_woo_rfq_file_add_to_quote_styles = apply_filters('gpls_woo_rfq_add_to_quote_styles', $gpls_woo_rfq_file_add_to_quote_styles);

            wc_get_template('woo-rfq/add-to-quote-single.php',
                array('rfq_product_script' => $rfq_product_script,
                    'product' => $product,
                    'in_rfq' => $in_rfq,
                    'rfq_check' => $rfq_check,
                    'normal_check' => $normal_check,
                    'request_quote' => $request_quote,
                    'view_your_cart_text' => $view_your_cart_text,
                    'rfq_enable' => $rfq_enable,
                    'link_to_rfq_page' => $link_to_rfq_page,
                    'gpls_woo_rfq_file_add_to_quote_styles' => $gpls_woo_rfq_file_add_to_quote_styles,
                ), '', gpls_woo_rfq_WOO_PATH);
            ?>


        <?php else: ?>
            <?php
            if (!is_admin()) {

                add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script_show_single_add', 1000);


                $rfq_product_script = "jQuery(document ).ready( function() { 
    jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).show();
    jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).attr('style','visibility: visible !important');
jQuery('.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button').prop('disabled',false);;
                 jQuery('.gpls_rfq_set').prop('disabled', false);
    }); ";


                echo "<div class='gpls_script' style='display: none'><script> " .
                    $rfq_product_script. '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            }
            ?>
        <?php endif; ?>

            <?php

            if ($rfq_check) {
                //  $rfq_product_script = "";
                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == "no") {

                    if (!is_admin()) {

                        add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script', 1000);
                        add_action('wp_add_inline_script', 'gpls_woo_rfq_print_script', 1000);
                        add_action('wp_enqueue_script', 'gpls_woo_rfq_print_script', 1000);

                        $rfq_product_script = "jQuery(document ).ready( function() { 
                        
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
                        
                        
                        jQuery( '.amount,.bundle_price, .product-selector__price' ).hide();
jQuery( '.amount,.bundle_price, .product-selector__price' ).attr('style','visibility: collapse');

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
            } );";


                        echo "<div class='gpls_script' style='display: none'><script> " .
                            $rfq_product_script. '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


                        $rfq_product_script = "jQuery(document ).ready( function() {
                        
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
                        
                        
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','visibility: collapse');
 
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
} );";


                        echo "<div class='gpls_script' style='display: none'><script> " .
                            $rfq_product_script. '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }


                }
            }

            if ($normal_check) {

                if (function_exists('is_user_logged_in')) {
                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'

                        && !(get_option('settings_gpls_woo_rfq_hide_visitor_prices_normal', 'no') == 'yes' && !is_user_logged_in())) {
                        if (!is_admin()) {

                            {
                                $rfq_product_script = "jQuery(document ).ready( function()
                                 { jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).show();
jQuery( '.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button' ).attr('style','visibility: visible !important');
jQuery('.single_add_to_cart_button,.storefront-sticky-add-to-cart__content-button').prop('disabled',false);
                 jQuery('.gpls_rfq_set').prop('disabled', false);

} ); ";


                                echo "<div class='gpls_script' style='display: none'><script> " .
                                    $rfq_product_script. '</script></div>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                                add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script_show_single_add', 1000);

                            }


                        }


                    }
                }
            }


        }


        public function gpls_woo_rfq_add_to_cart_link_shop_favorites($link, $product)
        {



             $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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

            $rfq_enable = gpls_woo_get_rfq_enable($product);
            if ($normal_check && $rfq_enable == 'yes') {
                return $link;
            }

            $allow_favs=get_option('settings_gpls_woo_rfq_allow_favorites','no');
            $allow_favs_anon=get_option('settings_gpls_woo_rfq_allow_anon_favorites','no');

            if (
                ($allow_favs=="yes" && is_user_logged_in())
                ||
                (get_current_user_id() ==0 && $allow_favs_anon=='yes' && $allow_favs=="yes")
            )
            {
                ob_start();
                wc_get_template('woo-rfq/add-to-favs-form.php',
                    array('product' => $product,

                    ), '', gpls_woo_rfq_WOO_PATH);
                $result = ob_get_clean();
                return $link."<div style='display:block'>{$result}</div>";
            }else{
                return $link;
            }


        }



        public function gpls_woo_rfq_add_to_cart_link_shop($link, $product)
        {

            if ($product->get_type() === 'external') {
                return $link;
            }

            $read_more = "";
            //  global $product;

            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }


            $rfq_enable = gpls_woo_get_rfq_enable($product);

           /* if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option, 'woo-rfq-for-woocommerce');
                DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
            }
            $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

            $rfq_product_script = "";


            $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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


            $pf = new WC_Product_Factory();
            $product = $pf->get_product($product->get_id());


            if ($rfq_enable == 'yes') {

                if (( isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
                    && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == "no")
                ) {


                    return '';
                }

            }

            if (( isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
            )
            ) {

                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'

                ) {
                    $pf = new WC_Product_Factory();

                    $product = $pf->get_product($product->get_id());


                    $rfq_enable = gpls_woo_get_rfq_enable($product);


                    if ($rfq_enable == 'no') {

                        return $this->gpls_woo_rfq_add_to_cart_link($link, $product);

                    }

                    if ($rfq_enable == "yes") {
                        $data = $product->get_data();

                        $this_price = $data["price"];

                        if (trim($data["sale_price"]) != '') {
                            $this_price = $data["sale_price"];
                        }
                        $type = $product->get_type();
                        // if ($type == 'simple' || $type == 'variable')
                        {
                            if (trim($this_price) === '') {



                            }
                        }
                        //   return '3333333';
                        return $this->gpls_woo_rfq_add_to_cart_link($link, $product);;
                    }
                }


                if ($rfq_enable == 'yes') {

                    if (( isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
                        && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == "yes")
                    ) {

                        $type = $product->get_type();
                        //if ($type == 'simple' || $type == 'variable')
                        {
                            if (trim($this_price) === '') {


                            }
                        }


                    }

                    return $this->gpls_woo_rfq_add_to_cart_link($link, $product);
                }


            }

            if ($rfq_check == true) {

                $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";

            }

            if (isset( $GLOBALS["gpls_woo_rfq_checkout_option"]) &&  $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

                if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                    if (
                        get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                    ) {
                        $rfq_check = false;
                        $normal_check = true;

                    }
                }

                if($normal_check){

                    if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                        'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                        && !$product->backorders_allowed()
                        && $rfq_enable=="yes"

                    )
                    {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                        $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                        $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                        if ($read_more != "") {
                            $request_quote = $read_more;// "Request Quote"
                        }

                        $id = $product->get_id();
                        $sku = $product->get_sku();

                        $url = esc_url($product->get_permalink());
                        $link = '<a  href="' . $url . '" data-product_id="' . $id . '" data-product_sku="' . $sku . '" class="button product_type_simple ajax_add_to_cart">' . $request_quote . '</a>';

                        return $this->gpls_woo_rfq_add_to_cart_link($link, $product);



                    }
                }


            }


            if (  $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

                if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                    if (
                        get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                        && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "no"

                    ) {
                        $rfq_check = true;
                        $normal_check = false;

                    }
                }

                if($rfq_check){

                    if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                        'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                        && !$product->backorders_allowed()


                    )
                    {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                        $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                        if ($read_more != "") {
                            $request_quote = $read_more;// "Request Quote"
                        }

                        $id = $product->get_id();
                        $sku = $product->get_sku();
                        //$url=esc_url($product->add_to_cart_url());
                        $url = esc_url($product->get_permalink());
                        $link = '<a  href="' . $url . '" data-product_id="' . $id . '" data-product_sku="' . $sku . '" class="button product_type_simple ajax_add_to_cart">' . $request_quote . '</a>';
                        //  return '1617';
                        return $this->gpls_woo_rfq_add_to_cart_link($link, $product);

                    }
                }


            }



            if (  $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {

                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

                if(is_plugin_active( 'rfqtk/rfqtk.php')) {
                    if (
                        get_option('settings_gpls_woo_rfq_show_prices', 'no') == "no"
                        && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "no"
                        && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "no"

                    ) {
                        $rfq_check = true;
                        $normal_check = false;

                    }
                }

                if($rfq_check){

                    if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                        'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                        && !$product->backorders_allowed()


                    )
                    {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                        $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                        $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                        $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                        if ($read_more != "") {
                            $request_quote = $read_more;// "Request Quote"
                        }

                        $id = $product->get_id();
                        $sku = $product->get_sku();
                        //$url=esc_url($product->add_to_cart_url());
                        $url = esc_url($product->get_permalink());
                        $link = '<a  href="' . $url . '" data-product_id="' . $id . '" data-product_sku="' . $sku . '" class="button product_type_simple ajax_add_to_cart">' . $request_quote . '</a>';
                        //  return '1670';
                        return $this->gpls_woo_rfq_add_to_cart_link($link, $product);



                    }
                }


            }


            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                && get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
            ) {
                // return '55555';
                return $this->gpls_woo_rfq_add_to_cart_link($link, $product);
            }


            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq"
                && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"

            ) {


                return $this->gpls_woo_rfq_add_to_cart_link($link, $product);
            }

            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout" && $rfq_enable != 'yes') {

                return $this->gpls_woo_rfq_add_to_cart_link($link, $product);

            }


            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq"
                && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "no") {
                // return '7777';
                return $this->gpls_woo_rfq_add_to_cart_link($link, $product);
            }

            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq" && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                && get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
            ) {
                // return '88';
                return $this->gpls_woo_rfq_add_to_cart_link($link, $product);
            }


        }

        /**
         * @param $link
         * @param $product
         * @return mixed|void
         */
        public function gpls_woo_rfq_add_to_cart_link($link, $product)
        {
            $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);

            //$link = $link. gpls_woo_rfq_fav_image16;

            return $link;
        }

        public function woo_custom_cart_button_text($product_add_to_cart_text, $product)
        {

//global $product;

            if ($product->get_type() == 'external') {
                return $product_add_to_cart_text;
            }


            $is_product = false;

            global $wp_query;
            if (isset($wp_query)) {
                if (function_exists('is_product')) {
                    $is_product = is_product();
                }
            }


            if (($product->get_type() == 'variable') && $is_product == false) {

                return $product_add_to_cart_text;//"In RFQ"

            }


            $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
            $rfq_check = false;
            $normal_check = false;
            $checkout = "";

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
                add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);
                $rfq_check = true;
                $normal_check = false;
                $checkout = "rfq";
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
                $checkout = "normal";
            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
                    && !is_user_logged_in()) {
                    $rfq_check = true;
                    $normal_check = false;
                    $checkout = "rfq";
                }
            }

            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            $five_c=false;

            if(is_plugin_active( 'rfqtk/rfqtk.php'))
            {
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                    && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_plus_normal_checkout', 'no') == "yes"
                    && get_option('settings_gpls_woo_rfq_limit_to_rfq_only', 'no') == "yes"

                ) {

                    $five_c=true;
                    $rfq_check = false;
                    $normal_check = true;

                }
            }



            if ($rfq_check) {
                $default_text = __('Add to Quote', 'woo-rfq-for-woocommerce');
                $default_text = __($default_text, 'woo-rfq-for-woocommerce');
            } else {
                $default_text = __('Add to Cart', 'woo-rfq-for-woocommerce');
                $default_text = __($default_text, 'woo-rfq-for-woocommerce');
            }



            global $woocommerce;


            $rfq_enable = gpls_woo_get_rfq_enable($product);

            $add_txt = get_option('rfq_cart_wordings_add_to_cart', $default_text);
            $add_txt = __($add_txt, 'woo-rfq-for-woocommerce');


            $in_txt = get_option('rfq_cart_wordings_in_cart', $default_text);
            $in_txt = __($in_txt, 'woo-rfq-for-woocommerce');



            if ($normal_check) {
                if ($rfq_enable != "yes" || !isset($rfq_enable)) {

                    //$add_txt = $product_add_to_cart_text;
                    if(!$five_c)
                    {
                        // $product=wc_get_product(1);
                        if (function_exists('WC') && WC()->cart != null ) {
                            $product_cart_id = WC()->cart->generate_cart_id( $product->get_id() );
                            $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );
                            if ( $in_cart ) {
                                return $in_txt;
                            }
                        }else{
                            return $product_add_to_cart_text;
                        }

                    }
                }
            }



            if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                && !$product->backorders_allowed()
            ) {



                $add_txt = $in_txt = $product_add_to_cart_text;
            }


            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !is_user_logged_in()) {

                    $add_txt = get_option('rfq_cart_wordings_add_to_rfq', $default_text);
                    $add_txt = __($add_txt, 'woo-rfq-for-woocommerce');

                    $in_txt = get_option('rfq_cart_wordings_in_rfq', $default_text);
                    $in_txt = __($in_txt, 'woo-rfq-for-woocommerce');

                }
            }


            if (isset($woocommerce) && $woocommerce != null && $woocommerce->cart != null) {
                foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
                    $_product = $values['data'];

                    if(!is_object($_product)){
                        $_product=wc_get_product($values['product_id']);
                    }

                    if (!$_product || !is_object($_product)) {
                        continue;
                    }

                    if (get_the_ID() == $_product->get_id()) {

                        $add_txt = $in_txt;
                        break;

                    }
                }
            }

            if (isset($woocommerce) && $woocommerce->cart != null) {

                $product_cart_id = WC()->cart->generate_cart_id( $product->get_id());

                if ( WC()->cart->find_product_in_cart( $product_cart_id )) {

                    $add_txt = $in_txt;
                }
            }


            $rfq_checkout_mode = $checkout;

            $add_txt = apply_filters('gpls_woo_rfq_custom_add_to_cart_button_text', $add_txt, $product, $rfq_checkout_mode);



            if($rfq_check){

                if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                    'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                    && !$product->backorders_allowed()
                ) {

                        $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                    $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                    $read_more = __($read_more, 'woo-rfq-for-woocommerce');


                    if ($read_more != "") {
                        $request_quote = $read_more;// "Request Quote"
                    }


                    $add_txt = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);

                }
            }else{

                if (get_option('settings_gpls_woo_rfq_allow_out_of_stock', 'no') == "no" &&
                    'yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock'
                    && !$product->backorders_allowed()
                    && $rfq_enable !="yes"
                )
                {

                    $request_quote = __('Read more', 'woo-rfq-for-woocommerce');// "Request Quote"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                    $read_more = get_option('settings_gpls_woo_rfq_read_more');// "Request Quote"
                    $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                    if ($read_more != "") {
                        $request_quote = $read_more;// "Request Quote"
                    }

                    $add_txt = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);

                }
            }

            do_action('gpls_woo_rfq_add_to_cart_button_text_action', $add_txt, $product, $rfq_checkout_mode);

            $add_txt= apply_filters('gpls_woo_rfq_add_to_cart_button_text', $add_txt, $product, $rfq_checkout_mode);




            return $add_txt;



        }

        /**
         * @param $wp
         * @return array
         */
        public function get_url()
        {

            if(isset($_SERVER['REQUEST_URI'])){
                $url = get_site_url() . sanitize_url( wp_unslash($_SERVER['REQUEST_URI']));

            }

            $current_url = gpls_woo_rfq_remove_http(  $url);
            return $current_url;

        }





    }

}

