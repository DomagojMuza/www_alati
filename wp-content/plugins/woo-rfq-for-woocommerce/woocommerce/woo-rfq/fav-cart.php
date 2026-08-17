<?php
/**
 * WOO-RFQ-List
 */
// phpcs:ignoreFile
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<noscript>
    <H1> Javascript is required for this page. Please enable JavaScript to continue.</h1>
</noscript>

<?php
//require_once(ABSPATH . 'wp-settings.php');

//$gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');
require_once(gpls_woo_rfq_DIR . 'wp-session-manager/wp-session-manager.php');
require_once(ABSPATH . 'wp-includes/class-phpass.php');

if (is_user_logged_in()) {

    $perm_values = np_get_fav_rec(get_current_user_id());

    $gpls_woo_fav_array = array();

    foreach ($perm_values as $key => $value) {

        $new_arr = array('date_added'=>$value['created']);

        $gpls_woo_fav_array[$value['product_id']] = $new_arr;
    }
} else {
    $gpls_woo_fav_array = gpls_woo_rfq_get_item('gpls_woo_fav_array');
}


if (!$gpls_woo_fav_array) {

    ob_start();

    wc_get_template('woo-rfq/rfq-cart-favs-empty.php',
        array('confirmation_message' => ''),
        '', gpls_woo_rfq_WOO_PATH);

    echo ob_get_clean();
    exit;
}

?>

