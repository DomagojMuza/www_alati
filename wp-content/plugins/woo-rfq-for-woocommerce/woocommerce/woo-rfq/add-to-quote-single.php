<?php
// phpcs:ignoreFile

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (($in_rfq == true) && isset($link_to_rfq_page)) {

    $view_rfq_cart_button = "<a class='rfqcart-link-single' style='text-align: center' href='" . $link_to_rfq_page . "'>" . $view_your_cart_text . "</a>";

    $view_rfq_cart_button = apply_filters('gpls_woo_rfq_view_rfq_cart_button', $view_rfq_cart_button, $in_rfq, $rfq_check, $normal_check, $rfq_enable, $product);

    echo ($view_rfq_cart_button);

} else {
    $view_rfq_cart_button = "<a class='rfqcart-link' style='display:none !important;text-align: center' href='" . $link_to_rfq_page . "'>" . $view_your_cart_text . "</a>";

    $view_rfq_cart_button = apply_filters('gpls_woo_rfq_view_rfq_cart_button', $view_rfq_cart_button, $in_rfq, $rfq_check, $normal_check, $rfq_enable, $product);

    echo ($view_rfq_cart_button);
}

echo '<div style="clear:both;margin-bottom:10px"></div>';

$single_add_to_cart_button = "
                <button type='submit' name='add-to-cart'                   
                  onmouseover=" . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'] . ';' . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover'] .
    " onmouseout=" . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] . ';' . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] .
    " onload=" . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] . ';' . $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] .
    " class='single_add_to_cart_button button alt  gpls_rfq_set gpls_rfq_css' 
              value='" . $product->get_id() . "'>" . esc_html($request_quote) . "</button>
                <input type='hidden' value='-1' name='rfq_product_id' id='rfq_product_id'/>
                <input type='hidden'  name='rfq_single_product' id='rfq_product_id'/>";
wp_nonce_field('rfq_id_nonce');


$single_add_to_cart_button = apply_filters('gpls_woo_rfq_single_add_to_cart_button',
    $single_add_to_cart_button, $in_rfq, $rfq_check, $normal_check, $rfq_enable, $product);

echo ($single_add_to_cart_button);// phpcs:ignore WordPress.Security.EscapeOutput
echo "<div class='gpls_script' style='display: none'><script> " .
    $rfq_product_script . '</script></div>';


?>



