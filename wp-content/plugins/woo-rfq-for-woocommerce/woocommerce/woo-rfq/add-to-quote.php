<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>

<?php



?>

<div style="clear:both">
<form  style="display: block;" class="woo_rfq_after_shop_loop_button"
      data-rfq-product-id='<?php echo ($rfq_id); ?>'
      action='<?php echo esc_url($product->add_to_cart_url()) ?>' method='post'>
                        <?php $nonce = wp_create_nonce('rfq_id_nonce');
                        wp_nonce_field('rfq_id_nonce'); ?>
                        <input type='hidden' value='<?php echo ($rfq_id); ?>' name='rfq_id' id='rfq_id'/>
                        <input type='hidden' value='<?php echo ($data_var); ?>' name='rfq_var' id='rfq_var'/>
                        <input class='variation_id' type='hidden' id='rfq_variation_id' name='rfq_variation_id'/>
                      <input type='hidden' value='<?php echo ($product_type); ?>' name='product_type' id='product_type'/>
                        <input type='hidden' value='<?php echo ($product->get_id()); ?>' name='rfq_product_id'
                               id='rfq_product_id'/>
                       <?php if(!class_exists('GPLS_WOO_RFQ_PLUS') || get_option("gpls_rfq_show_add_to_quote_qty","no")!="yes"): ?>
                       <input type='submit'  name='submit' value='<?php echo ($request_quote) ?>' id='rfq_button_<?php echo ($rfq_id); ?>'
                              class='button rfq_button'
                       style="<?php echo $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_styles'] ?>"
                       onmouseover="<?php echo ($gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'])
                           . ';' . ($gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover']) ?>"
                       onmouseout="<?php echo ($gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout']) . ';' .
                           ($gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout']) ?>"/>
                       <?php else: ?>
                       <?php do_action("gpls_rfq_add_to_quote_qty_action",
                               $rfq_id,$gpls_woo_rfq_file_add_to_quote_styles,$product,$request_quote,$data_var,$rfq_check) ?>
                       <?php endif; ?>
                        <div style="display:none !important;max-width:20px !important; text-align: center !important;margin-left: auto !important;margin-right:auto  !important" id='image_<?php echo ($rfq_id); ?>'>
                            <image style="max-width:10px !important"  src="<?php echo (gpls_woo_rfq_URL) ?>/gpls_assets/img/select2-spinner.gif"></image></div>
                        <div id='note_<?php echo $rfq_id; ?>'></div>



</form>
</div>
