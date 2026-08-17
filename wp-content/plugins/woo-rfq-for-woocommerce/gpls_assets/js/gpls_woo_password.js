
jQuery.ajaxSetup({ cache: false });
jQuery( window ).on("load", function() {
    try {
        if(localized_script.account_option==="always_pwd") {

            jQuery('#rfq_createaccount')[0].checked = true;
            jQuery('#rfq_createaccount').prop('disabled', true);
            jQuery('.gpls-woo-rfq_checkout_button').attr('disabled', 'disabled');
        }


        let isChecked = jQuery('#rfq_createaccount')[0].checked;

        if(isChecked)
        {
            jQuery(('#account_password')).attr("required", true);
            jQuery("#account_password").addClass("required");
            jQuery("#account_password").show();
            jQuery('.password_input_div').show();


        }else{
            jQuery('#account_password').attr("required", false);
            jQuery('#account_password').removeClass("required");
            jQuery("#account_password").hide();
            jQuery('.password_input_div').hide();

        }


        jQuery('#rfq_createaccount').on('click', function () {

            let isChecked = jQuery('#rfq_createaccount')[0].checked;

            if(isChecked)
            {
                jQuery(('#account_password')).attr("required", true);
                jQuery("#account_password").addClass("required");
                jQuery("#account_password").show();
                jQuery('.password_input_div').show();


            }else{
                jQuery('#account_password').attr("required", false);
                jQuery('#account_password').removeClass("required");
                jQuery("#account_password").hide();
                jQuery('.password_input_div').hide();

            }

        });

        jQuery( document ).ready( function( $ ) {
            jQuery( 'body' ).on( 'keyup', '#account_password', function( event ) {
                if( jQuery( this ).val() != '' ) {

                    jQuery( '#password-length','.woocommerce-password-hint' ).show();

                    check_password_strength( jQuery( '#account_password' ),
                        null,
                        jQuery( '#password-length' ),
                        jQuery('.gpls-woo-rfq_checkout_button'), ['123', 'abc', 'hello', 'admin'] );
                }else{
                    check_password_strength( jQuery( '#account_password' ),
                        null,
                        jQuery( '#password-length' ),
                        jQuery('.gpls-woo-rfq_checkout_button'), ['123', 'abc', 'hello', 'admin'] );
                    jQuery( '#password-length','.woocommerce-password-hint' ).hide();


                }
            });




        });

        function check_password_strength( password,  confirm_password, length_Message, submit_Btn, blacklist_Words ) {
            var password = password.val();

            blacklist_Words = blacklist_Words.concat( wp.passwordStrength.userInputDisallowedList() )
            jQuery('.gpls-woo-rfq_checkout_button').attr( 'disabled', 'disabled' );
            length_Message.removeClass( 'short bad good strong' );
            var password_length = wp.passwordStrength.meter( password, blacklist_Words );
            switch ( password_length ) {
                case 2:
                    length_Message.addClass( 'bad' ).html( pwsL10n.bad );
                    jQuery( '.woocommerce-password-hint' ).show();
                    break;
                case 3:
                    length_Message.addClass( 'good' ).html( pwsL10n.good );
                    jQuery( '.woocommerce-password-hint' ).hide();
                    break;
                case 4:
                    jQuery( '.woocommerce-password-hint' ).hide();
                    length_Message.addClass( 'strong' ).html( pwsL10n.strong );
                    break;

                default:
                    jQuery( '.woocommerce-password-hint' ).hide();
                    length_Message.addClass( 'short' ).html( pwsL10n.short );

            }
            if ( password_length >= 2  ) {
                // submit_Btn.removeAttr( 'disabled' );
                jQuery('.gpls-woo-rfq_checkout_button').removeAttr( 'disabled' );
            }else{
                jQuery( '.woocommerce-password-hint' ).show();

            }
            return password_length;


        }



    }catch(err){}

} ); // jQuery( document ).ready



















