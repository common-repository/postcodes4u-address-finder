<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 *
 *   Modified by 3X Software for Postcodes 4u Lookup. 30/07/2021
 *   Adapted from version 3.6.0 form
 *
 *   Updated for Postcodes 4u Lookup. 05/01/2023
 *      Adapted from version 7.0.1 form
 *
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Postcodes4u Options
global $pc4u_options;


$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Billing address', 'woocommerce' ) : esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<form method="post">

		<h3><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h3><?php // @codingStandardsIgnoreLine ?>

		<div class="woocommerce-address-fields">
                    <?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
                    
                    <div class="woocommerce-address-fields__field-wrapper">
                    <?php
                        //    ======================================= START ===
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
                                    $pc4uPosition='company';
                                    $pc4uPositionPriority=25;
                                } else {
                                    if($pc4u_options['woocommerce_position'] == '2') {
                                        $pc4uPosition='address_1';
                                        $pc4uPositionPriority=45;
                                    } else {
                                        if($pc4u_options['woocommerce_position'] == '3') {
                                            $pc4uPosition='address_2';
                                            $pc4uPositionPriority=55;
                                        } else {
                                            if($pc4u_options['woocommerce_position'] == '4') {
                                                $pc4uPosition='state';
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

                            foreach ( $address as $key => $field ) {
                                if($pc4uDisplayed=='no' && ($pc4uPosition=='top' || substr_compare($key, $pc4uPosition, -strlen($pc4uPosition)))) {

                                    // Check for Billing Postcode
                                    $postcodeFieldKey =  'billing_postcode';
                                    if (isset($address[$postcodeFieldKey])) {
                                        $postcodeField =  $address[$postcodeFieldKey];

                                         // Add Billing Postcode Field - Set as Half Width
                                        $postcodeField['class'] = array('form-row-first');
                                        $postcodeField['priority'] = $pc4uPositionPriority;


                                        woocommerce_form_field( $postcodeFieldKey, $postcodeField, wc_get_post_data_by_key( $postcodeFieldKey, $postcodeField['value'] ) );
                                        //  ORG -- woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                                        // woocommerce_form_field( $postcodeFieldKey, $postcodeField, $checkout->get_value( $postcodeFieldKey ) );
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
                                            <div id="pc4uselect_text" style="display:none"><?php echo $pc4uAddressSelectDropdownText;?></div>
                                        </div>
                                <?php
                                        $pc4uDisplayed='yes';
                                    } else {
                                        // Check for Shipping Postcode
                                        $postcodeFieldKey =  'shipping_postcode';
                                        if (isset($address[$postcodeFieldKey])) {
                                            $postcodeField =  $address[$postcodeFieldKey];

                                            // Add Shipping Postcode Field - Set as Half Width
                                            $postcodeField['class'] = array('form-row-first', 'address-field', 'pc4uWooPostcode');
                                            $postcodeField['priority'] = $pc4uPositionPriority;

                                            woocommerce_form_field( $postcodeFieldKey, $postcodeField, wc_get_post_data_by_key( $postcodeFieldKey, $postcodeField['value'] ) );
                                            //  ORG -- woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                                            // woocommerce_form_field( $postcodeFieldKey, $postcodeField, $checkout->get_value( $postcodeFieldKey ) );
                                ?>
                                            <p class= "pc4uWooLookup form-row form-row-last">
                                              <label class="pc4uWooLookupLabel">&nbsp;</label>
                                              <input onclick="Pc4uWooSearchShippingBegin(); return false;" type="submit" value="<?php echo $pc4uLookupButtonText?>" id="Pc4uShippingLookup" class = "Pc4uLookup " name="wooShipping" />
                                            </p>
                                            <p class= "form-row" >
                                              <select id="pc4uWooShippingDropdown"  class = "pc4uWooDropdown Pc4uDropdown" style="display: none;" onchange="Pc4uSearchIdBegin('pc4uWooShipping')"><option>Select an address:</option></select>
                                            </p>
                                            <div class="clear">
                                                <div id="postcodes4ukey" style="display: none;" ><?php echo trim($pc4u_options['user_key']);?></div>
                                                <div id="postcodes4uuser" style="display: none;" ><?php echo trim($pc4u_options['user_name']);?> </div>
                                                <div id="pc4ualt_address_disp" style="display: none;" ><?php echo $pc4u_options['alt_address_disp'];?> </div>
                                                <div id="pc4ucounty_disp" style="display: none;" ><?php echo $pc4u_options['county_address_disp'];?> </div>
                                                <div id="pc4ushow_warnings" style="display:none"><?php echo $pc4uShowWarnings;?></div>
                                                <div id="pc4uselect_text" style="display:none"><?php echo $pc4uAddressSelectDropdownText;?></div>
                                            </div>
                        <?php
                                            $pc4uDisplayed='yes';
                                        } else {


                                        }
                                    }
                                }

                                // Ensure Postcode Field NOT Displayed
                                if($key != "billing_postcode" && $key != "shipping_postcode" ) {
                                    // Normal Field Processing
                                    woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                                }
                            }
                            //pc4u Extra Spacing ??
                        ?>
                            <p class= "form-row form-row-wide">&nbsp;</p>
                    <?php
                        } else {
                            // ------------------------------------------------------------------
                            // Normal Address Form Behavior (Default WooCommerce processing)
                            // ------------------------------------------------------------------
                            foreach ( $address as $key => $field ) {
                                woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                            }
                            // ------------------------------------------------------------------
                        }
                    ?>
                  
		</div>

		<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
		<p>
<!--                    <button type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>-->
                    <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
                    <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
                    <input type="hidden" name="action" value="edit_address" />
		</p>
            </div>

	</form>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