<div id="rfq_cart_wrapper" class="rfq_cart_wrapper">

    <?php do_action('gpls_woo_rfq_cart_actions_upload_files'); ?>

    <?php
    $favorites = get_option('rfq_cart_sc_section_link_to_favorites_page', home_url() . '/favorites/');
    $favorites = __($favorites, 'woo-rfq-for-woocommerce');
    $link_to_fav_page = strtolower($favorites);
    $wc_get_update_url = $link_to_fav_page;
    ?>

    <?php


    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_styles'] = '';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseover'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseover'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseout'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseout'] = '';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_styles'] = '';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseover'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseover'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseout'] = '';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseout'] = '';

    $gpls_woo_rfq_styles = apply_filters('gpls_woo_rfq_before_cart_gpls_woo_rfq_styles', $gpls_woo_rfq_styles);

    ?>

    <div class="woo_rfq_top_html_desc">

        <?php //do_action('gpls_woo_rfq_fav_top_html_desc') ;
        ?>

    </div>


    <?php do_action('gpls_woo_rfq_before_fav_cart'); ?>

    <form name="rfqfavform" id="rfqform" class="rfqform" action="<?php echo($wc_get_update_url); ?>"
          method="post" enctype="multipart/form-data">


        <?php if (isset($global_product_id)): ?>
            <input type="hidden" name="global_product_id" id="global_product_id"
                   value="<?php echo($global_product_id); ?>"/>
        <?php endif; ?>

        <?php $nonce = wp_create_nonce('gpls_woo_rfq_handle_rfq_fav_cart_nonce'); ?>
        <div class="woocommerce gpls_woo_rfq_request_page">

            <div class="woocommerce gpls_woo_rfq_request_cart">
                <div style="clear: both"></div>
                <table id="rfq_cart_shop_table"
                       class="shop_table shop_table_responsive cart rfq_cart_shop_table" cellspacing="0">

                    <tr class="cart_tr">
                        <th class="product-check cart_th">&nbsp;</th>
                        <th class="product-remove cart_th">&nbsp;</th>
                        <th class="product-thumbnail cart_th">&nbsp;</th>
                        <th class="product-name cart_th"><?php printf((__('Product', 'woo-rfq-for-woocommerce'))); ?></th>
                        <th class="product-price cart_th"><?php printf((__('Price', 'woo-rfq-for-woocommerce'))); ?></th>
                        <th class="fav-date cart_th"><?php printf((__('Date', 'woo-rfq-for-woocommerce'))); ?></th>

                    </tr>

                    <?php do_action('gpls_woo_rfq_before_fav_cart_contents'); ?>

                    <?php


                    foreach ($gpls_woo_fav_array as $cart_item_key => $cart_item) {

                        $product_id = $cart_item_key;

                        $_product = wc_get_product($product_id);

                        // $_product   = $cart_item['data'];
                        //$product_id = $cart_item['data']['id'];
                        do_action('gpls_before_fav_row',$product_id);

                        if ($_product && $_product->exists()) {
                            $product_permalink = $_product->get_permalink();
                            $checked = false;
                            $checked = apply_filters('gpls_fav_chb_filter',$checked, $product_id);
                            $visible = 'gpls_hidden';
                            $visible = apply_filters('gpls_fav_chb_visible', $visible, $product_id);

                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                <td class="product-check cart_td ">
                                    <input type="checkbox" class="<?php echo $visible ?>"
                                           name="checkbox_<?php echo $product_id ?>" id="checkbox_<?php echo $product_id ?>" <?php echo $checked ?> >
                                    <?php

                                    do_action('gpls_woo_rfq_fav_check_box_action', $cart_item, $cart_item_key);

                                    ?>
                                </td>

                                <td class="product-remove cart_td ">
                                    <?php

                                        $url = esc_url($wc_get_update_url) . "?remove_fav_item=" . $product_id;
                                        echo(apply_filters('woocommerce_cart_item_remove_link', sprintf(
                                            '<a href="%s" type="submit" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                            $url . '&man-deleted=' . $product_id . "&gpls_woo_fav_nonce=" . $nonce,
                                            (__('Remove this item', 'woo-rfq-for-woocommerce')),
                                            esc_attr($product_id),
                                            esc_attr($_product->get_sku())
                                        ), ($cart_item_key)));

                                    ?>

                                </td>

                                <td class="product-thumbnail cart_td">
                                    <?php

                                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);


                                    if (!$product_permalink) {
                                        echo($thumbnail);
                                    } else {
                                        printf('<a href="%s">%s</a>', esc_url($product_permalink), ($thumbnail));
                                    }
                                    ?>
                                </td>

                                <td class="product-name  cart_td"
                                    data-title="<?php printf((__('Product', 'woo-rfq-for-woocommerce'))); ?>">
                                    <?php
                                    if (!$product_permalink) {
                                        echo (apply_filters('woocommerce_cart_item_name', $_product->get_title(),
                                                $cart_item, $cart_item_key)) . '&nbsp;';
                                    } else {
                                        echo(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>',
                                            esc_url($product_permalink), $_product->get_title()), $cart_item, $cart_item_key));
                                    }
                                    //  do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                                    // Meta data

                                    do_action('gplsrfq_cart_item_product', $_product, $cart_item, $cart_item_key);

                                    do_action('gpls_woo_rfq_get_product_extra', $_product, $cart_item, $cart_item_key);

                                    // Backorder notification
                                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                        echo '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woo-rfq-for-woocommerce') . '</p>';
                                    }
                                    ?>
                                </td>


                                <td class="product-price  cart_td"
                                    data-title="<?php echo __('Price', 'woo-rfq-for-woocommerce'); ?>">
                                    <?php

                                    $hide = false;

                                    if (($GLOBALS["gpls_woo_rfq_checkout_option"] === "rfq")) {


                                        if (class_exists('GPLS_WOO_RFQ_PLUS') && function_exists('gpls_woo_rfq_plus_check_staff_mode')) {
                                            if (gpls_woo_rfq_plus_check_staff_mode() === "yes") {
                                                $hide = false;
                                            }
                                        }

                                        if ((get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'yes')) {
                                            $hide = false;
                                        }
                                    }
                                    if (($GLOBALS["gpls_woo_rfq_checkout_option"] === "normal_checkout")) {

                                        if (gpls_woo_get_rfq_enable($_product) == 'yes') {
                                            //  $_product=wc_get_product();
                                            $hide = true;
                                        }
                                        if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes') {
                                            $hide = false;
                                        }
                                        //settings_gpls_woo_rfq_normal_checkout_show_prices
                                    }

                                    if (function_exists('is_user_logged_in')) {
                                        if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' && !is_user_logged_in()) {
                                            $hide = true;

                                        }
                                    }


                                    if ($hide == false) {
                                        echo wc_price($_product->get_price());
                                    }
                                    ?>
                                </td>
                                <td class="fav-date cart_td">
                                    <?php
                                 // echo date_format($cart_item['date_added'],"m/d/Y H:i:s");
                                    $date=date_create($cart_item['date_added']);
                                    echo date_format($date,"Y/m/d");

                                    ?>
                                </td>

                            </tr>
                            <?php
                        }

                        do_action('gpls_after_fav_row',$product_id);
                    }
                    ?>

                    <?php

                    do_action('gpls_woo_rfq_after_fav_cart_contents');

                    ?>

                    <?php do_action('gpls_woo_woocommerce_before_fav_cart_submit'); ?>

                </table>
            </div>
            <?php
            do_action('gpls_woo_rfq_after_fav_items_list');
            ?>

            <div style="clear:both"></div>
            <?php do_action('gpls_woo_woocommerce_after_fav_cart_table'); ?>


        </div>
    </form>
    <div style="clear: both"></div>

    <div class="woo_rfq_bottom_html_desc">
        <?php
        do_action('gpls_woo_rfq_fav_bottom_html_desc');
        ?>
    </div>

</div>


