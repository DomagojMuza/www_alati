
import { sprintf, __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
//import {GPLS_Custom_Cart_Checkout_Labels} from './gpls_cart_checkout_labels';
//import  {Gpls_Cart_Checkout_Link_To_Quote} from './gpls_cart_checkout_link_to_quote'
import { useEffect, useState, useCallback } from '@wordpress/element';
//import { SelectControl, TextareaControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import DOMPurify from "dompurify";
import { Component } from 'react';
import $ from 'jquery';

const settings = getSetting( 'gpls-rfq_data', {} );


const defaultLabel = __(
	'Quote Request',
	'woo-rfq-for-woocommerce'
);

const label = decodeEntities( settings.title ) || defaultLabel;
const Label = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ label } />;
};

let submit_label = "";
let proceed_to_checkout_label = "";
let quote_link = '';
let rfq_mode=false;
let bid_form='';
let allow_bid='no';
let require_bid='no';

let cart_url='';
let checkout_url='';

jQuery.ajax({
	async: false,
	type: "post",
	dataType: "json",
	url: "/wp-admin/admin-ajax.php", //this is wordpress ajax file which is already avaiable in wordpress
	data: {
		action: 'gplswoo_get_submit_order_label', //this value is first parameter of add_action

	},
	success: function (resp) {



		submit_label = resp['rfq_cart_wordings_submit_your_rfq_text'];
		proceed_to_checkout_label = resp['rfq_cart_wordings_proceed_to_rfq'];
		quote_link=resp['rfq_cart_link_quote'];
		rfq_mode=resp['rfq_checkout_mode'];
		bid_form=resp['rfq_cart_bid'];
		allow_bid = resp['allow_bid'];
		require_bid = resp['require_bid'];
		cart_url = resp['cart_url'];
		checkout_url = resp['checkout_url'];
	}
});



//====================================================================

export function GPLS_Custom_Cart_Checkout_Labels() {

	const {registerCheckoutFilters} = window.wc.blocksCheckout;
	const { __ } = window.wp.i18n;
	const { registerPlugin } = window.wp.plugins;
	const { ExperimentalOrderMeta } = window.wc.blocksCheckout;


	if (rfq_mode ==="rfq") {



		if (document.URL.indexOf(cart_url) === 0)
		{

			registerCheckoutFilters('woo-rfq-for-woocommerce', {
				proceedToCheckoutButtonLabel: (e, t) => proceed_to_checkout_label,
			});
		}


		if (document.URL.indexOf(checkout_url) === 0)
		{

			registerCheckoutFilters('woo-rfq-for-woocommerce', {
				placeOrderButtonLabel: (e, t) => submit_label,
			});
		}
	}


	if (allow_bid==="yes") {
		jQuery(window).on("load", function () {
			//$('.wc-block-checkout__form').append(bid_form);
			//wp-block-woocommerce-checkout-contact-information-block
			jQuery(bid_form).insertAfter(".wp-block-woocommerce-checkout-payment-block");
			if (require_bid==="yes") {
				if (!jQuery('.wc-block-components-checkout-place-order-button').val()) {
					jQuery('.wc-block-components-checkout-place-order-button').prop("disabled", true);
					jQuery(".gpls_woo_rfq_plus_customer_bid_text").show();

				} else {
					jQuery('.wc-block-components-checkout-place-order-button').prop("disabled", false);
					jQuery(".gpls_woo_rfq_plus_customer_bid_text").hide();
				}
			}
		});
	}


}

GPLS_Custom_Cart_Checkout_Labels();
//======================================================================


//=====================================================================





//===============================================================

/**
 * Content component
 */
/*const Content = () => {
	return decodeEntities( settings.description || '' );
};*/


const Content = ( props ) => {
	const { eventRegistration, emitResponse } = props;
	const { onPaymentSetup } = eventRegistration;
	useEffect( () => {
		const unsubscribe = onPaymentSetup( async () => {
			// Here we can do any processing we need, and then emit a response.
			// For example, we might validate a custom field, or perform an AJAX request, and then emit a response indicating it is valid or not.

			const _woo_rfq_customer_bid = jQuery('#gpls_woo_rfq_plus_customer_bid').val();



			//const customDataIsValid = !! _woo_rfq_customer_bid.length;

			if ( 1 ) {
				return {
					type: emitResponse.responseTypes.SUCCESS,
					meta: {
						paymentMethodData: {
							_woo_rfq_customer_bid,
						},
					},
				};
			}

			return {
				type: emitResponse.responseTypes.ERROR,
				message: 'There was an error',
			};
		} );
		// Unsubscribes when this component is unmounted.
		return () => {
			unsubscribe();
		};
	}, [
		emitResponse.responseTypes.ERROR,
		emitResponse.responseTypes.SUCCESS,
		onPaymentSetup,
	] );
	return decodeEntities( settings.description || '' );
};







/**
 * payment method config object.
 */
const gpls_rfq = {
	name: "gpls-rfq",
	label: <Label />,
	content: <Content />,
	edit: <Content />,
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: settings.supports,
	},
};

registerPaymentMethod( gpls_rfq );



