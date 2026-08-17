<?php
/**
 * Subscription information template
 *
 * @author  Brent Shepherd / Chuck Mac
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v3.0.4
 */
// phpcs:ignoreFile
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( empty( $subscriptions ) ) {
	return;
}

$has_automatic_renewal = false;
$is_parent_order       = wcs_order_contains_subscription( $order, 'parent' );
?>
<div style="margin-bottom: 40px;">
<h2><?php esc_html_e( 'Subscription information', 'woo-rfq-for-woocommerce' ); ?></h2>
<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 0.5em;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'ID', 'subscription ID table heading', 'woo-rfq-for-woocommerce' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Start date', 'table heading', 'woo-rfq-for-woocommerce' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End date', 'table heading', 'woo-rfq-for-woocommerce' ); ?></th>
            <?php if ($show_prices  == 'yes')  : ?>
            <th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Recurring total', 'table heading', 'woo-rfq-for-woocommerce' ); ?></th>
            <?php endif; ?>
        </tr>
	</thead>
	<tbody>
	<?php foreach ( $subscriptions as $subscription ) : ?>
		<?php $has_automatic_renewal = $has_automatic_renewal || ! $subscription->is_manual(); ?>
		<tr>
            <td class="td" scope="row" style="text-align:left;"><?php echo ($subscription->get_order_number()); ?></td>
            <td class="td" scope="row" style="text-align:left;"><?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'start_date', 'site' ) ) ); ?></td>
			<td class="td" scope="row" style="text-align:left;"><?php echo esc_html( ( 0 < $subscription->get_time( 'end' ) ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When cancelled', 'Used as end date for an indefinite subscription', 'woo-rfq-for-woocommerce' ) ); ?></td>
            <?php if ($show_prices  == 'yes')  : ?>
            <td class="td" scope="row" style="text-align:left;">
				<?php echo ( $subscription->get_formatted_order_total() ); ?>
				<?php if ( $is_parent_order && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
					<br>
					<small><?php /* translators: %s: date */ printf( esc_html__( 'Next payment: %s', 'woo-rfq-for-woocommerce' ), esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'next_payment', 'site' ) ) ) ); ?></small>
				<?php endif; ?>
			</td>
            <?php endif; ?>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>

</div>

