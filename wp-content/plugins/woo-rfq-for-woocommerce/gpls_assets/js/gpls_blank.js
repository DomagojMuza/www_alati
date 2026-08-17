function changeClasses() {
    jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed wc-variation-is-unavailable');
    jQuery('.single_add_to_cart_button').addClass('woocommerce-variation-add-to-cart-enabled enabled');
    jQuery('.woocommerce-variation-add-to-cart').removeClass('woocommerce-variation-add-to-cart-disabled disabled');
    jQuery('.woocommerce-variation-add-to-cart').addClass('woocommerce-variation-add-to-cart-enabled enabled');
}


function gpls_enable() {

   jQuery('.variations_form').find('select').on('change', function (event) {


      // console.log(event);
       var selected= jQuery('.variations_form').find(":selected").val();
       //console.log(jQuery('.variations').find(":selected").val());
      // if(event.value !="")
       console.log(selected);
       if(selected != '')
       {
           changeClasses();
       }

    });


}


jQuery(window).on("load", function () {

    try {

        gpls_enable();

    } catch (err) {
        console.log(err);
    }

});


jQuery('form').on('change', function (event) {
    try {
        // console.log(event.target.nodeName);
        if (event.target.nodeName === "SELECT") {

            gpls_enable();

        }
    } catch (err) {
        console.log(err);
    }
});

jQuery('form').on('change', function (event) {
    try {
        // console.log(event.target.nodeName);
        if (event.target.nodeName === "SELECT") {

            gpls_enable();

        }
    } catch (err) {
        console.log(err);
    }
});
jQuery( document ).ajaxComplete(function() {
    try {

        gpls_enable();

    } catch (err) {
        console.log(err);
    }
});
jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed wc-variation-is-unavailable');
jQuery('.single_add_to_cart_button').addClass('woocommerce-variation-add-to-cart-enabled enabled');
jQuery('.woocommerce-variation-add-to-cart').removeClass('woocommerce-variation-add-to-cart-disabled disabled');
jQuery('.woocommerce-variation-add-to-cart').addClass('woocommerce-variation-add-to-cart-enabled enabled');