<?php




$needs_payment=get_option('settings_gpls_woo_rfq_no_payment_checkout','no');

if($needs_payment == "yes") {

    add_action('woocommerce_order_needs_payment', 'gplswoo_woocommerce_order_needs_payment', 10, 3);
    function gplswoo_woocommerce_order_needs_payment($status, $order, $valid_order_statuses){


        return false;
    }


}

{
    if ( !is_admin()&&get_option('settings_gpls_woo_rfq_no_payment_checkout', 'no') == 'yes')
    {

         //  $GLOBALS["gpls_woo_rfq_show_prices"] = "no";
        //   $GLOBALS["hide_for_visitor"] = "no";


        if (!function_exists('gpls_woo_rfq_needs_payment')){
            function gpls_woo_rfq_needs_payment($needs_payment, $cart) {
                return false;
            }
        }

        if (!function_exists('woocommerce_payment_complete_status')){
            function woocommerce_payment_complete_status($status, $id, $order) {


                return 'pending';
            }
        }


          if (!function_exists('gpls_pre_payment_complete')){
              function gpls_pre_payment_complete( $id, $trx_id) {

$order=wc_get_order($id);
$order->set_status('wc-pending');
$order->save();

              }
          }

        if (!function_exists('gpls_woo_rfq_check')) {
            function gpls_woo_rfq_check()
            {
                add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_needs_payment', 1000, 2);


                add_filter( 'woocommerce_payment_complete_order_status','woocommerce_payment_complete_status',1000,3);
//apply_filters( 'woocommerce_payment_complete_order_status', $this->needs_processing() ? 'processing' : 'completed', $this->get_id(), $this )

              //  add_action( 'woocommerce_pre_payment_complete','gpls_pre_payment_complete', 100,2 );
            }
        }

        add_action('wp_loaded', 'gpls_woo_rfq_check');
        add_action('wp', 'gpls_woo_rfq_check');
        add_action('init', 'gpls_woo_rfq_check');
        //return;
    }

}