<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/rfqtk-email-order-items.php.
 *
 */
// phpcs:ignoreFile
if (!defined('ABSPATH')) {
    exit;
}

$text_align = is_rtl() ? 'right' : 'left';

if (!isset($hide_admin)) {
    $hide_admin = false;
}

if (!isset($show_prices)) {
    $show_prices = true;
}

if ($show_prices == true) {
    gpls_woo_rfq_remove_filters();
}


foreach ($items as $item_id => $item) :
    if (apply_filters('woocommerce_order_item_visible', true, $item)) {
        $product = $item->get_product();

        if (!is_object($product) || !$product) {
            continue;
        }

        ?>
        <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
            <td class="td"
                style="text-align:<?php echo ($text_align); ?>; vertical-align:middle; border: 1px solid #eee;
                        font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;"><?php

                // Show title/image etc
                if ($show_image) {
                    try {

                        $current_array = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail');

                        echo (apply_filters('woocommerce_order_item_thumbnail', '<div style="margin-bottom: 5px"><img src="'
                            . ($product->get_image_id() && is_array($current_array) && !empty($current_array) ? current($current_array) :
                                wc_placeholder_img_src()) . '" alt="' . esc_attr__('Product image', 'woo-rfq-for-woocommerce') .
                            '" height="' . esc_attr($image_size[1]) . '" width="' . esc_attr($image_size[0]) . '" style="vertical-align:middle; margin-' .
                            (is_rtl() ? 'left' : 'right') . ': 10px;" /></div>', $item));
                    } catch (Exception $exception) {

                    }
                }

                // Product name
                echo (apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false));

                // SKU
                if ($show_sku && is_object($product) && $product->get_sku()) {
                    echo ' (#' . ($product->get_sku()) . ')';
                }

                // allow other plugins to add additional product information here
                do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text);

                wc_display_item_meta($item);

                if ($show_download_links) {
                    wc_display_item_downloads($item);
                }

                // allow other plugins to add additional product information here
                do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text);

                ?></td>
            <td class="td"
                style="text-align:<?php echo ($text_align); ?>;
                        vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php echo (apply_filters('woocommerce_email_order_item_quantity', $item->get_quantity(), $item)); ?></td>


            <?php if (($show_prices == true || $sent_to_admin == true) && $hide_admin == false) : ?>
                <td class="td"
                    style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                    <?php echo ($order->get_formatted_line_subtotal($item)); ?></td>
            <?php endif; ?>

        </tr>
        <?php
    }

    if ($show_purchase_note && is_object($product) && ($purchase_note = $product->get_purchase_note())) : ?>
        <tr>
            <td colspan="3"
                style="text-align:<?php echo ($text_align); ?>;
                        vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php echo (wpautop(do_shortcode(($purchase_note)))); ?></td>
        </tr>
    <?php endif; ?>

<?php endforeach; ?>
