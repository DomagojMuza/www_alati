<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>

<?php



ob_start();
$product_id=$product->get_id();
wc_get_template('woo-rfq/add-to-favs-link.php',
    array('product_id'=>$product_id), '', gpls_woo_rfq_WOO_PATH);
$fav_link = ob_get_clean();

?>


<form  style="display: block" class="link_to_favs_page_form"
      data-fav-product-id='<?php echo  $product_id  ?>'
      action='add-to-favs' method='post'>
    <input type='hidden' value='<?php echo $product_id; ?>' name='product_id' id='product_id'/>
                        <?php $nonce = wp_create_nonce('fav_id_nonce');
                        wp_nonce_field('fav_id_nonce'); ?>

    <div id='fav_link_<?php echo $product_id; ?>' style='display:block'><?php echo $fav_link ?></div>
    <div style="display:none !important;max-width:20px !important;margin-top:10px; text-align: center !important;margin-left: auto !important;margin-right:auto  !important"
         id='image_<?php echo $product_id; ?>'>
        <image style="max-width:10px !important"  src="<?php echo (gpls_woo_rfq_URL) ?>/gpls_assets/img/select2-spinner.gif"></image></div>
    <div class="notefav_checked" id='notefav_<?php echo $product_id; ?>'></div>

</form>




