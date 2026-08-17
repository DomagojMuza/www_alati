
jQuery( document ).ready( function() {




} ); // jQuery( document ).ready

function woorfq_main(){
    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');






    jQuery( '.gpls_rfq_set' ).show();jQuery( '.gpls_rfq_set' ).attr('style','visibility: visible;');
    jQuery( '.amount' ).hide();jQuery( '.amount' ).attr('style','visibility: collapse;');
    jQuery( '.bundle_price' ).hide();jQuery( '.bundle_price' ).attr('style','visibility: collapse;');
    jQuery( '.price' ).hide();jQuery( '.price' ).attr('style','visibility: collapse;');
    jQuery( '.product-selector__price' ).hide();jQuery( '.product-selector__price' ).attr('style','visibility: collapse;');
    jQuery( '.total' ).hide();jQuery( '.total' ).attr('style','visibility: collapse;');
    jQuery( '.from' ).hide();jQuery( '.from' ).attr('style','visibility: collapse;');
    jQuery( '.woocommerce-Price-amount,.from, .price,.total,.amount, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line' ).hide();
    jQuery( '.woocommerce-Price-amount,.from, .price,.total,.amount, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line' ).attr('style','visibility: collapse;');
    jQuery( '.woocommerce-shipping-totals' ).attr('style','visibility: collapse;');
    jQuery( '#shipping-option' ).attr('style','visibility: collapse;');
    jQuery( '.wc-block-checkout__shipping-option--free' ).attr('style','visibility: collapse;');
    jQuery( '.wc-block-checkout__shipping-option .wc-block-checkout__shipping-option--free' ).attr('style','visibility: collapse;');
    jQuery( '.wc-block-checkout__shipping-option' ).attr('style','visibility: collapse;');



    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').show();


    jQuery( '.related .woocommerce-Price-amount >*,' +
        '.related .from >*,' +
        '.related .price >*,' +
        '.related .total >*,' +
        '.related .amount >*,' +
        '.related  .bundle_price >*').attr('style','visibility: visible !important;');


}


jQuery(window).on("load",function () {
    woorfq_main();

});

jQuery( document ).ajaxComplete(function() {
    woorfq_main();


});
