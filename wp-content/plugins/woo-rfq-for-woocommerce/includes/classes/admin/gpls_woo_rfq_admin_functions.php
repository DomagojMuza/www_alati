<?php

/**
 * Main class
 *
 */
if (!class_exists('gpls_woo_rfq_admin_functions')) {

    class gpls_woo_rfq_admin_functions
    {
        public  function __construct() {

        }

        public function gpls_woo_rfq_product_ninja_get_formid( $product,$formid ){

            $gpls_woo_rfq_admin_product_form =  get_post_meta($product->get_id(), 'gpls_woo_rfq_ninja_product_form', true);;


            if( isset( $gpls_woo_rfq_admin_product_form ) ) {

                return $gpls_woo_rfq_admin_product_form;
            }

            return $formid;

        }

        public function gpls_woo_rfq_product_ninja_get_form_label( $product,$label ){

            $gpls_woo_rfq_ninja_product_form_label =  get_post_meta($product->get_id(), 'gpls_woo_rfq_ninja_product_form_label', true);;


            if( isset( $gpls_woo_rfq_ninja_product_form_label ) ) {

                return $gpls_woo_rfq_ninja_product_form_label;
            }

            return $label;

        }



    }

}



/*function gpls_woo_rfq_get_pages(){


    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => -1,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'

    );

    $pages = get_pages($args);

    $option=array();

    $first_option = 'Quote Request';

    $home = home_url() . '/quote-request/';

    $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);




    if(isset($_SERVER['REQUEST_URI'])){
        $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];
    }else{
        $actual_link = get_site_url() ;
    }

    $home = preg_replace("/^http:/i", "https:", $actual_link);

    $option[$home]=$first_option;

    foreach( $pages as $pagg ) {
        $label = $pagg->post_title;
        if ( strlen( $label ) > 30 )
            $label = substr( $label, 0, 30 ) . '...';

        if ( trim($label)=='')continue;


        $option[get_page_link($pagg->ID)]=$label ;
    }

    return $option;

}*/

