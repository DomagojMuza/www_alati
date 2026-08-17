<?php
/*
 * Copyright (c) 2022. Neah Plugins All rights reserved.
 * Email: contact@neahplugins.com.
 */

/**
 * Privacy class GDPR Compliance
 * Author: Neah Plugins
 * Author URI: https://www.neahplugins.com/
 *
 * * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 */


if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
    return;
}

if( ! class_exists( 'Gpls_Woo_Rfq_Privacy' ) )
{



   class Gpls_Woo_Rfq_Privacy extends WC_Abstract_Privacy {
        /**
         * Constructor
         *
         */
        public function __construct() {

            parent::__construct( __( 'NP Quote Request for WooCommerce', 'woo-rfq-for-woocommerce' ) );

        }

        /**
         * Gets the message of the privacy to display.
         *
         */
        public function get_privacy_message() {

                      $content = '<p><b>' . __( 'The following is a suggested policy text. Please consult legal professional and modify for your use case as needed.
                      .', 'woo-rfq-for-woocommerce' ) . '</b></p>' .
            '<p>' . __( 'While visiting our site, we collect information so we can:', 'woo-rfq-for-woocommerce' ) . '</p>' ;

                    $content .=
                        '<ul>' .
                        '<li>' . __( 'Process your quote request.', 'woo-rfq-for-woocommerce' ) . '</li>' .
                        '<li>' . __( 'Email you with a quote regarding your quote request.', 'woo-rfq-for-woocommerce' ) . '</li>' .
                        '</ul>' .
                        '<p>' . __( 'We also use cookies to keep track of your quote request while you’re browsing our site.', 'woo-rfq-for-woocommerce' ) . '</p>'.

                      '<p>' . __( 'Our team have access to the information you provide while submitting your quote request to offer you the best quote.', 'woo-rfq-for-woocommerce' ) . '</p>' .
                        '<ul>' .
                        '<li>' . __( 'Such information include Quote request details such as products added, custom fields, date, name, address and email.', 'woo-rfq-for-woocommerce' ) . '</li>' .
                        '</ul>' ;

                       return $content;


        }




    }

new Gpls_Woo_Rfq_Privacy();

}