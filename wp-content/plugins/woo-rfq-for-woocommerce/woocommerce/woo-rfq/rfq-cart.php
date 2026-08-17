<?php
/**
 * WOO-RFQ-List

 */
// phpcs:ignoreFile
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<noscript>
    <H1> Javascript is required for this page. Please enable JavaScript to continue.</h1>
</noscript>

<?php
//require_once(ABSPATH . 'wp-settings.php');

$gpls_woo_rfq_cart = gpls_woo_rfq_get_item('gpls_woo_rfq_cart');


?>

<div id="rfq_cart_wrapper" class="rfq_cart_wrapper">

    <?php do_action('gpls_woo_rfq_cart_actions_upload_files'); ?>

    <?php
    $wc_get_update_url = pls_woo_rfq_get_link_to_rfq();
    ?>

    <?php

    if(function_exists('is_product') && is_product()){
        global $product;
        $global_product_id=$product->get_id();
        $location="product";
    }else{
        $global_product_id="";
        $location="rfq_page";
    }


    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_styles']='';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseover']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseover']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseout']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseout']='';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_styles']='';

    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseover']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseover']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseout']='';
    $gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseout']='';

    $gpls_woo_rfq_styles = apply_filters('gpls_woo_rfq_before_cart_gpls_woo_rfq_styles',$gpls_woo_rfq_styles);

    ?>

    <div class="woo_rfq_top_html_desc" >

        <?php do_action('gpls_woo_rfq_request_page_top_html_desc') ; ?>

    </div>

    <?php gpls_woo_rfq_print_notices(); ?>

    <?php do_action('gpls_woo_rfq_before_cart'); ?>

    <form name="rfqform" id="rfqform" class="rfqform" action="<?php echo  ($wc_get_update_url); ?>" method="post" enctype="multipart/form-data">


        <input type="hidden" name="rfqform_location" id="rfqform_location" value="<?php echo  ($location); ?>" />

        <?php if(isset( $global_product_id)): ?>
            <input type="hidden" name="global_product_id" id="global_product_id" value="<?php echo  ($global_product_id); ?>" />
        <?php endif; ?>

        <?php $nonce = wp_create_nonce('gpls_woo_rfq_handle_rfq_cart_nonce') ; ?>
        <div class="woocommerce gpls_woo_rfq_request_page">

            <div class="woocommerce gpls_woo_rfq_request_cart">
                <div style="clear: both"></div>
                <table id="rfq_cart_shop_table" class="shop_table shop_table_responsive cart rfq_cart_shop_table" cellspacing="0" >

                    <tr class="cart_tr">
                        <th class="product-remove cart_th">&nbsp;</th>
                        <th class="product-thumbnail cart_th">&nbsp;</th>
                        <th class="product-name cart_th"><?php printf(  (__('Product', 'woo-rfq-for-woocommerce'))); ?></th>
                        <th class="product-quantity cart_th"><?php printf(  (__('Quantity', 'woo-rfq-for-woocommerce'))); ?></th>

                    </tr>

                    <?php do_action('gpls_woo_rfq_before_cart_contents'); ?>

                    <?php

                    foreach ($gpls_woo_rfq_cart as $cart_item_key => $cart_item) {


                        if($cart_item['data']==null)continue;

                        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                        //d($cart_item);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                        // $_product   = $cart_item['data'];
                        //$product_id = $cart_item['data']['id'];


                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('gpls_woo_woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink() : '', $cart_item, $cart_item_key);
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                <td class="product-remove cart_td ">
                                    <?php

                                    if (isset($cart_item['bundled_by'])) {
                                        echo '';
                                    } else {
                                        $url = esc_url($wc_get_update_url) . "?remove_rfq_item=" . $cart_item_key;
                                        echo  (apply_filters('woocommerce_cart_item_remove_link', sprintf(
                                            '<a href="%s" type="submit" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                            $url . '&man-deleted=' . $cart_item_key."&gpls_woo_rfq_nonce=".$nonce,
                                            (__('Remove this item', 'woo-rfq-for-woocommerce')),
                                            esc_attr($product_id),
                                            esc_attr($_product->get_sku())
                                        ),  ($cart_item_key)));


                                    }

                                    ?>

                                </td>

                                <td class="product-thumbnail cart_td">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                    if ( ! $product_permalink ) {
                                        echo  ($thumbnail);
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ),  ($thumbnail) );
                                    }
                                    ?>
                                </td>

                                <td class="product-name  cart_td" data-title="<?php printf(  (__('Product', 'woo-rfq-for-woocommerce'))); ?>">
                                    <?php
                                    if (!$product_permalink) {
                                        echo  (apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key)) . '&nbsp;';
                                    } else {
                                        echo  (apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_title()), $cart_item, $cart_item_key));
                                    }
                                    //  do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                                    // Meta data

                                    echo    (rfq_cart_get_item_data($cart_item));

                                    do_action('gplsrfq_cart_item_product',$_product, $cart_item, $cart_item_key);

                                    do_action('gpls_woo_rfq_get_product_extra',$_product,$cart_item, $cart_item_key);

                                    // Backorder notification
                                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                        echo '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woo-rfq-for-woocommerce') . '</p>';
                                    }
                                    ?>
                                </td>



                                <td class="product-quantity  cart_td" data-title="<?php echo(  (__('Quantity', 'woo-rfq-for-woocommerce'))); ?>">
                                    <?php


                                    if ($_product->is_sold_individually()) {
                                        $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                    } else if ((isset($cart_item['bundled_by']) && isset($cart_item['bundled_by']))
                                        || (isset($cart_item['bundled_items']) && isset($cart_item['bundled_items']) )
                                    ) {
                                        $product_quantity = sprintf("{$cart_item['quantity']} <input type='hidden' name='cart[%s][qty]' value='{$cart_item['quantity']}' />", $cart_item_key);
                                    }else if ((isset($cart_item['composite_parent']) && isset($cart_item['composite_parent']))
                                        || (isset($cart_item['composite_children']) && isset($cart_item['composite_children']) )
                                    ) {
                                        $product_quantity = sprintf("{$cart_item['quantity']} <input type='hidden' name='cart[%s][qty]' value='{$cart_item['quantity']}' />", $cart_item_key);
                                    }

                                    else {
                                        $product_quantity = woocommerce_quantity_input(array(
                                            'input_name' => "cart[{$cart_item_key}][qty]",
                                            'input_value' => $cart_item['quantity'],
                                            'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                            'min_value' => '0',
                                            ''
                                        ), $_product, false);
                                    }
                                    if ((isset($cart_item['bundled_items']) && isset($cart_item['bundled_items']) )) {
                                        echo '<b style="padding-left: 1.3em">'. ($product_quantity).'</b>';// phpcs:ignore WordPress.Security.EscapeOutput
                                    }else if ((isset($cart_item['composite_children']) && isset($cart_item['composite_children']) )) {
                                        echo '<b style="padding-left: 1.3em">'. ($product_quantity).'</b>';// phpcs:ignore WordPress.Security.EscapeOutput
                                    }
                                    else{
                                        echo $product_quantity; // phpcs:ignore WordPress.Security.EscapeOutput
                                    }


                                    ?>
                                </td>


                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <?php

                    do_action('gpls_woo_rfq_after_cart_contents');

                    ?>

                    <tr class="cart_tr">

                        <td colspan="6" class="actions cart_td">

                            <?php

                             $confirmation_message = get_option('rfq_cart_wordings_gpls_woo_rfq_update_rfq_cart_button',__('Update Quote Request', 'woo-rfq-for-woocommerce'));
                            $confirmation_message = __($confirmation_message,'woo-rfq-for-woocommerce');



                            ?>

                            <div class="update_rfq_cart">

                                <?php do_action('gpls_woo_rfq_cart_actions'); ?>

                                <input   type="submit" class="update-rfq-cart button alt gpls-woo-rfq_update-rfq-cart_button"
                                         id="update_rfq_cart" formnovalidate="formnovalidate" style="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_styles']); ?>"
                                         onmouseover="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseover'])
                                             .';'. ($gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseover']); ?>"
                                         onmouseout="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_onmouseout'])
                                             .';'. ($gpls_woo_rfq_styles['gpls_woo_rfq_page_update_button_background_onmouseout']); ?>"


                                         name="update_rfq_cart" value="<?php echo  ($confirmation_message); ?>"

                                />
                            </div>
                        </td>
                    </tr>





                </table>
            </div>
            <?php

            do_action('gpls_woo_rfq_after_items_list');

            ?>

            <div style="clear:both"></div>
            <div class="rfq_shop_table_customer_info_div">
                <table id="rfq-shop-table_customer_info" class="shop_table cart shop_table_responsive rfq-shop-table_customer_info" cellspacing="1"   cellpadding="1" >

                    <?php

                    do_action('gpls_woo_rfq_before_customer_info_label');

                    ?>

                    <tr class="info_tr">
                        <?php
                        $customer_info_label = get_option('settings_gpls_woo_rfq_customer_info_label','Customer Information');
                        $customer_info_label = __($customer_info_label,'woo-rfq-for-woocommerce');
                        if(!isset($customer_info_label)){
                            $customer_info_label =  (__('Customer Information','woo-rfq-for-woocommerce'));
                        }

                        ?>
                        <td align="center" colspan="4" class="info_td"  style="text-align: center;">
                            <h1 class="woo-rfq-customer-info-header"><?php echo  ($customer_info_label); ?></h1>
                        </td>


                    </tr>

                    <?php do_action('gpls_woo_rfq_cart_actions_customer_bid'); ?>

                    <?php if (function_exists('is_user_logged_in')): ?>
                        <?php if (!is_user_logged_in()): ?>
                            <tr class="info_tr">

                                <th class="FName info_th"><?php printf(  (__('First Name', 'woo-rfq-for-woocommerce'))); ?> <abbr class="required" ></abbr>
                                </th>


                            </tr>
                            <tr class="info_tr">

                                <td class="info_td"><input style=" " type="text" id="rfq_fname" name="rfq_fname" placeholder="<?php printf(  (__('First Name', 'woo-rfq-for-woocommerce'))); ?>"
                                    /></td>
                            </tr>
                            <tr class="info_tr">
                                <th class="LName  info_th"><?php printf(  (__('Last Name', 'woo-rfq-for-woocommerce'))); ?> <abbr class="required" ></abbr>
                                </th>

                            </tr>

                            <tr class="info_tr">
                                <td class="info_td">
                                    <input style=" " type="text" id="rfq_lname" name="rfq_lname" placeholder="<?php printf(  (__('Last Name', 'woo-rfq-for-woocommerce'))); ?>" class="required"  /></td>

                            </tr>
                            <tr class="info_tr">

                                <th class="email  info_th"><?php printf(  (__('Email', 'woo-rfq-for-woocommerce'))); ?> <abbr class="required" ></abbr></th>

                            </tr>

                            <tr class="info_tr">

                                <td class="info_td"><input style=" " id="rfq_email_customer" name="rfq_email_customer" type="email"
                                                           class="email required"   placeholder="<?php printf(  (__('Email', 'woo-rfq-for-woocommerce'))); ?> "    /></td>
                            </tr>

                            <?php if(get_option('rfq_cart_sc_section_rfq_page_phone_hide','no')==='no'): ?>
                                <tr class="info_tr">


                                    <th class="info_th"><?php printf(  (__('Phone', 'woo-rfq-for-woocommerce'))); ?> <abbr  id="rfq_phone_label" ></abbr></th>


                                </tr>
                                <tr class="info_tr">
                                    <td class="info_td"><input style="  !important" id="rfq_phone" name="rfq_phone" placeholder="<?php printf(  (__('Phone', 'woo-rfq-for-woocommerce'))); ?>" type="text"
                                        /></td>

                                </tr>
                            <?php else: ?>
                                <input type="hidden" id="rfq_phone" name="rfq_phone" />
                            <?php endif; ?>

                            <?php if(get_option('rfq_cart_sc_section_rfq_page_company_hide','no')==='no'): ?>

                                <tr class="info_tr">
                                    <th colspan="2" class="company info_th"><?php printf(  (__('Company', 'woo-rfq-for-woocommerce'))); ?> <abbr  id="rfq_company_label"></abbr></th>

                                </tr>


                                <tr class="info_tr">
                                    <td colspan="2" class="company info_td">
                                        <input type="text" id="rfq_company" name="rfq_company" placeholder="<?php printf(  (__('Company', 'woo-rfq-for-woocommerce'))); ?> " class="rfq_cart_address" />
                                    </td>

                                </tr>
                            <?php else: ?>
                                <input type="hidden" id="rfq_company" name="rfq_company" />
                            <?php endif; ?>


                            <?php if(get_option('rfq_cart_sc_section_rfq_page_country_hide','no')==='no'): ?>
                                <tr class="info_tr">
                                    <th colspan="2" class="country info_th" style="padding-top:10px"><?php printf( (__('Country', 'woo-rfq-for-woocommerce'))); ?><abbr class="required"   id="rfq_billing_country_label"></abbr></th>

                                </tr>


                                <tr class="info_tr">
                                    <td colspan="2" style="padding:15px;" class="rfq_state_select info_td" placeholder="<?php printf( (__('Country', 'woo-rfq-for-woocommerce'))); ?>">

                                        <?php
                                        $countries = new WC_Countries();
                                        $allowed_countries = $countries->get_allowed_countries();
                                        //$allowed_countries = $countries->get_countries();
                                        ?>
                                        <select name="rfq_billing_country" id="rfq_billing_country"
                                                class="rfq_billing_country" style="padding:5px;font-size: 1.em;" data-msg="<?php printf(  (__('Required', 'woo-rfq-for-woocommerce'))); ?>">
                                            <option value="">Select a country&hellip;</option>

                                            <?php
                                            foreach ($allowed_countries as $key => $value) { ?>
                                                <option value="<?php echo  ($key); ?>"><?php echo  ($value); ?></option>
                                                <?php
                                            }
                                            ?>

                                        </select>

                                    </td>

                                </tr>

                            <?php else: ?>
                                <input type="hidden" id="rfq_billing_country" name="rfq_billing_country" />
                            <?php endif; ?>


                            <?php if(get_option('rfq_cart_sc_section_rfq_page_state_hide','no')==='no'):
                                ?>

                                <tr class="info_tr">

                                    <th class="info_th" style="padding-top:10px"><?php printf(  (__('State', 'woo-rfq-for-woocommerce'))); ?><abbr class="required"  id="rfq_state_select_label"></abbr></th>


                                </tr>

                                <tr class="info_tr">

                                    <td colspan="2" style="padding:10px;" class="info_td">
                                        <select name="rfq_state_select" id="rfq_state_select" style="padding:5px;font-size: 1.em;"
                                                class="rfq_state_select" placeholder="<?php printf( (__('State','woo-rfq-for-woocommerce')))?>" style=" vertical-align: top" >
                                            <option value="">Select an option…</option>
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="DC">District Of Columbia</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                            <option value="AA">Armed Forces (AA)</option>
                                            <option value="AE">Armed Forces (AE)</option>
                                            <option value="AP">Armed Forces (AP)</option>
                                            <option value="AS">American Samoa</option>
                                            <option value="GU">Guam</option>
                                            <option value="MP">Northern Mariana Islands</option>
                                            <option value="PR">Puerto Rico</option>
                                            <option value="UM">US Minor Outlying Islands</option>
                                            <option value="VI">US Virgin Islands</option>
                                        </select>
                                        <input style="  " class="form-control" name="rfq_state_text" id="rfq_state_text" type="text"
                                               placeholder="<?php printf(  (__('State/County','woo-rfq-for-woocommerce'))); ?>"/>
                                    </td>

                                </tr>
                            <?php else: ?>
                                <input type="hidden" id="rfq_state_select" name="rfq_state_select" />
                            <?php
                            endif; ?>

                            <?php if(get_option('rfq_cart_sc_section_rfq_page_address_hide','no')==='no'): ?>

                                <tr class="info_tr">
                                    <th colspan="2" class="address info_td"><?php printf(  (__('Address', 'woo-rfq-for-woocommerce'))); ?><abbr  id="rfq_address_label"></abbr></th>

                                </tr>
                                <tr class="info_tr">
                                    <td colspan="2" class="address info_td">
                                        <input style="  " type="text" id="rfq_address" name="rfq_address" placeholder="<?php printf(  (__('Address', 'woo-rfq-for-woocommerce'))); ?>" class="rfq_cart_address" />
                                    </td>

                                </tr>

                            <?php else: ?>
                                <input type="hidden" id="rfq_address" name="rfq_address" />
                            <?php endif; ?>

                            <?php if(get_option('rfq_cart_sc_section_rfq_page_address_hide','no')==='no'): ?>

                                <tr class="info_tr">
                                    <th colspan="2" class="address info_td info_th"><?php printf(  (__('Address 2', 'woo-rfq-for-woocommerce'))); ?><abbr  id="rfq_address2_label"></abbr></th>

                                </tr>
                                <tr class="info_tr">
                                    <td colspan="2" class="address info_td">
                                        <input style="  " type="text" id="rfq_address2" name="rfq_address2" placeholder="Apartment, suite, etc."
                                               class="rfq_cart_address" />
                                    </td>

                                </tr>
                            <?php else: ?>
                                <input type="hidden" id="rfq_address2" name="rfq_address2" />
                            <?php endif; ?>


                            <?php if(get_option('rfq_cart_sc_section_rfq_page_city_hide','no')==='no'): ?>

                                <tr class="info_tr">

                                    <th class="info_th"><?php printf(  (__('City', 'woo-rfq-for-woocommerce'))); ?><abbr  id="rfq_city_label"></abbr></th>

                                </tr>


                                <tr class="info_tr">

                                    <td class="text info_td"><input style="  " class="form-control" type="text" id="rfq_city" name="rfq_city"
                                                                    placeholder="<?php printf(  (__('City', 'woo-rfq-for-woocommerce'))); ?>"/></td>
                                </tr>

                            <?php else: ?>
                                <input type="hidden" id="rfq_city" name="rfq_city" />
                            <?php endif; ?>

                            <?php if(get_option('rfq_cart_sc_section_rfq_page_zip_hide','no')==='no'): ?>

                                <tr class="info_tr">

                                    <th class="info_th"><?php printf(  (__('Zip', 'woo-rfq-for-woocommerce'))); ?><abbr  id="rfq_zip_label"></abbr></th>

                                </tr>
                                <tr class="info_tr">
                                    <td class="info_td" >
                                        <input style="  " class="form-control" name="rfq_zip" id="rfq_zip" type="text" placeholder="<?php printf(  (__('Zip', 'woo-rfq-for-woocommerce'))); ?>"
                                        />
                                    </td>

                                </tr>
                            <?php else: ?>
                                <input type="hidden" id="rfq_zip" name="rfq_zip" />
                            <?php endif; ?>



                        <?php endif; ?>
                    <?php endif; ?>

                    <?php do_action('gpls_woo_rfq_cart_actions_ninja'); ?>

                    <?php if (function_exists('is_user_logged_in')): ?>
                        <?php if (is_user_logged_in()): ?>
                            <input type="hidden" id="rfq_fname" name="rfq_fname" value="rfq_fname"/>
                            <input type="hidden" id="rfq_lname" name="rfq_lname" value="rfq_lname" />
                            <input type="hidden" id="rfq_email_customer" name="rfq_email_customer" value="rfq_email_customer" />
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(get_option('rfq_cart_sc_section_rfq_page_comment_hide','no')=='no'): ?>
                        <tr class="info_tr">

                            <th class="info_th"><?php printf(  (__('Customer Note', 'woo-rfq-for-woocommerce'))); ?><abbr  id="rfq_message_label"></abbr></th>

                        </tr>

                        <tr class="info_tr">
                            <td colspan="4"  style="" class="info_td">
                                <textarea id="rfq_message" name="rfq_message" placeholder="<?php printf(  (__('Your message to us', 'woo-rfq-for-woocommerce'))); ?>" rows="5" class="rfq-cart-message"   ></textarea>
                            </td>

                        </tr>

                    <?php else: ?>
                        <input type="hidden" id="rfq_message" name="rfq_message" />
                    <?php endif; ?>

                    <?php
                    if(function_exists('is_user_logged_in')) {
                        if (!is_user_logged_in()) {
                            do_action('gpls_woo_create_an_account');
                        }
                    }

                    ?>

                    <tr class="info_tr">
                        <td colspan="2" align="center" class="info_td"  style="text-align: center !important;" >
                            <input type="hidden" name="gpls_woo_rfq_nonce" value='<?php echo  ($nonce); ?>'>
                            <?php
                            $button_text = get_option('rfq_cart_wordings_submit_your_rfq_text', 'Submit Your Request For Quote');
                            $button_text = __($button_text,'woo-rfq-for-woocommerce');

                            $button_text = apply_filters('gpls_woo_rfq_rfq_submit_your_order_text',$button_text);

                            ?>



                            <?php do_action('gpls_woo_woocommerce_before_rfqcart_submit'); ?>


                            <div class="rfq_proceed-to-checkout" >
                                <input  name="gpls-woo-rfq_checkout_button" id="gpls-woo-rfq_checkout_button"
                                        type="submit" class="button alt gpls-woo-rfq_checkout_button"
                                        style="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_styles']) ?>" value="<?php echo  ($button_text); ?>"
                                        onmouseover="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseover']).';'. ($gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseover']); ?>"
                                        onmouseout="<?php echo  ($gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_onmouseout']).';'. ($gpls_woo_rfq_styles['gpls_woo_rfq_page_submit_button_background_onmouseout']); ?>"

                                />
                                <br />
                                <input type="hidden" id="rfq_checkout" name="rfq_checkout" value="true"/>
                                <input type="hidden" id="gpls-woo-rfq_checkout" name="gpls-woo-rfq_checkout" value="false"/>
                                <input type="hidden" id="gpls-woo-rfq_update" name="gpls-woo-rfq_update" value="false"/>


                            </div>

                        </td>

                    </tr>


                </table>

                <?php
                $base_address = get_option('woocommerce_default_country', '' );

                $address_billing=array("","");

                if(isset($base_address)){
                    $address_billing = explode(':',$base_address,2);
                }

                $script = "

    jQuery('#rfq_billing_country').val('".$address_billing[0]."');
    

    if( jQuery('#rfq_billing_country').val() =='US'){
                 jQuery('#rfq_state_text').val('');
                 jQuery('#rfq_state_text').attr('required',false);
     }else{
                 jQuery('#rfq_state_select').val('');
                  jQuery('#rfq_state_select').attr('required',false);                 
     }    
";



                echo "<div class='gpls_script' style='display: none'><script> " .  ($script) . '</script></div>';



                ?>


            </div>

            <?php do_action('gpls_woo_woocommerce_after_cart_table'); ?>


        </div>
    </form>
    <div style="clear: both"></div>

    <div class="woo_rfq_bottom_html_desc" >
        <?php
        do_action('gpls_woo_rfq_request_page_bottom_html_desc');




        ?>
    </div>

</div>



