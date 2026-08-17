<?php

/**
 * Main class
 *
 */
if (!class_exists('gpls_woo_rfq_product_meta')) {

    class gpls_woo_rfq_product_meta
    {



        public static function init()
        {
            add_action( 'woocommerce_product_options_advanced', array(__CLASS__, 'gpls_woo_rfq_add_custom_general_fields' ),11,0);
            add_action( 'woocommerce_process_product_meta', array(__CLASS__,'gpls_woo_rfq_add_custom_general_fields_save' ),11,1);

        }



        public static function gpls_woo_rfq_add_custom_general_fields() {

            global $woocommerce, $post;
// Text Field

            ?>
            <h2><b>RFQ-ToolKit</b></h2>
            <hr class="product_meta_hr">
<?php

            woocommerce_wp_checkbox(
                array(
                    'id' => '_gpls_woo_rfq_rfq_enable',

                    'label' => __( 'Enable RFQ for this product.', 'woo-rfq-for-woocommerce' ),
                    'placeholder' => 'Enable RFQ for this product',
                    'desc_tip' => 'true',

                    'description' => __( "Enable quote requests for this product.", 'woo-rfq-for-woocommerce' )

                ));

        }

        public static function gpls_woo_rfq_add_custom_general_fields_save( $post_id ){


            if(isset($_REQUEST['woocommerce_meta_nonce'])
            && wp_verify_nonce(sanitize_key(wp_unslash($_REQUEST['woocommerce_meta_nonce'])), 'woocommerce_save_data')) {

                if ( isset($_POST['_gpls_woo_rfq_rfq_enable'])) {




                    update_post_meta($post_id, '_gpls_woo_rfq_rfq_enable', 'yes');
                    $product = wc_get_product($post_id);

                    $type_array = array('variable-subscription', 'variable');
                    if (in_array($product->get_type(), $type_array)) {
                        $product_children = $product->get_available_variations();

                        if (count($product_children) > 0) {
                            foreach ($product_children as $key => $val) {

                                update_post_meta($val["variation_id"], '_gpls_woo_rfq_rfq_enable', 'yes');
                            }
                        }

                    }


                } else {


                    update_post_meta($post_id, '_gpls_woo_rfq_rfq_enable', 'no');

                    $product = wc_get_product($post_id);

                    $type_array = array('variable-subscription', 'variable');

                    if (in_array($product->get_type(), $type_array)) {
                        $product_children = $product->get_available_variations();

                        if (count($product_children) > 0) {
                            foreach ($product_children as $key => $val) {

                                update_post_meta($val["variation_id"], '_gpls_woo_rfq_rfq_enable', 'no');

                            }
                        }

                    }


                }
            }
        }
    }

}
