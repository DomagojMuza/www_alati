<?php
/**
 * Plugin Name: NP Quote Request for WooCommerce
 * Description: NP Quote Request for WooCommerce enables your customers to easily submit a quote request to your WooCommerce store. It is very flexible and can be used in a variety of store settings. NP Quote Request for WooCommerce enables you to generate leads and engage with your customers!
 * Version: 2.4.11
 * Contributors: Neah Plugins,gplsaver
 * Author: Neah Plugins
 * Author URI: https://www.neahplugins.com/
 * Donate link: https://www.neahplugins.com/
 * Requires at least: 6.3
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * WC tested up to: 10.4.3
 * Text Domain: woo-rfq-for-woocommerce
 * Domain Path: /languages/
 * Copyright: 2018-2025 Neah Plugins.
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


if (!defined('gpls_woo_rfq_DIR')) {
//   WordPress.WP.I18n.NoEmptyStrings
//   WordPress.WP.I18n.NonSingularStringLiteralText



    DEFINE('gpls_woo_rfq_DIR', plugin_dir_path(__FILE__));
    DEFINE('gpls_woo_rfq_URL', plugin_dir_url(__FILE__));
    DEFINE('gpls_woo_rfq_FILE_NAME', (__FILE__));
    DEFINE('gpls_woo_rfq_PLUGIN_PATH', untrailingslashit(plugin_basename(__FILE__)));
    DEFINE('gpls_woo_rfq_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/');
    DEFINE('gpls_woo_rfq_WOO_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/');
    DEFINE('gpls_woo_rfq_GLOBAL_NINJA_FORMID', get_option('settings_gpls_woo_ninja_form_option'));



    $small_src = gpls_woo_rfq_URL . '/gpls_assets/img/favorite_small.png';
    $large_src = gpls_woo_rfq_URL . '/gpls_assets/img/favorite_large.png';
    $image_fav_small = '<image title="Add to your favorites" class="image_favgpls16" style="max-width:16px !important"  src="' . $small_src . '" />';
    $image_fav_large = '<image title="Add to your favorites" class="image_favgpls24" style="max-width:24px !important"  src="' . $large_src . '" />';

    DEFINE('gpls_woo_rfq_fav_image16', $image_fav_small);
    DEFINE('gpls_woo_rfq_fav_image24', $image_fav_large);


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
        && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'yes'
    ) {
        $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
    }

    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
        && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'

    ) {
        $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
    }

    if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
        && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no'
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
}



require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
require_once(ABSPATH . 'wp-includes/class-phpass.php');

require_once(gpls_woo_rfq_DIR . 'includes/classes/checkout/gpls_woo_rfq_ncheckout.php');
require_once(ABSPATH . 'wp-admin/includes/plugin.php');


if (!function_exists('gpls_woo_rfq_check_base_correct_info_plugins')) {
    function gpls_woo_rfq_check_base_correct_info_plugins()
    {
        $show_update = false;
        $checkagain_url = admin_url('update-core.php?force-check=1');

        if (is_plugin_active('rfqtk/rfqtk.php')) {

            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/rfqtk/rfqtk.php');
            $version1 = $plugin_data['Version'];
            $rfqtk_version = '3.5';


            {
                ?>
                <?php if (version_compare($version1, $rfqtk_version) == -1): $show_update = true; ?>

                <div id="message" class="error notice notice-success is-dismissible"
                     style="font-size: medium;font-weight: 500;color: black">

                    <p>Please upgrade NP Quote Request for WooCommerce Plus to version:
                        <?php echo esc_js($rfqtk_version); ?>.</p>
                </div>
            <?php endif; ?>
                <?php
            }
        }

        if (is_plugin_active('rfqtk-pdf/rfqtk-pdf.php')) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/rfqtk-pdf/rfqtk-pdf.php');
            $version1 = $plugin_data['Version'];
            $pdf_version = '7.0';

            {
                //<a href="/wp-admin/update-core.php?force-check=1">Check again.</a>
                ?>
                <?php if (version_compare($version1, $pdf_version) == -1): $show_update = true; ?>

                <div id="message" class="error notice notice-success is-dismissible"
                     style="font-size: medium;font-weight: 500;color: black">

                    <p>Please upgrade NP Quote Request for WooCommerce – PDF to version:
                        <?php echo esc_js($pdf_version); ?>.</p>
                </div>
            <?php endif; ?>
                <?php
            }

        }

        if (is_plugin_active('rfqtk-upload/rfqtk-upload.php')) {

            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/rfqtk-upload/rfqtk-upload.php');
            $version1 = $plugin_data['Version'];
            $upload_version = '4.0';

            {
                ?>
                <?php if (version_compare($version1, $upload_version) == -1): $show_update = true; ?>

                <div id="message" class="error notice notice-success is-dismissible"
                     style="font-size: medium;font-weight: 500;color: black">

                    <p>Please upgrade NP Quote Request for WooCommerce – File Upload to
                        version <?php echo esc_js($upload_version); ?>.</p>
                </div>
            <?php endif; ?>
                <?php
            }

        }

        ?>
        <?php if (isset($show_update) && $show_update): ?>

        <div id="message" class="error notice notice-success is-dismissible"
             style="font-size: larger;font-weight: 600;color: black">
            <form method="get">

                <p> Check <a href="<?php echo esc_js($checkagain_url) ?>" class="btn btn-primary">here</a> and click on "check
                    again" in the update page for automatic update
                    or download from <a target="_blank" href="https://neahplugins.com/my-account/api-downloads//">downloads</a><br/>
                    <span style="color:black">to ensure correct functionality and compatibility with the NP Quote Request for WooCommerce</span>.
                </p>
            </form>
        </div>
    <?php endif; ?>
        <?php


    }
}


require_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_admin()) {
  //  add_action('admin_notices', 'gpls_woo_rfq_check_base_correct_info_plugins', 1000);

}

add_action(
    'before_woocommerce_init',
    function () {


        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);

        }

    }
);



require_once(gpls_woo_rfq_DIR . 'includes/classes/gpls_woo_rfq_functions.php');
require_once(plugin_dir_path(__FILE__) . '/woo-rfq-includes/woo-rfq-functions.php');


if (!is_admin()) {
    if (rfqtk_first_main()) return 0;
}


//require_once(gpls_woo_rfq_DIR . 'includes/classes/checkout/gpls_woo_rfq_ncheckout.php');


add_action('plugins_loaded', 'GPLS_WOO_RFQ_Main', 400);

// if (function_exists('is_plugin_active')) {
//if (class_exists('is_plugin_active'))
{
    //  GPLS_WOO_RFQ_Main();
}

function GPLS_WOO_RFQ_Main()
{
    //

    return $GLOBALS['GPLS_WOO_RFQ'] = GPLS_WOO_RFQ::instance();
    // return  GPLS_WOO_RFQ::instance();

}

class GPLS_WOO_RFQ
{

    /**
     * Plugin bootstrapping.
     */


    public static function init()
    {

     //   $needs_payment = get_option('settings_gpls_woo_rfq_no_payment_checkout', 'no');

        //if ($needs_payment == "yes")
        {
            //   return;
        }


        add_action('plugins_loaded', array(__CLASS__, 'includes'), 400);


        if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {
            add_filter('woocommerce_payment_gateways', 'add_gpls_woo_rfq_class', 1, 1);


            add_action('woocommerce_blocks_loaded', array(__CLASS__, 'gplswoo_gateway_rfq_block_support'));
        }
    }

    protected static $_instance = null;

    public static function instance()
    {


        if (is_null(self::$_instance))
            self::$_instance = new self();

        return self::$_instance;
    }


    /**
     * Plugin includes.
     */


    public function __construct()
    {
        if(!class_exists( 'woocommerce' )){
            return;
        }

        if (is_admin()) {
            //  add_action('admin_notices', 'gpls_woo_rfq_check_base_correct_info_plugins', 1000);

        }


        if (class_exists('WC_Payment_Gateway'))
        {
            //	require_once 'includes/class-wc-gateway-dummy.php';
            require_once 'includes/classes/gateway/class-wc-gateway-rfq.php';
            $GLOBALS['WC_Gateway_RFQ'] = new WC_Gateway_RFQ();
            	require_once 'includes/classes/gateway/wc-gateway-gpls-request-quote.php';
        }

            $this->gpls_rfq_setup_constants();
        $this->gpls_rfq_setup_includes();
        $this->setup_email();

        add_action('init', array($this, 'gpls_4woo_quote_wp_init'), 1000);

        add_action('init', array($this, 'gpls_woo_rfq_setup_custom_css'), 100);

        // add_action('plugins_loaded', array($this, 'gpls_woo_rfq_init'), 100);
        $this->gpls_woo_rfq_init();
        //emails

       //   WordPress.Security.NonceVerification.Missing
        // phpcs:disable

        if (isset($_POST['gpls-woo-rfq_checkout']) && $_POST['gpls-woo-rfq_checkout'] == "true") {

            add_action('wp_loaded', array($this, 'check_for_rfq_checkout'), 1000);

        }
// phpcs:enable
        add_action('woocommerce_before_checkout_process', 'gpls_woo_rfq_woocommerce_RFQ_only_add_to_cart', 1000);

        add_action('init', 'gpls_woo_rfq_woocommerce_RFQ_load_payment_gateway', 1000);//
        //add_filter('woocommerce_data_get_price','gpls_woo_rfq_woocommerce_data_get_price',-1,2);

        add_action('wp_loaded', 'gpls_woo_rfq_order_received', 1000);

        add_action('wp_loaded', array($this, 'gpls_woo_rfq_wp_loaded'), 1000);

        add_action('wp_loaded', 'gpls_rfq_remove_rfq_cart_item', 10000);

        add_action('wp_loaded', 'gpls_rfq_remove_fav_cart_item', 10000);
        add_action('wp_loaded', 'gpls_rfq_add_fav_cart_item', 10000);

        add_action('wp_loaded', 'gpls_rfq_update_rfq_cart', 1000);

        if(!is_admin()) {
            add_action('wp_loaded', array($this, 'gpls_woo_rfq_setup_menu'), 1000);
        }

        add_filter('wp_get_nav_menu_items', 'gpls_woo_rfq_hide_rfq_page', 999, 3);

       // add_filter('wp_get_nav_menu_items', 'gpls_woo_rfq_hide_fav_page', 999, 3);

        add_action('wp_footer', array($this, 'footer_action'), 1000);

        //add_action('plugins_loaded', array($this, 'gpls_woo_rfq_load_textdomain'));
        $this->gpls_woo_rfq_load_textdomain();

        add_action('init', array($this, 'gpls_woo_rfq_setup_customer_cookie'), 10);



        add_action('wp_logout', array($this, 'gpls_woo_rfq_logout'), 100);

        if (!has_action('admin_enqueue_scripts', array($this, 'gpls_woo_rfq_enqueue_admin_css'))) {
            // add_action('admin_head', array($this, 'gpls_woo_rfq_enqueue_admin_css'));
            add_action('admin_enqueue_scripts', array($this, 'gpls_woo_rfq_enqueue_admin_css'), 1000);
        }

        add_action('gpls_woo_rfq_after_normal_checkout', array($this, 'gpls_woo_rfq_after_normal_checkout'), 100, 1);




}


    public static function includes()
    {


        if (class_exists('WC_Payment_Gateway')) {
            //	require_once 'includes/class-wc-gateway-dummy.php';
            require_once 'includes/classes/gateway/class-wc-gateway-rfq.php';
            $GLOBALS['WC_Gateway_RFQ'] = new WC_Gateway_RFQ();
            //	require_once 'includes/classes/gateway/wc-gateway-gpls-request-quote.php';
        }
    }


    /**
     * Plugin url.
     *
     * @return string
     */


    public static function plugin_url()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     * Plugin url.
     *
     * @return string
     */
    public static function plugin_abspath()
    {
        return trailingslashit(plugin_dir_path(__FILE__));
    }


    /**
     * Registers WooCommerce Blocks integration.
     *
     */
    public static function gplswoo_gateway_rfq_block_support()
    {
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            require_once 'includes/blocks/class-wc-gpls_rfq-payments-blocks.php';
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                    $payment_method_registry->register(new WC_Gateway_gpls_rfq_Blocks_Support());
                }
            );

        }
    }

    public function gpls_woo_rfq_get_text($translated, $text, $domain)
    {
        /*$update_rfq_cart_button = get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button','');


        if( is_cart() && $translated == 'Update cart' ){
            $translated = $update_rfq_cart_button;
        }

        return $translated;*/

    }


    public function gpls_woo_rfq_after_normal_checkout($order_id)
    {

        $wp_session = gpls_woo_get_session();

        $key = sanitize_key( 'rfq_checkout_files');

        unset($wp_session[$key]);

        $key = sanitize_key( 'gpls_woo_rfq_cart_notices');

        unset($wp_session[$key]);

        $key = sanitize_key( 'gpls_woo_rfq_cart_notices');

        unset($wp_session[$key]);

        $wp_session->write_data();

        gpls_woo_rfq_cart_delete( 'gpls_woo_rfq_cart_notices');
        gpls_woo_rfq_cart_delete( 'rfq_checkout_files');

    }


    public function gpls_woo_rfq_enqueue_admin_css()
    {
        //   WordPress.Security.NonceVerification.Recommended
        // phpcs:disable

        if (is_admin() && isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'settings_gpls_woo_rfq'

        ) {
            $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_admin.css';

            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_admin.css';
            wp_enqueue_style('gpls_woo_rfq_plus_css_admin', $url_css, array(), wp_rand(10, 100000));

        }
//   WordPress.Security.NonceVerification.Recommended
        if (is_admin() && isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'settings_gpls_woo_rfq'
            && !class_exists('GPLS_WOO_RFQ_PLUS')

        ) {
            //  $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_admin_free.css';

            //  $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_admin_free.css';
            //  wp_enqueue_style('gpls_woo_rfq_plus_css_admin_free', $url_css, array(), wp_rand(10, 100000));

        }

// phpcs:enable
    }


    public function check_for_rfq_checkout()
    {

        add_filter('woocommerce_payment_gateways', 'add_gpls_woo_rfq_class', 1, 1);

        require_once(gpls_woo_rfq_DIR . 'includes/classes/gateway/wc-gateway-gpls-request-quote.php');

        // add_filter('woocommerce_available_payment_gateways', 'gpls_rfq_remove_other_payment_gateways', 1000, 1);
        add_filter('woocommerce_payment_gateways', 'gpls_rfq_remove_other_block_payment_gateways', 1000, 1);


        gpls_woo_rfq_handle_checkout();


    }


    public function gpls_woo_rfq_settings()
    {


        if (is_admin()) {
            require_once(gpls_woo_rfq_DIR . 'includes/classes/admin/admin.php');
            $GPLS_Woo_RFQ_Admin = new GPLS_Woo_RFQ_Admin();
        }


    }


    public function gpls_woo_rfq_init()
    {

        //  $this->setup_email();
        $this->gpls_rfq_css_js();
        //gateway
        // $this->setup_gateway();

        $plugin = plugin_basename(__FILE__);

        add_filter("plugin_action_links_$plugin", array($this, 'gpls_woo_rfq_plugin_settings_link'));

        $this->gpls_woo_rfq_settings();


        //  register_activation_hook(__FILE__, array($this, 'gpls_woo_rfq_activation'));

        //     register_deactivation_hook(__FILE__, array($this, 'gpls_woo_rfq_deactivation'));


    }


    public function gpls_woo_rfq_load_textdomain()
    {
        load_plugin_textdomain('woo-rfq-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 100, 4);


    }

    public function plugin_row_meta($links_array, $plugin_file_name, $plugin_data, $statu)
    {
        /* if( strpos( $plugin_file_name, basename(__FILE__) ) )
         {
             $row_meta = array(
                 'Premium' =>  '<a target="_blank"  style="color:dodgerblue;" target="_blank" href="https://neahplugins.com/product/woocommerce-quote-request-plus/">' . __( 'Get Premium','woo-rfq-for-woocommerce' ) . '</a>',
                             );

             return array_merge( $row_meta,$links_array);
         }*/

        return (array)$links_array;
    }

    public function gpls_woo_rfq_plugin_settings_link($links)
    {


        $settings_link = admin_url('admin.php?page=wc-settings&tab=settings_gpls_woo_rfq&section=');
        $settings_link = '<a href="' . $settings_link . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }


    public function setup_email()
    {
        require_once(gpls_woo_rfq_DIR . 'includes/classes/emails/class-wc-email-rfq.php');
        $GLOBALS["wc_email_rfq"] = new WC_Email_RFQ();

    }

    public function setup_gateway()
    {
        require_once(gpls_woo_rfq_DIR . 'includes/classes/gateway/class-wc-gateway-rfq.php');
        $GLOBALS["wc_gateway_frq"] = new WC_Gateway_RFQ();

    }


    public function gpls_4woo_quote_wp_init()
    {


        do_action('before_gpls_4woo_quote_wp_init');

        // $this->gpls_rfq_setup_includes();
        // $this->setup_email();

        // $this->gpls_woo_rfq_init();

        add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2);
        add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_is_purchasable', 1000, 2);

        add_filter('woocommerce_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);
        add_filter('woocommerce_variation_is_purchasable', 'gpls_woo_rfq_normal_is_purchasable', 1000, 2);


        $status_label = __('Quote Request', 'woo-rfq-for-woocommerce');


        register_post_status('wc-gplsquote-req', array(
            'label' => $status_label,
            'public' => true,
            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
//  
            //'label_count' => _n_noop($status_label . '<span class="count">(%s)</span>', $status_label . ' <span class="count">(%s)</span>','woo-rfq-for-woocommerce')

        ));

        add_filter('wc_order_statuses', array($this, 'gpls_rfq_add_quote_request_to_order_statuses'), 1000);
        //  $this->setup_email();

        if (!is_admin()) {

            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq") {
                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no') {
                    $rfq_check = true;
                }
            }


            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "normal_checkout") {

                {
                    $normal_check = true;
                }
            }

            if (function_exists('is_user_logged_in')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes'
                    && !is_user_logged_in()) {
                    $rfq_check = true;
                    $normal_check = false;
                }
            }


            $staff = "no";

            if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_rfq_plus_check_staff_mode')) {
                $staff = gpls_woo_rfq_plus_check_staff_mode();
            }

            if (function_exists('gpls_woo_rfq_is_account_page')) {

                if ($rfq_check && !gpls_woo_rfq_is_account_page() && $staff === "no") {

                    add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_cart_custom_css', 1000);
                    add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_mode_remove_subtotals_custom_css', 1000);
                    add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_cart_custom_js', 1000);


                }
                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq"
                    && !gpls_woo_rfq_is_account_page() && $staff === "no") {
                    // add_action('wp_print_footer_scripts', 'gpls_woo_add_rfq_change_text_footer', 1000);
                    add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_change_text_footer_rfq', 1000);
                }

                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "normal_checkout"
                    && !gpls_woo_rfq_is_account_page() && $staff === "no") {
                    // add_action('wp_print_footer_scripts', 'gpls_woo_add_rfq_change_text_footer', 1000);
                    add_action('wp_enqueue_scripts', 'gpls_woo_add_rfq_change_text_footer_normal', 1000);
                }

            }
        }

        if (class_exists('gpls_woo_rfq_prices')) {
            $GLOBALS["gpls_woo_rfq_prices"] = new gpls_woo_rfq_prices();
        }

        if (is_admin()) {
            require_once(gpls_woo_rfq_DIR . 'includes/classes/admin/privacy/gpls_woo_rfq_privacy.php');
        }
    }


    // Add to list of WC Order statuses
    public function gpls_rfq_add_quote_request_to_order_statuses($order_statuses)
    {
//  

        $status_label = get_option('settings_gpls_woo_rfq_quote_request_label', 'Quote Request');
        $status_label = __($status_label, 'rfqtk');

        if ($status_label == '') {
            $status_label = __('Quote Request', 'woo-rfq-for-woocommerce');
        }

        $order_statuses["wc-gplsquote-req"] = $status_label;

      //  $order_statuses = array_reverse($order_statuses, True);


        return $order_statuses;

    }


    public function gpls_woo_rfq_activation()
    {


    }


    public function gpls_woo_rfq_wp_loaded()
    {
        // add_filter( 'gettext', array($this, 'gpls_woo_rfq_get_text'), 20, 3 );

    }

    public function gpls_woo_rfq_setup_menu()
    {


     //   $this->gpls_woo_upgrade_routines();

        gpls_woo_rfq_create_page();

         gpls_woo_rfq_check_menu();


    }

    function gpls_rfq_install()
    {


    }


    public function gpls_woo_rfq_deactivation()
    {


    }


    public function gpls_woo_rfq_uninstall()
    {


    }


    public function gpls_woo_rfq_pay_plugin_path()
    {


        return untrailingslashit(plugin_dir_path(__FILE__));

    }


    public function gpls_rfq_setup_includes()
    {





        require_once(gpls_woo_rfq_DIR . 'includes/classes/gpls_woo_rfq_functions.php');

        //   require_once(gpls_woo_rfq_DIR . 'includes/classes/prices/gpls_woo_rfq_prices.php');
        //    $GLOBALS["gpls_woo_rfq_prices"] = new gpls_woo_rfq_prices();

        require_once(gpls_woo_rfq_DIR . 'includes/classes/coupons/gpls_woo_rfq_coupons.php');
        $GLOBALS["gpls_woo_rfq_coupons"] = new gpls_woo_rfq_coupons();

        require_once(gpls_woo_rfq_DIR . 'includes/classes/shipping/gpls_woo_rfq_shipping.php');
        $GLOBALS["gpls_woo_rfq_shipping"] = new gpls_woo_rfq_shipping();

        require_once(gpls_woo_rfq_DIR . 'includes/classes/cart/gpls_woo_rfq_cart.php');
        $GLOBALS["gpls_woo_rfq_cart"] = new gpls_woo_rfq_CART();

        require_once(gpls_woo_rfq_DIR . 'includes/classes/checkout/gpls_woo_rfq_checkout.php');

        $GLOBALS["gpls_woo_rfq_checkout"] = new gpls_woo_rfq_CHECKOUT();

        require_once(gpls_woo_rfq_DIR . 'includes/classes/admin/gpls_woo_rfq_admin_functions.php');

        if (!is_admin()) {
            add_shortcode('gpls_woo_rfq_get_cart_sc', 'gpls_woo_rfq_get_rfq_cart');
        }
        if (!is_admin()) {
            add_shortcode('gpls_woo_rfq_get_favs_sc', 'gpls_woo_rfq_get_fav_cart');
        }

    }


    public function gpls_woo_rfq_enqueue_scripts()
    {

        if (!is_admin()) {


              $url_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_woo_rfq.css';
            $url_css_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_woo_rfq.css';
            wp_enqueue_style('gpls_woo_rfq_css', $url_css, array(), wp_rand(10, 100000));




            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq") {

                if ((get_option('gpls_woo_rfq_hide_price_cart_checkout', 'no') === 'yes')) {

                    $custom_css = "                    
                    
                    .site-header-cart .cart-contents .woocommerce-Price-amount{visibility: collapse;}                      
                    ";

                    wp_add_inline_style('gpls_woo_rfq_css', $custom_css);

                }

            }

            $list = 'enqueued';

            // if (!wp_script_is( 'gpls_woo_rfq.js', $list ))
            {
                $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_woo_rfq.js';
                $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_woo_rfq.js';
                wp_enqueue_script('gpls_woo_rfq_js', $url_js, array('jquery'), wp_rand(10, 100000), true);

            }




            $hide_visitor = false;

            $staff = "no";

            if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_rfq_plus_check_staff_mode')) {
                $staff = gpls_woo_rfq_plus_check_staff_mode();
            }

            if ($staff == "no" && function_exists('is_user_logged_in')) {
                if ((get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' &&
                    !is_user_logged_in())) {

                    $hide_visitor = true;

                }
            }


            if ($hide_visitor) {

                if (!is_admin()) {
                    $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh_visitor.css';
                    $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh_visitor.css';
                    wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                    $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh.css';
                    $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh.css';
                    wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                    if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                        $custom_extra_css = get_option('rfq_cart_hide_quote_extra_css', '');
                        if ($custom_extra_css != '') {
                            wp_add_inline_style('url_gpls_wh_css', $custom_extra_css);
                        }

                        if (get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote_cart', 'no') == 'yes' &&
                            function_exists('wp_get_current_user') && !wp_get_current_user()->exists()
                            && function_exists('gpls_woo_rfq_get_catalog_button_qty_css')) {

                            $css = gpls_woo_rfq_get_catalog_button_qty_css();

                            wp_add_inline_style('url_gpls_wh_css', $css);

                        }


                    }

                    $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                    $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                    wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);
                }
            }


            // if (1)
            {
               /* if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                    $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                    DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
                }

                $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

                $rfq_product_script = "";

                $hide_visitor = false;
                $rfq_check = false;
                $normal_check = false;
                //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
                $rfq_check = false;
                $normal_check = false;

                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq") {
                    add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);

                    $rfq_check = true;
                    $normal_check = false;

                    if (get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'yes') {
                        //   $rfq_check = false;
                        //   $normal_check = true;
                    }
                }

                if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "normal_checkout") {
                    $rfq_check = false;
                    $normal_check = true;
                }

                if (function_exists('is_user_logged_in')) {
                    if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' && !is_user_logged_in()) {
                        $rfq_check = true;
                        $normal_check = false;
                        $hide_visitor = true;

                    }
                }


                if ($staff == "no" && ($GLOBALS["gpls_woo_rfq_checkout_option"] === "rfq")) {

                    $hide = true;

                    if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_rfq_plus_check_staff_mode')) {
                        if (gpls_woo_rfq_plus_check_staff_mode() === "yes") {
                            $hide = false;
                        }
                    }

                    if ((get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'yes')) {
                        $hide = false;
                    }

                    global $wp_query;

                    if (isset($wp_query)) {
                        if (function_exists('gpls_woo_rfq_is_cart_page') && function_exists('gpls_woo_rfq_is_checkout_page')) {
                            if ($GLOBALS["gpls_woo_rfq_checkout_option"] === "rfq" && get_option('gpls_woo_rfq_hide_price_cart_checkout', 'no') == 'yes'
                                && (gpls_woo_rfq_is_cart_page() || gpls_woo_rfq_is_checkout_page())) {
                                $hide = true;
                            }
                        }
                    }

///////////////////////////////////////////  START GPLS_WOO_RFQ_PLUS
                    if (class_exists('GPLS_WOO_RFQ_PLUS')) {


                        // xxx();
                        if (function_exists('gpls_woo_rfq_plus_visibility_functions')) {
                            gpls_woo_rfq_plus_visibility_functions();

                        }

                    }


///////////////////////////////////////////  END GPLS_WOO_RFQ_PLUS

                    if (isset($wp_query)) {
                        if (function_exists('gpls_woo_rfq_is_account_page') && gpls_woo_rfq_is_account_page()) {
                            $hide = false;
                        }

                        if (is_view_order_page()) {

                            global $wp;

                            $order = wc_get_order($wp->query_vars['view-order']);

                            if ($order->get_status() == "gplsquote-req") {
                                $hide = true;
                            }

                            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                                $show_prices_in_my_account = get_option('settings_gpls_woo_rfq_show_prices_in_my_account', 'no');

                                if ($show_prices_in_my_account == 'yes' || gpls_woo_rfq_plus_check_staff_mode() == "yes") {
                                    $hide = false;
                                }
                            }


                        }
                    }


                    if ($hide == true) {

                        if (!is_admin()) {
                            $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh.css';
                            $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh.css';
                            wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                                $custom_extra_css = get_option('rfq_cart_hide_quote_extra_css', '');
                                if ($custom_extra_css != '') {
                                    wp_add_inline_style('url_gpls_wh_css', $custom_extra_css);
                                }
                                if (get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote_cart', 'no') == 'yes' &&
                                    function_exists('wp_get_current_user') && !wp_get_current_user()->exists()
                                    && function_exists('gpls_woo_rfq_get_catalog_button_qty_css')) {

                                    $css = gpls_woo_rfq_get_catalog_button_qty_css();
                                    wp_add_inline_style('url_gpls_wh_css', $css);

                                }
                            }


                            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                            wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);
                        }
                    }


                }


                if (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && ($GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout")) {

                    $home = home_url() . '/quote-request/';

                    $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);


                    // $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];

                   // $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];

                    if(isset($_SERVER['REQUEST_URI'])){
                        $actual_link = get_site_url() . sanitize_url( wp_unslash($_SERVER['REQUEST_URI']));

                    }

                    if ((strtolower(wp_parse_url(trim($rfq_page))['path'])) ===
                        (strtolower(wp_parse_url(trim($actual_link))['path']))) {

                        if (!is_admin()) {
                            $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh.css';
                            $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh.css';
                            wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                                $custom_extra_css = get_option('rfq_cart_hide_quote_extra_css', '');
                                if ($custom_extra_css != '') {
                                    wp_add_inline_style('url_gpls_wh_css', $custom_extra_css);
                                }
                                $gpls_woo_rfq_url_wh = plugin_dir_url(WP_PLUGIN_DIR . '/rfqtk/rfqtk.php');
                                $gpls_woo_rfq_url_wh_css = $gpls_woo_rfq_url_wh . 'assets/css/gpls_wh_rfqtk.css';
                                wp_enqueue_style('url_gpls_wh_css2', $gpls_woo_rfq_url_wh_css, array(), wp_rand(10, 10000));

                                if (get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote_cart', 'no') == 'yes' &&
                                    function_exists('wp_get_current_user') && !wp_get_current_user()->exists()
                                    && function_exists('gpls_woo_rfq_get_catalog_button_qty_css')) {

                                    $css = gpls_woo_rfq_get_catalog_button_qty_css();
                                    wp_add_inline_style('url_gpls_wh_css', $css);

                                }
                            }

                            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                            wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);



                        }
                    }


                }


            }

            $is_product_page = false;


            global $wp;
          //  $url = get_site_url() . $_SERVER['REQUEST_URI'];

            if(isset($_SERVER['REQUEST_URI'])){
                $url = get_site_url() . sanitize_url( wp_unslash($_SERVER['REQUEST_URI']));
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

                if (!is_object($product) && !function_exists('wc_get_product')) return;

                if (!is_object($product)) $product = wc_get_product(get_the_ID());


                if (!isset($product) || !is_object($product)) {
                    return;
                }

                if ($product->get_type() == 'external') {
                    return;
                }


                $rfq_enable = gpls_woo_get_rfq_enable($product);

               /* if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                    $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                    $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option,'woo-rfq-for-woocommerce');
                    DEFINE('gpls_woo_rfq_INQUIRE_TEXT', $settings_gpls_woo_inquire_text_option);
                }
                $form_label = gpls_woo_rfq_INQUIRE_TEXT;*/

                $rfq_product_script = "";


                $hide_visitor = false;
                $rfq_check = false;
                $normal_check = false;

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
                        $hide_visitor = true;

                    }
                }

                if ($staff == "no" && ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) {


                    $hide = false;

                    $hide_price = get_post_meta($product->get_id(), '_gpls_woo_rfq_hide_price', true);

                    $hide_price = gpls_woo_rfq_get_hide_price($product);


                    $hide_all = get_option('settings_gpls_woo_rfq_limit_to_rfq_only_hide_prices', 'no');
                    if ($rfq_enable == "no") {
                        $hide_all = "no";
                    }

                    if ($hide_price == 'yes' || ($hide_all == 'yes' && $rfq_enable == 'yes')) {

                        $hide = true;
                    }

                    if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_rfq_plus_check_staff_mode')) {
                        if (gpls_woo_rfq_plus_check_staff_mode() == "yes") {
                            $hide = false;
                        }
                    }


                    if ((get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'no') || $hide == true) {

                        if (!is_admin()) {
                            $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh.css';
                            $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh.css';
                            wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                                $custom_extra_css = get_option('rfq_cart_hide_quote_extra_css', '');
                                if ($custom_extra_css != '') {
                                    wp_add_inline_style('url_gpls_wh_css', $custom_extra_css);
                                }

                                if (get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote_cart', 'no') == 'yes' &&
                                    function_exists('wp_get_current_user') && !wp_get_current_user()->exists()
                                    && function_exists('gpls_woo_rfq_get_catalog_button_qty_css')) {

                                    $css = gpls_woo_rfq_get_catalog_button_qty_css();
                                    wp_add_inline_style('url_gpls_wh_css', $css);

                                }
                            }

                            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                            wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);
                        }
                    }


                }

                if (($rfq_enable == 'yes' && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq")) {


                    if (($normal_check && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no')) {

                        if (!is_admin()) {
                            $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh.css';
                            $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh.css';
                            wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                                $custom_extra_css = get_option('rfq_cart_hide_quote_extra_css', '');
                                if ($custom_extra_css != '') {
                                    wp_add_inline_style('url_gpls_wh_css', $custom_extra_css);
                                }
                                if (get_option('settings_gpls_woo_rfq_hide_visitor_add_to_quote_cart', 'no') == 'yes' &&
                                    function_exists('wp_get_current_user') && !wp_get_current_user()->exists()
                                    && function_exists('gpls_woo_rfq_get_catalog_button_qty_css')) {

                                    $css = gpls_woo_rfq_get_catalog_button_qty_css();
                                    wp_add_inline_style('url_gpls_wh_css', $css);

                                }
                            }

                            $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                            $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                            wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);
                        }
                    }
                }


            }

            if (function_exists('is_plugin_active')) {
                if (is_plugin_active('rfqtk/rfqtk.php')) {

                    $has_string = strpos($url, 'order-received');

                  if (
                      // phpcs:disable

                          $has_string !== false && (isset($_REQUEST['key']))//  WordPress.Security.NonceVerification.Recommended
                        && strpos(sanitize_key(wp_unslash($_REQUEST['key'])), 'wc_order_', 0) === 0)
                      // phpcs:enable
                    {

                        //   WordPress.Security.NonceVerification.Recommended
                        if ((gpls_woo_rfq_is_checkout_page())
                            && (isset($GLOBALS["gpls_woo_rfq_checkout_option"]) && $GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) {

                            // if ((get_option('settings_gpls_woo_rfq_show_prices', 'no') != 'yes'))
                            {
                                $hide_all = get_option('settings_gpls_woo_rfq_limit_to_rfq_only_hide_prices', 'no');

                                // if (($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq"))
                                {

                                    if ($hide_all == 'yes') {
                                        $order_id = absint(get_query_var('order-received'));

                                        $order = wc_get_order($order_id);
                                        foreach ($order->get_items() as $item) {

                                            $product_id = $item->get_product_id();
                                            $product = wc_get_product($product_id);


                                            $rfq_enable = gpls_woo_get_rfq_enable($product);


                                            if ($rfq_enable == 'yes') {

                                                add_filter('woocommerce_order_formatted_line_subtotal', 'gpls_woo_rfq_order_formatted_line_subtotal', 100, 3);
                                                add_filter('woocommerce_get_formatted_order_total', 'gpls_woo_rfq_get_formatted_order_total', 100, 2);
                                                add_filter('woocommerce_get_order_item_totals', 'gpls_woo_rfq_woocommerce_get_order_item_totals', 100, 2);


                                                $GLOBALS["gpls_woo_rfq_show_prices"] = "no";

                                                $url_gpls_wh_css = gpls_woo_rfq_URL . 'gpls_assets/css/gpls_wh_visitor.css';
                                                $url_gpls_wh_path = gpls_woo_rfq_DIR . 'gpls_assets/css/gpls_wh_visitor.css';
                                                wp_enqueue_style('url_gpls_wh_css', $url_gpls_wh_css, array(), wp_rand(10, 100000));

                                                $url_js = gpls_woo_rfq_URL . 'gpls_assets/js/gpls_wh.js';
                                                $url_js_path = gpls_woo_rfq_DIR . 'gpls_assets/js/gpls_wh.js';
                                                wp_enqueue_script('url_gpls_wh_js', $url_js, array('jquery'), wp_rand(10, 100000), true);
                                                break;
                                            }

                                        }
                                    }
                                }
                            }
                        }


                    }
                }
            }

        }
    }


    public function gpls_woo_rfq_enqueue_footer_scripts()
    {

        //do_action('astra_woo_quick_view_product_summary', 'quick-view');


        if (!is_admin()) {

            $rfq_product_script = "jQuery(window ).on('load', function() { jQuery('form.checkout').removeAttr( 'novalidate');
    jQuery('.required').attr('required',true); } );  
    ";


            echo "<div class='gpls_script' style='display: none'><script> " .
                wp_kses_post($rfq_product_script). '</script></div>';


            $rfq_product_script2 = "
