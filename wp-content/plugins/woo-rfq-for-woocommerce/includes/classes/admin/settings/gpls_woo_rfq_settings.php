<?php
if (!class_exists('GPLS_Woo_RFQ_Settings')) {

    class GPLS_Woo_RFQ_Settings extends WC_Settings_Page
    {


        public function __construct()
        {
            parent::__construct();

            $this->id = 'settings_gpls_woo_rfq';
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
            add_action('woocommerce_admin_field_gpls_woo_options', array(__CLASS__, 'gpls_woo_rfq_more_options'), 100000, 1);

            if (!has_filter('woocommerce_admin_settings_sanitize_option', array($this, 'gpls_woo_rfq_basic_admin_field_validator'), 100000, 3)) {

                add_filter('woocommerce_admin_settings_sanitize_option', array($this, 'gpls_woo_rfq_basic_admin_field_validator'), 100000, 3);
            }


        }

        public  function gpls_woo_rfq_basic_admin_field_validator($value, $option, $raw_value)
        {




            if ($option['id'] == 'settings_gpls_woo_rfq_revert_to_classic') {

                if (isset($_POST['settings_gpls_woo_rfq_revert_to_classic'], $_REQUEST['_wpnonce'])
                    && wp_verify_nonce( sanitize_key(wp_unslash( $_REQUEST['_wpnonce'] )), 'woocommerce-settings' ) ) {
                   // add_action('admin_notices', array($this, 'settings_gpls_woo_rfq_revert_to_classic_notice'));




                    $cart_page_id=get_option( 'woocommerce_cart_page_id' );

                    if(function_exists('wp_save_post_revision'))
                    wp_save_post_revision($cart_page_id);

                    $post_arr_cart = array(
                        'ID'           => $cart_page_id,
                        'post_content'=>'<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',

                    );
                    wp_update_post($post_arr_cart);


                    $checkout_page_id=get_option( 'woocommerce_checkout_page_id' );

                    if(function_exists('wp_save_post_revision'))
                    wp_save_post_revision($cart_page_id);

                    $post_arr_checkout = array(
                        'ID'           => $checkout_page_id,
                        'post_content'=>'<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',

                    );
                    wp_update_post($post_arr_checkout);

                    add_action('admin_notices', array($this, 'settings_gpls_woo_rfq_revert_to_classic_notice'));

                    $value = 'no';
                }

            }

            return $value;

        }

        public function settings_gpls_woo_rfq_revert_to_classic_notice()
        {
            ?>
            <div class="notice error my-acf-notice is-dismissible">


              <?php printf(wp_kses_post(__('<br />Cart and Checkout pages have been reverted to classic cart and checkout using shortcodes.<br /><br />
              ', 'woo-rfq-for-woocommerce'), '<span style="color: green; font-weight: bold">', '</span>')); ?>



            </div>
            <?php
            return 0;
        }

        public function get_sections()
        {

            if (class_exists('GPLS_WOO_RFQ_PLUS')) {
                $sections = array(
                    '' => __('General', 'woo-rfq-for-woocommerce'),
                    'translations' => __('Labels', 'woo-rfq-for-woocommerce'),
                    'links' => __('Links', 'woo-rfq-for-woocommerce'),
                    'rfq_page' => __('Quote Request Page', 'woo-rfq-for-woocommerce'),
                    'add-to-quote' => __('Quote Button', 'woo-rfq-for-woocommerce'),
                    'favorites' => __('Favorites', 'woo-rfq-for-woocommerce'),
                );
            } else {
                $sections = array(
                    '' => __('General', 'woo-rfq-for-woocommerce'),
                    'translations' => __('Labels', 'woo-rfq-for-woocommerce'),
                    'links' => __('Links', 'woo-rfq-for-woocommerce'),
                    'rfq_page' => __('Quote Request Page', 'woo-rfq-for-woocommerce'),
                    'add-to-quote' => __('Quote Button', 'woo-rfq-for-woocommerce'),
                    'favorites' => __('Favorites', 'woo-rfq-for-woocommerce'),
                    'email-options' => __('Email & Proposal', 'woo-rfq-for-woocommerce'),

                );


            }
            if (!class_exists('GPLS_WOO_RFQ_FIELD') && !class_exists('GPLS_WOO_RFQ_PLUS')
                && !class_exists('GPLS_WOO_PDF') && !class_exists('GPLS_WOO_RFQ_UPLOAD')
            ) {
                $sections['npoptions'] = __('More Options', 'woo-rfq-for-woocommerce');
            }
            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }

        public function output()
        {
            global $current_section;
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::output_fields($settings);
        }


        public function add_settings_tab($settings_tabs)
        {
            $settings_tabs[$this->id] = 'RFQ-ToolKit';
            return $settings_tabs;
        }


        public function save()
        {
            global $current_section;
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::save_fields($settings);
        }


        public static function gpls_woo_rfq_more_options($value)
        {
//   WordPress.WP.I18n.NoEmptyStrings
            $option_value = get_option($value['id'], $value['default']);

            ?>



            <tr valign="top" >

                <td class="forminp forminp-<?php echo wp_kses_post(sanitize_title($value['type'])) ?>">


                    <table style="background:white; width:100%; min-width:800px">


                        <tr valign="top">

                            <td class="forminp">
                                <div>
                                    <table style="background:#3d363614;margin-top:1em">

                                        <tr>
                                            <td>
                                                <div class="plus_options" style=" ">

                                                    <ul class="plus_options_ul">

                                                        <li class="plus_options_li" style="margin-top: 15px;">
                                                            <div>
                                                                <span class="plus_options-header"> <?php echo esc_html_e('Available in the Plus Version:', 'woo-rfq-for-woocommerce'); ?></span>
                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php  esc_html_e('Buy or request a quote at woocommerce checkout', 'woo-rfq-for-woocommerce') ?></strong>:
                                                                <?php esc_html_e('Allow the choice to purchase or request a quote at WooCommerce checkout.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Buy or request a quote based on items in the cart', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('If the cart contains a "quote item", then customer can only request a quote.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>
                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Enable role based price visibility and checkout options at WooCommerce checkout:', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>


                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Bulk action for stores with large number of products:', 'woo-rfq-for-woocommerce') ?></strong>

                                                                <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php esc_html_e('Bulk enable/disable quote items, "Hide Add to Cart", "Hide Price"  by category.', 'woo-rfq-for-woocommerce'); ?>

                                                            </div>
                                                        </li>


                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Use Google ReCAPTCHA for quote request:', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>
                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Allow visitors to pay for an order without having to log on first (guest pay).', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>
                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Enable price visibility by IP address.', 'woo-rfq-for-woocommerce') ?></strong>


                                                            </div>
                                                        </li>
                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('And more!', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>


                                                    </ul>
                                                    <ul class="plus_options_ul">
                                                        <li class="plus_options_li plus_large">
                                                            <div style="margin-bottom:20px"><span> <a target="_blank"
                                                                                                      class="get_plus"
                                                                                                      href="https://neahplugins.com/product/woocommerce-quote-request-plus/"><?php  esc_html_e('Get Quote Request Plus For WooCommerce!', 'woo-rfq-for-woocommerce'); ?></a></span>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="clear_narrow">&nbsp;</div>
                                                <div class="plus_narrow">

                                                </div>
                                            </td>

                                        </tr>


                                    </table>
                                </div>

                            </td>
                        </tr>

                        <tr valign="top">

                            <td class="forminp">
                                <div>
                                    <table style="background:#3d363614;margin-top:1em">

                                        <tr>
                                            <td>
                                                <div class="plus_options" style=" ">

                                                    <ul class="plus_options_ul">


                                                        <li class="plus_options_li" style="margin-top: 15px;">
                                                            <div>
                                                                <span class="plus_options-header"> <?php esc_html_e('Available in the Cart & Quote to PDF For WooCommerce :', 'woo-rfq-for-woocommerce'); ?></span>
                                                            </div>

                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('PDF Attachments', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('Include a PDF version of the quote to in the quote email', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('WooCommerce cart to Quote', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('Export the cart to a PDF download and optionally create a quote request.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Include a custom form with the WooCommerce cart to Quote:', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('Collect needed information to prepare a quote for the customer.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('And more!', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>


                                                    </ul>
                                                    <ul class="plus_options_ul">
                                                        <li class="plus_options_li plus_large">
                                                            <div style="margin-bottom:20px"><span> <a target="_blank"
                                                                                                      class="get_plus"
                                                                                                      href="https://neahplugins.com/product/np-quote-request-woocommerce-email-cart-pdf/"><?php esc_html_e('Try cart & quote to PDF for free!', 'woo-rfq-for-woocommerce'); ?></a></span>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="clear_narrow">&nbsp;</div>
                                                <div class="plus_narrow">

                                                </div>
                                            </td>

                                        </tr>


                                    </table>
                                </div>

                            </td>
                        </tr>


                        <tr valign="top">

                            <td class="forminp">
                                <div>
                                    <table style="background:#3d363614;margin-top:1em">

                                        <tr>
                                            <td>
                                                <div class="plus_options" style=" ">

                                                    <ul class="plus_options_ul">


                                                        <li class="plus_options_li" style="margin-top: 15px;">
                                                            <div>
                                                                <span class="plus_options-header"> <?php esc_html_e('Available in File Upload Add-on:', 'woo-rfq-for-woocommerce'); ?></span>
                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('File Uploads:', 'woo-rfq-for-woocommerce') ?></strong>:
                                                                <?php esc_html_e('Allow your customers to upload documents that are linked to quote requests or orders.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Google ReCAPTCHA:', 'woo-rfq-for-woocommerce') ?></strong>: <?php esc_html_e('Optionally enable Google ReCAPTCHA for file uploads.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Allow upload in multiple places:', 'woo-rfq-for-woocommerce') ?>
                                                                </strong>: <?php esc_html_e('Quote Request page, checkout page, my accounts page, thank you page or at product level.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>
                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('Centralized File Repository:', 'woo-rfq-for-woocommerce') ?>
                                                                </strong>: <?php esc_html_e('File repository to view and manage the uploaded files.', 'woo-rfq-for-woocommerce') ?>

                                                            </div>
                                                        </li>

                                                        <li class="plus_options_li">
                                                            <div>
                                                                <span class="bulletpoint">&#8226;</span>&nbsp;<strong> <?php esc_html_e('And more!', 'woo-rfq-for-woocommerce') ?></strong>

                                                            </div>
                                                        </li>


                                                    </ul>
                                                    <ul class="plus_options_ul">
                                                        <li class="plus_options_li plus_large">
                                                            <div style="margin-bottom:20px"><span> <a target="_blank"
                                                                                                      class="get_plus"
                                                                                                      href="https://neahplugins.com/product/woocommerce-quote-request-upload-files/"><?php esc_html_e('GET File Upload For WooCommerce!', 'woo-rfq-for-woocommerce'); ?></a></span>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="clear_narrow">&nbsp;</div>
                                                <div class="plus_narrow">

                                                </div>
                                            </td>

                                        </tr>


                                    </table>
                                </div>

                            </td>
                        </tr>


                    </table>


                </td>
            </tr>
            <?php
        }



        public  function np_checkout_page()
        {
            $block_checkout = WC_Blocks_Utils::has_block_in_page(wc_get_page_id('checkout'), 'woocommerce/checkout');
            $cart_checkout = WC_Blocks_Utils::has_block_in_page(wc_get_page_id('cart'), 'woocommerce/cart');

            if ($block_checkout || $cart_checkout) {
                return "cart and checkout blocks";

            } else {
                return "classic shortcode checkout";
            }
        }



        public function get_settings($section = null)
        {

            $shortcode_or_block='';
            
            $shortcode_or_block_message="";



            $str_classic_begin = '';


            $str_classic_end = '';

            $settings = array();

            switch ($section) {
                case '' :
                    $settings =

                        array(

                            'general_section_title' => array(
                                'name' => __('RFQ-ToolKit General Options', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => __('RFQ-ToolKit general options ', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_general_section_title'
                            ),



                            'checkout_option' => array(
                                'name' => '1- ' . __('Checkout Option', 'woo-rfq-for-woocommerce'),
                                'type' => 'select',
                                'options' => array(
                                    'normal_checkout' => __('Normal Checkout', 'woo-rfq-for-woocommerce'),
                                    'rfq' => __('RFQ', 'woo-rfq-for-woocommerce'),),
                                'desc' => __('RFQ turns WooCommerce shopping cart into a request for quote.<br /> 
         In premium version there are additional options to buy or request the whole cart as a quote at WooCommerce checkout.<br />
          RFQ option provides very high compatibility with other third party plugins and products.<br />
           Normal option allows checking out normally or request a quote using a quote cart.', 'woo-rfq-for-woocommerce').
                                    '<br /><br />' . __('Normal Checkout: If normal checkout, prices will be shown except for selected products<br />
 (managed in product setup-advanced tab). 
 Customers can only inquire about the products that you specify in product setup in the advanced tab or in the products list in admin. 
<br><br>RFQ Checkout: In RFQ mode the plugin is integrated with the WooCommerce cart and<br />
 the entire cart is submitted as a quote request. All the prices are hidden and at checkout the option is to submit a quote request. <br /><br /> 
  ', 'woo-rfq-for-woocommerce'),
                                'default' => 'normal_checkout',
                                'id' => 'settings_gpls_woo_rfq_checkout_option'
                            ),

                            'settings_gpls_woo_rfq_show_prices' => array(
                                'name' => '2- ' . __('Always Show Product Prices With RFQ Checkout', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('Applicable to RFQ checkout option only.<br /> Prices are shown for products but checkout is still a request for quote. Premium version show prices in the email and thank you page also.', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_prices'
                            ),

                            'settings_gpls_woo_rfq_normal_checkout_show_prices' => array(
                                'name' => '3- ' . __('Show Prices With Normal Checkout', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('Applicable to normal checkout option only.<br /> Prices are shown on the site but customer can inquire or checkout with selected products.< br />
                            This allows customers to checkout using the published prices or to request a personalized quote.  Premium version show prices in the email and thank you page also', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_normal_checkout_show_prices'
                            ),
                            'settings_gpls_woo_rfq_allow_out_of_stock' => array(
                                'name' => '3-0 ' . __('Allow quote items when they are out of stock', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('<br />Allow quote items when they are out of stock', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_allow_out_of_stock'
                            ),
                            'rfq_cart_wordings_outofstock_text' => array(
                                'name' => '3-01- Normal & RFQ Checkout-' . __('sold-out Message', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'If 3-0 above is selected,<br />
                                     this message(not blank) will show if customer is viewing a sold-out quote item',
                                'default' => '',
                                'id' => 'rfq_cart_wordings_outofstock_text',
                                'css' => 'width:400px'
                            ),
                            'settings_gpls_woo_rfq_hide_visitor_prices' => array(
                                'name' => '4- ' . __('Hide Prices from Visitors', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('Hide Prices From Visitor. Visitors who are not logged in can only submit a quotes request. <br />Enable guest checkout so the customers can submit requests as guest', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_hide_visitor_prices'
                            ),
                            'general_section_end345' => array(
                                'type' => 'sectionend',
                                'id' => 'settings_gpls_woo_rfq_general_section_end345'
                            ),
                            'general_section_title2a' => array(
                                'name' => '',
                                'type' => 'title',
                                'desc' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                                'id' => 'settings_gpls_woo_rfq_general_section_title2a'
                            ),
                            'settings_gpls_woo_rfq_no_payment_checkout' => array(
                                'name' => '4-02 ' . __('Allow checkout without payment at WooCommerce checkout. DOES NOT CREATE A QUOTE REQUEST', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('No Payment Checkout: Customer can complete an order at checkout without payment.<br />
   Admin can then optionally covert the order to a quote request and email the customer.<br />
  If this option is selected, Customer can checkout without payment.<br />
   The order status is pending and admin receives a new order notification<br /> about a new order with "no payment" in the subject and heading of order ', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_no_payment_checkout'
                            ),
                            'settings_gpls_woo_rfq_no_payment_checkout_text' => array(
                                'name' => '4-03 ' . __('No payment message', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'If 4-02 above is selected,<br />
                                     this message will be added to the subject of the notification email to admin.<br />
                                      It also adds a note to the order',
                                'default' => __('No Payment', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_no_payment_checkout_text',
                                'css' => 'width:200px'
                            ),
                            'general_section_end34523edfwww3444' => array(
                                'type' => 'sectionend',
                                'id' => 'settings_gpls_woo_rfq_general_section_end34534ed3444444'
                            ),
                            'general_section_title2sssa' => array(
                                'name' => '',
                                'type' => 'title',
                                'desc' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                                'id' => 'settings_gpls_woo_rfq_general_section_titlesss2a'
                            ),

                            'general_section_end34523edfwww' => array(
                                'type' => 'sectionend',
                                'id' => 'settings_gpls_woo_rfq_general_section_end34534ed344'
                            ),



                        );
                    break;

                case 'translations':
                    $settings =

                        array(

                            'rfq_cart_wordings_section_title' => array(
                                'name' => __('Custom Labels', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => __('Manage labels and wordings', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_section_title'
                            ),


                            'rfq_cart_wordings_add_to_cart' => array(
                                'name' => '1- RFQ Checkout-' . __('Add To Cart Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "Add to Cart" - (Change to Add To Quote in RFQ Checkout)', 'woo-rfq-for-woocommerce'),
                                'default' => __('Add to Cart', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_add_to_cart',
                                'css' => 'width:400px'
                            ),

                            'rfq_cart_wordings_in_cart' => array(
                                'name' => '2- RFQ Checkout-' . __('Add To Cart Again Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "Already in the Cart"- (Change to Add To Quote in RFQ Checkout) ', 'woo-rfq-for-woocommerce'),
                                'default' => __('Add to Cart', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_in_cart',
                                'css' => 'width:400px'
                            ),

                            'rfq_cart_wordings_add_to_rfq' => array(
                                'name' => '3- Normal Checkout-' . __('Add To Quote Request Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Normal Checkout Only. Choose the text for "Request Quote"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Add To Quote', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_add_to_rfq',
                                'css' => 'width:400px'
                            ),

                            'rfq_cart_wordings_in_rfq' => array(
                                'name' => '4- Normal Checkout-' . __('Add To Quote Request Again Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Normal Checkout Only. Choose the text for "Already In Quote Request"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Add To Quote', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_in_rfq',
                                'css' => 'width:400px'
                            ),

                            'rfq_cart_wordings_proceed_to_rfq' => array(
                                'name' => '5- ' . __('RFQ Checkout- Proceed To Submit Your Quote Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('RFQ Checkout- Choose the text for "Proceed To Submit Your Quote"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Proceed To Submit Your Quote', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_proceed_to_rfq',
                                'css' => 'width:400px'
                            ),


                            'rfq_cart_wordings_submit_your_rfq_text' => array(
                                'name' => '6- ' . __('Normal & RFQ Checkout - Submit Your Quote Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Normal & RFQ Checkout- Choose the text for "Submit Your Request For Quote"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Proceed To Submit Your Quote', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_submit_your_rfq_text',
                                'css' => 'width:400px'
                            ),


                            'rfq_cart_wordings_view_rfq_cart' => array(
                                'name' => '7- Normal & RFQ Checkout- ' . __('View List', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "View Your Quote Cart"', 'woo-rfq-for-woocommerce'),
                                'default' => __('View List', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_view_rfq_cart',
                                'css' => 'width:400px'
                            ),

                            'rfq_cart_wordings_rfq_cart_is_empty' => array(
                                'name' => '8- Normal Checkout- ' . __('Quote Request List Is Empty Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "You Quote cart is empty and was not submitted"', 'woo-rfq-for-woocommerce'),
                                'default' => __('You quote request list is empty and was not submitted', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_rfq_cart_is_empty',
                                'css' => 'width:400px'
                            ),
                            'rfq_cart_wordings_return_to_shop' => array(
                                'name' => '9- Normal Checkout- ' . __('Return To Shop In Quote Request Page', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "Return To Shop In Quote Request Page"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Return To Shop', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_return_to_shop',
                                'css' => 'width:400px'
                            ),
                            'rfq_cart_wordings_product_was_added_to_quote_request' => array(
                                'name' => '10- Normal & RFQ Checkout- ' . __('Product Was Added To The Quote Request', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Choose the text for "Added to Quote List"', 'woo-rfq-for-woocommerce'),
                                'default' => __('Product was successfully added to quote request.', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_product_was_added_to_quote_request',
                                'css' => 'width:400px'
                            ),
                            'gpls_woo_rfq_quote_submit_confirm_message' => array(
                                'name' => '11- Normal & RFQ Checkout- ' . __('Your Request Has Been Submitted', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Your quote request has been successfully submitted!', 'woo-rfq-for-woocommerce'),
                                'default' => __('Your request has been successfully submitted!', 'woo-rfq-for-woocommerce'),
                                'id' => 'gpls_woo_rfq_quote_submit_confirm_message',
                                'css' => 'width:400px'
                            ),
                            'rfq_cart_wordings_quote_request_currently_empty' => array(
                                'name' => '12- Normal Checkout- ' . __('Your Quote Request List Is Currently Empty', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => '',
                                'default' => __('Your Quote Request List is Currently Empty.', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_quote_request_currently_empty',
                                'css' => 'width:400px'
                            ),
                            'rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button' => array(
                                'name' => '13- Normal Checkout-' . __('Update Quote Request', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'Button Text to update Quest Request List in Quote Request Page',
                                'default' => __('Update Quote Request', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button',
                                'css' => 'width:400px'
                            ),
                            'settings_gpls_woo_rfq_customer_info_label' => array(
                                'name' => '14- Normal Checkout-' . __('Customer Information', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'Section title for customer information in the quote request page',
                                'default' => __('Customer Information', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_customer_info_label',
                                'css' => 'width:400px'
                            ),
                            'settings_gpls_woo_rfq_read_more' => array(
                                'name' => '15-  Normal & RFQ Checkout-' . __('Read more', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'label for products with options or no prices',
                                'default' => __('Read more', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_read_more',
                                'css' => 'width:400px'
                            ),
                            'settings_gpls_woo_rfq_Select_Options' => array(
                                'name' => '16-  Normal & RFQ Checkout-' . __('Select Options', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => 'label for products with options or no prices',
                                'default' => __('Select options', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_Select_Options',
                                'css' => 'width:400px'
                            ),


                            'general_section_end' => array(
                                'type' => 'sectionend',
                                'id' => 'settings_gpls_woo_rfq_general_section_end'
                            ),


                        );
                    break;

                case 'links':
                    $settings =

                        array(
                            'rfq_cart_section_title' => array(
                                'name' => __('Show links to Quote Request Page', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => __('Manage links to "Request Quote Page" - (normal checkout)', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_section_title'
                            ),
                            'settings_gpls_woo_rfq_show_cart_link_archive_top' => array(
                                'name' => '1- ' . __('Show Link To Quote Request Page At The Top of the Product Archives Page', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => '',
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_cart_link_archive_top'
                            ),
                            'settings_gpls_woo_rfq_show_cart_link_archive_end' => array(
                                'name' => '2- ' . __('Show Link To Quote Request Page At The Bottom of the Product Archives Page', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => '',
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_cart_link_archive_end'
                            ),
                            'settings_gpls_woo_rfq_show_cart_link_cart' => array(
                                'name' => '3- ' . __('Show Link To Quote Request Page in WooCommerce Cart Page', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => $str_classic_begin .__('Link at bottom of WooCommerce Cart Page', 'woo-rfq-for-woocommerce').'</div>' . $str_classic_end,
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_cart_link_cart'
                            ),


                            'settings_gpls_woo_rfq_show_cart_single_page' => array(
                                'name' => '4- ' . __('Show Link To Quote Request Page in the Single Product Description', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => '',
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_cart_single_page'
                            ),

                            'settings_gpls_woo_rfq_show_cart_thank_you_page' => array(
                                'name' => '5- ' . __('Show Link To Quote Request Page in the Thank you page', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => '',
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_show_cart_thank_you_page'
                            ),

                            'rfq_cart_section_title_end' => array(
                                'type' => 'sectionend',
                                'id' => 'rfq_cart_section_title_end'
                            ),
                        );
                    break;

                case 'rfq_page':
                    $settings =

                        array(

                            'rfq_cart_sc_section_title' => array(
                                'name' => __('Default Request for Quote Page: Applicable In Normal Checkout Mode', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => __('<p>In the Normal Checkout mode, a default page called <i>Quote Request</i> is created to view the quote list. This page
                                            is only needed if in the Normal Checkout mode. (In RFQ mode, the WooCommerce cart
                                           is used to display the items requested for quote.)</p>
                                           <p><b>Exclude this page from page caching. 
                                           You can read more <a href="https://neahplugins.com/how-to-make-the-np-quote-plugin-work-with-page-caching/" target="_blank" >How to make the NP Quote Plugin work with page caching plugins</a> </b></p>
                                           </b> You can modify the <i>Quote Request</i> page by modifying the "rfq-cart.php"
                                            template in "plugins/woo-rfq-for-woocommerce/woocommerce/woo-rfq/rfq-cart.php".
                                           Create the "woo-rfq" folder under "your_child_theme/woocommerce" and  Copy the "rfq-cart.php" file to the "woo-rfq" folder 
                                           under the WooCommerce directory in your child theme directory and modify it. 
                                           <b>You can read more <a href="https://neahplugins.com/how-to-customize-the-quote-request-page/" target="_blank" >
                                           How to customize the quote request page</a></b>  <br /><br />
                                           You can also use the short code <b>[gpls_woo_rfq_get_cart_sc]</b>  in your own page.</p>                                           
                                             ', 'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_cart_sc_section_title',
                                'css' => 'width:600px'
                            ),


                            'rfq_cart_sc_section_show_link_to_rfq_page' => array(
                                'name' => __('Request for Quote Page URL', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Request for Quote Page URL.Please show the full URL. Use the same domain/subdomain name as your site.<br />'
                                    , 'woo-rfq-for-woocommerce'),
                                'default' => '/quote-request/',
                                'id' => 'rfq_cart_sc_section_show_link_to_rfq_page',
                                'css' => 'width:400px;'
                            ),

                            'rfq_cart_sc_section_title_end' => array(
                                'type' => 'sectionend',
                                'id' => 'rfq_cart_sc_section_title_end'
                            ),

                            'general_section_title451441' => array(
                                'name' => 'Session Cookie',
                                'type' => 'title',
                                'desc' => '<hr width="100%" size="2" />',
                                'id' => 'general_section_title451441'
                            ),

                            'settings_gpls_woo_rfq_cookie_or_phpsession' => array(
                                'name' => __('Choose Session method', 'woo-rfq-for-woocommerce'),
                                'type' => 'select',
                                'options' => array(
                                    'rfq_cookie' => __('Cookie', 'woo-rfq-for-woocommerce'),
                                    'php_session' => __('PHP Sessions', 'woo-rfq-for-woocommerce')
                                ),
                                'desc' => "<br />" . __('Some Hosting providers do not allow PHP sessions.<br />
                                 If there is a problem with items showing in the quote request page,<br /> changing the session method could help.', 'woo-rfq-for-woocommerce'),
                                'default' => 'rfq_cookie',
                                'id' => 'settings_gpls_woo_rfq_cookie_or_phpsession')
                        ,

                            'settings_gpls_woo_rfq_cookie_prepend' => array(
                                'name' => __('Cookie Name Starting Characters', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Some caching platforms use this to avoid caching cookies.', 'woo-rfq-for-woocommerce'),
                                'default' => '',
                                'id' => 'settings_gpls_woo_rfq_cookie_prepend',
                                'css' => 'width:400px'
                            ),
                            'settings_gpls_woo_rfq_cookie_days_keep' => array(
                                'name' => __('Days to keep items in quote cart(greater than 0)', 'woo-rfq-for-woocommerce'),
                                'type' => 'number',
                                'custom_attributes' => array(
                                    'step' => '1',
                                    'min' => '1',
                                ),
                                'desc' => __('Number of days to keep items in the quote cart.<br /> Should be greater than 0.  Default is 1 day(24 hours). <br />
                                Increase this if you want to keep items in the quote cart longer.<br /> Larger numbers take up more room in the database', 'woo-rfq-for-woocommerce'),
                                'default' => 30,
                                'id' => 'settings_gpls_woo_rfq_cookie_days_keep',
                                'css' => 'width:400px'
                            ),
                            'general_section_end34451441' => array(
                                'type' => 'sectionend',
                                'id' => 'general_section_end34451441'
                            ),


                        );
                    break;

                case 'add-to-quote':
                    $settings =

                        array(
                            'rfq_cart_short_redirect_title' => array(
                                'name' => __('Add to Quote Button', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => '',
                                'id' => 'rfq_cart_short_redirect_title',
                                'css' => 'width:600px'
                            ),
                            'settings_gpls_woo_rfq_normal_checkout_quote_position' => array(
                                'name' => __('Choose the position of "add to quote" in the shop and archive', 'woo-rfq-for-woocommerce'),
                                'type' => 'select',
                                'options' => array(
                                    'woocommerce_after_shop_loop_item' => __('At the bottom', 'woo-rfq-for-woocommerce'),
                                    'woocommerce_before_shop_loop_item' => __('At the top', 'woo-rfq-for-woocommerce'),
                                    'woocommerce_before_shop_loop_item_title' => __('Before the title', 'woo-rfq-for-woocommerce'),
                                    'woocommerce_shop_loop_item_title' => __('After title', 'woo-rfq-for-woocommerce')
                                ),
                                'desc' => "<br />" . __('Default position is at the bottom.".', 'woo-rfq-for-woocommerce'),
                                'default' => 'woocommerce_after_shop_loop_item',
                                'id' => 'settings_gpls_woo_rfq_normal_checkout_quote_position'),





                            'settings_gpls_woo_rfq_normal_checkout_quote_single_position' => array(
                                'name' => __('Choose the position of "add to quote" in the single product', 'woo-rfq-for-woocommerce'),
                                'type' => 'select',
                                'options' => array(
                                    'woocommerce_before_add_to_cart_button' => __('Before Add to Cart', 'woo-rfq-for-woocommerce'),
                                    'woocommerce_after_add_to_cart_button' => __('After Add to Cart', 'woo-rfq-for-woocommerce'),

                                ),
                                'desc' => "<br />" . __('. This only matters when using normal checkout with prices showing.
                                <br />Default position is before add to cart.".', 'woo-rfq-for-woocommerce'),
                                'default' => 'woocommerce_before_add_to_cart_button',
                                'id' => 'settings_gpls_woo_rfq_normal_checkout_quote_single_position'),

                            'rfq_cart_sc_redirect_title_end245' => array(
                                'type' => 'sectionend',
                                'id' => 'rfq_cart_sc_redirect_title_end245'
                            ),
                        );
                    break;

                case 'favorites':
                    $settings =

                        array(
                            'rfq_fav_section_admin_title1' => array(
                                'name' => __('Admin Page', 'rfqtk'),
                                'type' => 'title',
                                'desc' => __('<span style="color:navy;font-size:small">
                                Favorites admin Page for searching and exporting all your customers favorites: available in the
                                 <a href="https://neahplugins.com/product/woocommerce-quote-request-plus/">Plus Version</a></span>.<p><b>Exclude this page from page caching. 
                                           You can read more <a href="https://neahplugins.com/how-to-make-the-np-quote-plugin-work-with-page-caching/" target="_blank" >How to make the NP Quote Plugin work with page caching plugins</a> </b></p>
                                           </b> You can modify the <i>Quote Request</i> page by modifying the "fav-cart.php"
                                            template in "plugins/woo-rfq-for-woocommerce/woocommerce/woo-rfq/fav-cart.php".
                                           Create the "woo-rfq" folder under "your_child_theme/woocommerce" and  Copy the "fav-cart.php" file to the "woo-rfq" folder 
                                           under the WooCommerce directory in your child theme directory and modify it. ',
                                    'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_fav_section_admin_title1'
                            ),
                            'rfq_fav_section_admin_title_end1' => array(
                                'type' => 'sectionend',
                                'id' => 'rfq_fav_section_admin_title_end1'
                            ),

                            'rfq_fav_section_title' => array(
                                'name' => __('Allow Customers to Save Favorites', 'woo-rfq-for-woocommerce'),
                                'type' => 'title',
                                'desc' => __('If enabled, customers can save favorites and submit later for purchase or quote request',
                                    'woo-rfq-for-woocommerce'),
                                'id' => 'rfq_fav_section_title'
                            ),

                            'settings_gpls_woo_rfq_allow_favorites' => array(
                                'name' => '1- ' . __('Enable saving favorites', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' => __('Enable feature', 'woo-rfq-for-woocommerce'),
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_allow_favorites'
                            ),
                            'settings_gpls_woo_rfq_allow_anon_favorites' => array(
                                'name' => '2- ' . __('Enable saving favorites for visitors', 'woo-rfq-for-woocommerce'),
                                'type' => 'checkbox',
                                'desc' =>__('Enable feature for visitors.<br/>
                             Visitors favorites are saved on a temporary basis.<br/> See "Quote Request Page" setting for number of days quotes are kept', 'rfqtk')
                            ,
                                'default' => 'no',
                                'id' => 'settings_gpls_woo_rfq_allow_anon_favorites'
                            ),
                            'settings_gpls_woo_rfq_my_acct_favorites_label' => array(
                                'name' => '3-' . __('Favorites Label', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('The label to use for favorites in "my account" section',''),
                                'default' => __('Favorites', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_my_acct_favorites_label',
                                'css' => 'width:200px'
                            ),
                            'settings_rfq_favs_wordings_view_favs_cart' => array(
                                'name' => '4-' . __('View Favorites', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('The label to use for link to favorites', 'woo-rfq-for-woocommerce'),
                                'default' => __('View Favorites', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_rfq_favs_wordings_view_favs_cart',
                                'css' => 'width:200px'
                            ),

                            'settings_gpls_woo_rfq_add_to_favorites_label' => array(
                                'name' => '5-' . __('Add to Favorites', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('The label to use for "Add to Favorites" ', 'woo-rfq-for-woocommerce'),
                                'default' => __('Add to Favorites', 'woo-rfq-for-woocommerce'),
                                'id' => 'settings_gpls_woo_rfq_add_to_favorites_label',
                                'css' => 'width:200px'
                            ),
                            'rfq_cart_sc_section_link_to_favorites_page' => array(
                                'name' => '6- ' .__('Request for Favorites Page URL', 'woo-rfq-for-woocommerce'),
                                'type' => 'text',
                                'desc' => __('Favorites Page URL.Please show the full URL. Use the same domain/subdomain name as your site.<br />'
                                    , 'woo-rfq-for-woocommerce'),
                                'default' => '/favorites/',
                                'id' => 'rfq_cart_sc_section_link_to_favorites_page',
                                'css' => 'width:400px;'
                            )

                        );
                    break;
                case 'email-options':
                    $settings = array(


                        'settings_gpls_woo_rfq_admin_email_reply_to' => array(
                            'name' =>  __('Change "reply to" in admin email to customer email', 'woo-rfq-for-woocommerce'),
                            'type' => 'checkbox',
                            'desc' => __('Change "reply to" to the customer billing email in emails sent to admin for convenience.', 'woo-rfq-for-woocommerce'),
                            'default' => 'yes',
                            'id' => 'settings_gpls_woo_rfq_admin_email_reply_to',
                            'css' => 'width:400px;'
                        )
                    );
                    break;



                case 'npoptions':
                    $settings =

                        array(

                            'rfq_cart_sc_section_title' => array(
                                'name' => '',
                                'type' => 'title',
                                'desc' => '',
                                'id' => 'rfq_cart_sc_section_title',
                                'css' => 'width:600px'
                            ),

                            'rfq_cart_sc_section_more_options' => array(
                                'name' => '',
                                'type' => 'gpls_woo_options',
                                'id' => 'rfq_cart_sc_section_more_options',


                            ),

                            'rfq_cart_sc_section_title_end' => array(
                                'type' => 'sectionend',
                                'id' => 'rfq_cart_sc_section_title_end'
                            ),

                        );
                    break;


            }

            $settings = apply_filters('wc_settings_gpls_woo_rfq_settings', $settings, $section);

            return $settings;

        }


        public function ad_filter_menu($sorted_menu_objects, $args)
        {

            if ($args->theme_location != 'primary')
                return $sorted_menu_objects;


            foreach ($sorted_menu_objects as $key => $menu_object) {


                $rfq_page = pls_woo_rfq_get_link_to_rfq();
                if ($menu_object->title == $rfq_page) {
                    unset($sorted_menu_objects[$key]);
                    break;
                }
            }

            return $sorted_menu_objects;
        }


        public function get_page_by_name($pagename)
        {
           // if (get_page_by_title($pagename)) return true;

            $posts = get_posts(
                array(
                    'post_type'              => 'page',
                    'title'                  => $pagename,
                )
            );

            if ( ! empty( $posts ) ) {
                return true;
            }

            return false;
        }


    }

}

if (!isset( $GLOBALS["GPLS_Woo_RFQ_Settings"])) {

     $GLOBALS["GPLS_Woo_RFQ_Settings"] = new GPLS_Woo_RFQ_Settings();
}

return  $GLOBALS["GPLS_Woo_RFQ_Settings"];


?>
