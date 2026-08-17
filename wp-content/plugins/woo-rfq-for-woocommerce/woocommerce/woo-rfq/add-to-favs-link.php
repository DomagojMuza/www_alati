<?php

// phpcs:ignoreFile
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$add_to_favs = get_option('settings_gpls_woo_rfq_add_to_favorites_label',__('Add to Favorites','woo-rfq-for-woocommerce'));
$add_to_favs = __($add_to_favs,'woo-rfq-for-woocommerce');

echo "<div style='display: block'><button  class='link_to_favs_page'  >".$add_to_favs."</button></div>";

?>