jQuery(document).ajaxComplete(function (event, xhr, options) {
     jQuery( '.woocommerce #ast-quick-view-modal div.product form.cart .button.single_add_to_cart_button' ).hide();
                jQuery( '.woocommerce #ast-quick-view-modal div.product form.cart .button.gpls_rfq_set' ).show();
});";


            echo "<div class='gpls_script' style='display: none'><script> " .
                wp_kses_post($rfq_product_script). '</script></div>';


            if (function_exists('is_wc_endpoint_url')) {
                if ((is_wc_endpoint_url('order-received')) && ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq")) {

                    if (
                        (get_option('settings_gpls_woo_rfq_show_prices', 'no') != 'yes')
                        ||
                        (class_exists('GPLS_WOO_RFQ_PLUS') && get_option('gpls_woo_rfq_hide_price_cart_checkout', 'no') == 'yes')
                    ) {

                        $rfq_product_script3 = "jQuery(document ).ready( function() {
jQuery( '.wc-item-meta-label' ).hide();
jQuery( '.wc-item-meta-label' ).attr('style','visibility: collapse');
} ); ";

                        echo "<div class='gpls_script' style='display: none'><script> " .
                            wp_kses_post($rfq_product_script). '</script></div>';

                    }

                }
            }

        }

    }


    public function gpls_rfq_css_js()
    {
        if (!is_admin()) {
            //  add_action('wp_enqueue_scripts', array($this, 'gpls_woo_rfq_enqueue_jquery_scripts'),1000);
            add_action('wp_enqueue_scripts', array($this, 'gpls_woo_rfq_enqueue_scripts'), 2000);
            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_enqueue_scripts'), 2000);
            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_enqueue_footer_scripts'), 2000);


        }


    }


    public function gpls_rfq_setup_constants()
    {

        if (!defined('gpls_woo_rfq_DIR')) {


            DEFINE('gpls_woo_rfq_DIR', plugin_dir_path(__FILE__));
            DEFINE('gpls_woo_rfq_URL', plugin_dir_url(__FILE__));
            DEFINE('gpls_woo_rfq_FILE_NAME', (__FILE__));
            DEFINE('gpls_woo_rfq_PLUGIN_PATH', untrailingslashit(plugin_basename(__FILE__)));
            DEFINE('gpls_woo_rfq_TEMPLATE_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/');
            DEFINE('gpls_woo_rfq_WOO_PATH', untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/');
            DEFINE('gpls_woo_rfq_GLOBAL_NINJA_FORMID', get_option('settings_gpls_woo_ninja_form_option'));

            //  

           /* if(!defined('gpls_woo_rfq_INQUIRE_TEXT')) {
                $settings_gpls_woo_inquire_text_option = get_option('settings_gpls_woo_inquire_text_option');
                $settings_gpls_woo_inquire_text_option = __($settings_gpls_woo_inquire_text_option,'woo-rfq-for-woocommerce');
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
            && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'yes'
        ) {
            $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
        }

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
            && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'

        ) {
            $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
        }

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
            && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no'
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
    }

    public function gpls_woo_rfq_no_price_enqueue_scripts()
    {


    }


    function footer_action()
    {


    }

    public function remove_rfq_request_menu()
    {

        $home = home_url() . '/quote-request/';

        //$rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);

        $link_to_rfq_page_url = $_SERVER['REQUEST_URI'];

        if ($link_to_rfq_page_url != false && isset($link_to_rfq_page_url)) {

            $link_to_rfq_page_path = $link_to_rfq_page_url;

        } else {
            return;
        }

        $menu_name = 'primary';

        $locations = get_nav_menu_locations();

        $menu_id = $locations[$menu_name];

        $mymenu = wp_get_nav_menu_object($menu_id);

        $menuID = (int)$mymenu->term_id;

        $menu_items = wp_get_nav_menu_items($menuID);

        foreach ($menu_items as $menu_item => $item) {

            if (isset($item->url)) {

                $url = trim($item->url);


                //$url = trim(preg_replace('"' . $_SERVER['HTTP_HOST'] . '"', '', $url));
                // $url = trim(preg_replace('#^https?://#', '', $url));

                $url = trim(preg_replace('"' . get_site_url() . '"', '', $url));

                if ($url != false && is_array($url) && isset($url['path'])) {
                    if ($url == trim($link_to_rfq_page_path)) {

                    }
                }
            }
        }


    }

    public function gpls_woo_rfq_setup_custom_css()
    {

    }

    public function gpls_woo_rfq_setup_customer_session()
    {

    }

    public function gpls_woo_rfq_setup_customer_cookie()
    {
        if( !is_admin() && !function_exists('gpls_woo_get_session') ) {



            require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
            require_once(ABSPATH . 'wp-includes/class-phpass.php');
            add_filter('_rfqtk_wp_session_expiration_variant', RFQTK_WP_SESSION_EXPIRATION);
            add_filter('_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION_VARIANT);
        }

    }

    public function gpls_woo_rfq_end_session()
    {
        session_destroy();
    }

    public function gpls_woo_rfq_set_expiration_variant_time($exp)
    {
        return RFQTK_WP_SESSION_EXPIRATION_VARIANT;
    }


    public function gpls_woo_rfq_set_expiration_time($exp)
    {
        return RFQTK_WP_SESSION_EXPIRATION;
    }


    public function gpls_woo_rfq_logout()
    {

        //gpls_woo_rfq_cart_delete( 'gpls_woo_rfq_cart');

    }

    /**
     * @param $wpdb
     * @return mixed
     */
    public function gpls_woo_upgrade_routines()
    {
        global $wpdb;

        delete_option('settings_gpls_woo_rfq_sessions_table_fixed_rfq1');
        delete_option('settings_gpls_woo_rfq_sessions_notable_fix21z');
        delete_option('settings_gpls_woo_rfq_sessions_notable_fix21487m2we');
        delete_option('settings_gpls_woo_fix21487m23');


        $table_fixed = get_option('settings_gpls_notable_fix22w54e', 'no');

        if ($table_fixed == 'no') {

            GPLS_WOO_RFQ_activation();
            update_option('settings_gpls_notable_fix22w54e', 'yes');

        }
    }


}

GPLS_WOO_RFQ::init();

register_activation_hook(__FILE__, 'GPLS_WOO_RFQ_activation');

function GPLS_WOO_RFQ_activation()
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    global $wpdb;
    $wpdb_collate = $wpdb->collate;
    $charset = $wpdb->charset;


    $str = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}npxyz2021_sessions (
     `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
     `option_name` varchar(191)  NOT NULL,
     `option_value` longtext DEFAULT NULL,
     `expiration` bigint(20) NOT NULL,
     `misc_value` longtext DEFAULT NULL,
     `created` timestamp NOT NULL DEFAULT current_timestamp(),
     `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
     PRIMARY KEY (`option_id`),
     UNIQUE KEY `option_name` (`option_name`)
   )  CHARSET={$charset} COLLATE={$wpdb_collate};";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($str);

    $str = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}npxyzfavs (
      `fav_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `user_first` varchar(50) DEFAULT NULL,
  `user_last` varchar(50) DEFAULT NULL,
  `user_email` varchar(254) NOT NULL,
  `product_url` varchar(500) DEFAULT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `misc_value` varchar(1000) DEFAULT NULL,
  `misc_value2` varchar(1000) DEFAULT NULL,
  `misc_value3` varchar(1000) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`fav_id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
   )  CHARSET={$charset} COLLATE={$wpdb_collate};";
    dbDelta($str);

}

$table_fixed = get_option('settings_gpls_fix_faves', 'no');

if ($table_fixed == 'no')
{
    GPLS_WOO_RFQ_activation();
    update_option('settings_gpls_fix_faves', 'yes');
}


//$title = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
//$order = $order_factory->get_order(sanitize_text_field(($_REQUEST['order_id'])));
//$string = sprintf( __('You have %d tacos', 'plugin-domain'), $number );
//
// __( 'A string to be translated', 'text domain' );
//
//                        /* translators: %1$s is replaced with "string" */
//                        __( 'A %1$s to be translated', 'text domain' );
//                        Strings can be checked for placeholders using the following regex: %(\d+\$(?:\d+)?)?[bcdefgosuxEFGX].
