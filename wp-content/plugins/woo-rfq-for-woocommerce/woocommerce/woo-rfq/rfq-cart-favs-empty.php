<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/woo-rfq/rfq-cart-empty.php.
 *

 */
// phpcs:ignoreFile
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



?>
<div style="clear:both; ">
<p class="cart-empty">
	<h3>

        <?php
        $quote_request_empty = __('Your Favorites List is Currently Empty.', 'woo-rfq-for-woocommerce');
        $quote_request_empty = __($quote_request_empty, 'woo-rfq-for-woocommerce');
        echo ($quote_request_empty);
        ?>

    </h3>
</p>


<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<p class="rfq-return-to-shop">
		<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php
            $return_to_shop = get_option('rfq_cart_wordings_return_to_shop', __('Return To Shop', 'woo-rfq-for-woocommerce'));
            $return_to_shop = __($return_to_shop, 'woo-rfq-for-woocommerce');
            echo ($return_to_shop);

            ?>
		</a>
	</p>
<?php endif; ?>


</div>
    <div style="clear: both"></div>

<?php


?>