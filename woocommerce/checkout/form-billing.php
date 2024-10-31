<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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
 *  Modified by 3XSoftware for Postcodes 4u Lookup.  05/01/2018 - 30/07/2021
 *   Adapted from version 2.1.2/3.6.0 form
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Postcodes4u Options
global $pc4u_options;


?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
        <?php

        // Check If Woo Commerce Postcodes4u Address lookup required
        if (isset($pc4u_options) && isset($pc4u_options['woointegrate']) &&
             ($pc4u_options['woointegrate'] == true || $pc4u_options['woointegrate'] == '1')) {
            // Custom Billing Form Processing for Postcodes4u Lookup
            $pc4uDisplayed="no";

                // Set Postcodes4u Lookup Form Location
                //    New Position IDs 10-1st Name, 20-Last Name, 30-Company,
                //      40-Country, 50-Street1,60-Street 2(appt), 70-TownCity, 80-CountyState, 90-ostcode
                $pc4uPosition="top";
                $pc4uPositionPriority=5;
                if(isset($pc4u_options['woocommerce_position'])){
                    if($pc4u_options['woocommerce_position'] == '1' ) {
                        $pc4uPosition='billing_company';
                        $pc4uPositionPriority=25;
                    } else {
                        if($pc4u_options['woocommerce_position'] == '2') {
                            $pc4uPosition='billing_address_1';
                            $pc4uPositionPriority=45;
                        } else {
                            if($pc4u_options['woocommerce_position'] == '3') {
                                $pc4uPosition='billing_address_2';
                                $pc4uPositionPriority=55;
                            } else {
                                if($pc4u_options['woocommerce_position'] == '4') {
                                    $pc4uPosition='billing_state';
                                    $pc4uPositionPriority=75;
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


                $fields = $checkout->get_checkout_fields( 'billing' );


                foreach ( $fields as $key => $field ) {
                    if($pc4uDisplayed=='no' && ($pc4uPosition=='top' || $key == $pc4uPosition)) {
                        $postcodeFieldKey =  'billing_postcode';
                        $postcodeField =  $fields[$postcodeFieldKey];
                        if($postcodeField) {
                            // Add Postcode Field - Set as Half Width
                            $postcodeField['class'] = array('form-row-first', 'address-field', 'pc4uWooPostcode');
                            $postcodeField['priority'] = $pc4uPositionPriority;

                            woocommerce_form_field( $postcodeFieldKey, $postcodeField, $checkout->get_value( $postcodeFieldKey ) );
                        } else {
                            ?>
                            <p class= "pc4uWooPostcode form-row form-row-first ">
                                <label for="billing_postcode" class="">Billing Postcode</label>
                                <input type="text" class="input-text " name="billing_postcode" id="billing_postcode" placeholder=""  value="" max-width:80% />
                            </p>
                            <?php
                        }
                        ?>
                            <p class= "pc4uWooLookup form-row form-row-last">
                                <label class="pc4uWooLookupLabel">&nbsp;</label>
                                <input onclick="Pc4uWooSearchBillingBegin(); return false;" type="submit" value="<?php echo $pc4uLookupButtonText?>" id="Pc4uBillingLookup" class = "pc4uWooLookup Pc4uLookup " name="wooBilling" />
                            </p>
                            <p class= "form-row" >
                                <select id="pc4uWooBillingDropdown"  class = "pc4uWooDropdown Pc4uDropdown" style="display: none;" onchange="Pc4uSearchIdBegin('pc4uWooBilling')"><option>Select an address:</option></select>
                            </p>
                            <div class="clear">
                                <div id="postcodes4ukey" style="display: none;" ><?php echo trim($pc4u_options['user_key']);?></div>
				<div id="postcodes4uuser" style="display: none;" ><?php echo trim($pc4u_options['user_name']);?> </div>
				<div id="pc4ualt_address_disp" style="display: none;" ><?php echo $pc4u_options['alt_address_disp'];?> </div>
                                <div id="pc4ucounty_disp" style="display: none;" ><?php echo $pc4u_options['county_address_disp'];?> </div>
                                <div id="pc4ushow_warnings" style="display:none"><?php echo $pc4uShowWarnings;?></div>
                                <div id="pc4uselect_text" style="display:none"><?php echo $pc4uAddressSelectDropdownText ;?></div>
                            </div>
			 <?php
			 $pc4uDisplayed='yes';
                    }
                            // Ensure Postcode Field NOT Displayed
                            if($key != "billing_postcode") {
                                // Normal Field Processing
                                woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
                            }
			}
			//pc4u Extra Spacing ??
			?>
				<p class= "form-row form-row-wide">&nbsp;</p>
	    		<?php

                    } else {
                        // ------------------------------------------------------------------
                        // Normal Billing Form Behavior (Default WooCommerce processing)
                        // ------------------------------------------------------------------
                        $fields = $checkout->get_checkout_fields('billing');
			foreach ( $fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
                        // ------------------------------------------------------------------
                    }
		?>
	</div>

        <?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>

</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
                            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                                <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
                            </label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
