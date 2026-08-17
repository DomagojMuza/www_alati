<?php
/**
 * Admin Classes
 **/
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;
class GPLS_Woo_RFQ_Admin
{

    public  function __construct() {

        add_filter( 'woocommerce_get_settings_pages', array($this,'gpls_woo_rfq_add_settings_page' ));


        require_once(gpls_woo_rfq_DIR . 'includes/classes/admin/metaboxes/gpls_woo_rfq_product_meta.php');
        gpls_woo_rfq_product_meta::init();

        add_filter( 'wc_order_is_editable',  array($this,'gpls_woo_rfq_order_status_manager_order_is_editable'), 10, 2 );

    }

    public function gpls_woo_rfq_add_settings_page( $settings ) {

        if(!is_array($settings)){
            return $settings;
        }
        $settings[] = include(gpls_woo_rfq_DIR . 'includes/classes/admin/settings/gpls_woo_rfq_settings.php');;

        return $settings;
    }

    public function gpls_woo_rfq_order_status_manager_order_is_editable( $editable, $order ) {

        if(is_object($order)==false || $order == null || $order == false ){
            return $editable;
        }

        $editable_custom_statuses = array( 'gplsquote-req','gplsquote-sent');

        if ( in_array( $order->get_status(), $editable_custom_statuses ) ) {
            $editable = true;
        }

        return $editable;
    }



}
 



?>