<?php

// phpcs:ignoreFile
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$view_favs = get_option('settings_rfq_favs_wordings_view_favs_cart', __('View Favorites', 'woo-rfq-for-woocommerce'));
$view_favs = __($view_favs, 'woo-rfq-for-woocommerce');

$favorites = get_option('rfq_cart_sc_section_link_to_favorites_page', home_url().'/favorites/');
$favorites = __($favorites, 'woo-rfq-for-woocommerce');
$link_to_fav_page=strtolower($favorites);

echo "<div style='display: block'><a  class='link_to_fav_page_link' href='".$link_to_fav_page."' >".$view_favs."</a></div>";

?>