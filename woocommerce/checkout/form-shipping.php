<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 *
 *  Modified by 3XSoftware for Postcodes 4u Lookup.  05/01/2018 - 30/07/2021
 *   Adapted from version 2.2.0/3.6.0 form
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Postcodes4u Options
global $pc4u_options;

?>
<div class="woocommerce-shipping-fields">
<?php if (true === WC()->cart->needs_shipping_address()) : ?>

        <h3 id="ship-to-different-address">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> <span><?php esc_html_e( 'Ship to a different address?', 'woocommerce' ); ?></span>
            </label>
        </h3>

        <div class="shipping_address">

    <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

            <div class="woocommerce-shipping-fields__field-wrapper">
    <?php
    // Check If Woo Commerce Postcodes4u Address lookup required
    if (isset($pc4u_options) && isset($pc4u_options['woointegrate']) &&
            ($pc4u_options['woointegrate'] == true || $pc4u_options['woointegrate'] == '1')) {
        // Do Postcode4u Lookup Processing - Add Postcode Field
        $pc4uShipDisplayed = "no";

        // Set Postcodes4u Lookup Form Location
        //    New Position IDs 10-1st Name, 20-Last Name, 30-Company,
        //      40-Country, 50-Street1,60-Street 2(appt), 70-TownCity, 80-CountyState, 90-ostcode
        $pc4uShipPosition="top";
        $pc4uShipPositionPriority= 4;
        if(isset($pc4u_options['woocommerce_position'])){
            if($pc4u_options['woocommerce_position'] == '1' ) {
                $pc4uShipPosition='shipping_company';
                $pc4uShipPositionPriority=24;
            } else {
                if($pc4u_options['woocommerce_position'] == '2') {
                    $pc4uShipPosition='shipping_address_1';
                    $pc4uShipPositionPriority=44;
                } else {
                    if($pc4u_options['woocommerce_position'] == '3') {
                        $pc4uShipPosition='shipping_address_2';
                        $pc4uShipPositionPriority=54;
                    } else {
                        if($pc4u_options['woocommerce_position'] == '4') {
                            $pc4uShipPosition='shipping_state';
                            $pc4uShipPositionPriority=74;
                        }
                    }
                }
            }
        }

        // -- Show Warnings Flag - KRF 2021.07.30
        $pc4uShowWarnings = '';
        if(isset($pc4u_options['show_warnings'])) {
            $pc4uShowWarnings = $pc4u_options["show_warnings"];
        }

        // Customisable Button Text - Added 2021.07.30
        $pc4uLookupButtonText = "Lookup";
        if(isset($pc4u_options['lookup_button_text'])) {
            $pc4uLookupButtonText = trim($pc4u_options["lookup_button_text"]);
            if($pc4uLookupButtonText == '') {
                $pc4uLookupButtonText = 'Lookup';
            }
        }

        // Customisable Select Address Dropdown  Text - Added 2021.07.30
        $pc4uAddressSelectDropdownText = "Select an address:";
        if(isset($pc4u_options['address_select_dropdown_text'])) {
            $pc4uAddressSelectDropdownText = trim($pc4u_options["address_select_dropdown_text"]);
            if($pc4uAddressSelectDropdownText == '') {
                $pc4uAddressSelectDropdownText =  "Select an address:";
            }
        }
        // --------------------------------------------------------------------


        $fields = $checkout->get_checkout_fields('shipping');
        foreach ($fields as $key => $field) {
            if ($pc4uShipDisplayed == 'no' && ($pc4uShipPosition == 'top' || $key == $pc4uShipPosition)) {
                $postcodeFieldKey =  'shipping_postcode';
                $postcodeShippingField =  $fields[$postcodeFieldKey];

                if($postcodeShippingField) {
                    // Add Postcode Field - Set as Half Width
                    $postcodeShippingField['class'] = array('form-row-first', 'address-field', 'pc4uWooPostcode');
                    // Add New 'Priority' Value
                    $postcodeShippingField['priority'] = $pc4uShipPositionPriority;
                    woocommerce_form_field( $postcodeFieldKey, $postcodeShippingField, $checkout->get_value( $postcodeFieldKey ) );
                } else {
                    // No Field In Array - Add One Anyway
                ?>
                    <p class= "form-row form-row form-row-first pc4uWooDropdown">
                        <label for="shipping_postcode" class="">Shipping Postcode</label>
                        <input type="text" class="input-text " name="shipping_postcode" id="shipping_postcode" placeholder=""  value=""  />
                    </p>
                 <?php
                 }
                 ?>
                    <p class= "form-row form-row form-row-last">
                        <label class="pc4uWooLookupLabel">&nbsp;</label>
                        <input onclick="Pc4uWooSearchShippingBegin(); return false;" type="submit" value="<?php echo $pc4uLookupButtonText?>" id="Pc4uShippingLookup" class = "pc4uWooLookup Pc4uLookup" name="wooShipping" />
                    </p>
                    <p class= "form-row" >
                        <select id="pc4uWooShippingDropdown"  class = "pc4uWooDropdown Pc4uDropdown" style="display: none;" onchange="Pc4uSearchIdBegin('pc4uWooShipping')"><option>Select an address:</option></select>
                    </p>
                    <div class="clear">
                        <div id="postcodes4ukey" style="display: none;" ><?php echo trim($pc4u_options['user_key']); ?></div>
                        <div id="postcodes4uuser" style="display: none;" ><?php echo trim($pc4u_options['user_name']); ?> </div>
                        <div id="pc4ualt_address_disp" style="display: none;" ><?php echo $pc4u_options['alt_address_disp']; ?> </div>
                        <div id="pc4ucounty_disp" style="display: none;" ><?php echo $pc4u_options['county_address_disp'];?> </div>
                        <div id="pc4ushow_warnings" style="display:none"><?php echo $pc4uShowWarnings;?></div>
                        <div id="pc4uselect_text" style="display:none"><?php echo $pc4uAddressSelectDropdownText;?></div>
                    </div>
                <?php
                $pc4uShipDisplayed = 'yes';
            }

            if ($key != "shipping_postcode") {
                // WooCommerce Normal Field - Special Processing for Country
                woocommerce_form_field($key, $field, $checkout->get_value($key));
            }
        }
        ?>
                    <p class= "form-row form-row form-row-wide">&nbsp;</p>
                    <?php
    } else {
        // ------------------------------------------------------------------
        // Normal WooCommerce Shiping Field Processing
        // ------------------------------------------------------------------
        $fields = $checkout->get_checkout_fields('shipping');

        foreach ($fields as $key => $field) {
            woocommerce_form_field($key, $field, $checkout->get_value($key));
        }
    }
    ?>
            </div>

                <?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

        </div>
        <?php endif; ?>
</div>
<div class="woocommerce-additional-fields">
    <?php do_action('woocommerce_before_order_notes', $checkout); ?>

    <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

        <?php if (!WC()->cart->needs_shipping() || wc_ship_to_billing_address_only()) : ?>

            <h3><?php esc_html_e('Additional information', 'woocommerce'); ?></h3>

    <?php endif; ?>

        <div class="woocommerce-additional-fields__field-wrapper">
        <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
        <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    <?php do_action('woocommerce_after_order_notes', $checkout); ?>
</div>
