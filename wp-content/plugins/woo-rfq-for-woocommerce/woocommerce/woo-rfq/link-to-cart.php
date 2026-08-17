<?php

// phpcs:ignoreFile
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));
$view_your_cart_text = __($view_your_cart_text, 'woo-rfq-for-woocommerce');

echo "<div style='display: block'><a  class='link_to_rfq_page_link' href='".($link_to_rfq_page)."' >".($view_your_cart_text)."</a></div>";

?>