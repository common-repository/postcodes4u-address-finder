<?php
/** POSTCODES4u - Contact Form 7 Integration ****************************************************
 * Description: Adds Contact Form 7 Shortcode For Postcodes4u Lookup
 *  Updated 2018.04.13 - Allow Multiple CF7 forms
 *                       Allow Multiple Lookups With Forms
 *
 *  Updated 2018.08.10 - Improved multiple form handling.
 *                       Improved support for Older Edge & IE Browsers
 *
 *  Updated 2019.07.04 - 'Trimmed' Key & Username to stop extra space on Key
 *
 *  Updated 2020.01.09 - Updated Multi Form Processing to work with Material Design
 *
 *  Updated 2020.12.11 - Added 'Default Get' support To Postcode Field
 *
 *  Updated 2020.01.07 - Stop Propagation Of Onclick Event on CF7 Postcocde Lookup
 *
 *  Updated 2021.04.01 - Allow Postcodes4u Lookup Button When CF7 'Acceptance' Checkbox is used
 *
 *  Updated 2021.07.30 - Added Customisable Postcode Lookup Button Text
 * 
 *  Updated 2021.09.21 - Fix "default:get2 error - Added Isset Test
 *
 *    Version: 1.6.4
 * Author: Kevin Fisher - 3X Software
 * Plugin URI: plugins.3xsoftware.co.uk/wordpress-uk-address-finder
 ************************************************************************************************/

/* Ensure Means exists to check Plugins Present */
if ( ! defined( 'ABSPATH' ) ) {
    die( "You can't do anything by accessing this file directly." );
}
if ( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

add_action( 'plugins_loaded', 'pc4u_ContactForm7integration_init' , 20 );


function pc4u_ContactForm7integration_init() {

    /* Include Postcodes4u Options */
    global $pc4u_options;

    if(isset($pc4u_options)) {
        // Check for Postcodes4u Loaded and Required
        if(isset($pc4u_options['cf7integrate']) && ($pc4u_options['cf7integrate'] == true || $pc4u_options['cf7integrate'] == '1')) {
            // Check For CF7 Loaded & Active
            if ( is_plugin_active('contact-form-7/wp-contact-form-7.php') ) {
                // Add Postcode ShortCodes
                add_action( 'wpcf7_init', 'wpcf7_pc4u_add_formtags_pc4upostcode' );

                if(isset($pc4u_options['cf7AllowAutoP']) && ($pc4u_options['cf7AllowAutoP'] == true || $pc4u_options['cf7AllowAutoP'] == '1')) {
                    // Allow Default AutoP
                } else {
                    // STOP Contact Form 'Auto <p>'

                    //add_filter('wpcf7_autop_or_not', '__return_false');
                }
            }
        }
    }
}

function wpcf7_pc4u_add_formtags_pc4upostcode() {
    // Added to resolve a 'wpcf7_add_form_tag Not Found' error 2018.08.09 - KRF
    try {
        wpcf7_add_form_tag( array( 'pc4upostcode', 'pc4upostcode*'), 'wpcf7_pc4upostcode_formtag_handler', array( 'name-attr' => true ));
    }
    catch(Exception $e) {
        // Do Nothing
    }
}


/* Validation filter */
add_filter( 'wpcf7_validate_pc4upostcode', 'wpcf7_pc4upostcode_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_pc4upostcode*', 'wpcf7_pc4upostcode_validation_filter', 10, 2 );


function wpcf7_pc4upostcode_formtag_handler( $tag ) {


	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) ) {
		return '';
	}

        $postcodeTagName = $tag->name;
        $postcodeTagType = $tag->type;
        $postcodeTagBase = $tag->basetype;

        $postcodePlaceHolderText = "";
        $postcodeDefaultValue = "";
        $postcodeDefaultValueText = "";
        if($tag->options && count($tag->options) > 0) {
            $optCount = 0;
            foreach ($tag->options as $value) {
                if($value == "default:get") {
                     // Added Isset Test 2021.09.21 - KRF
                     $postcodeDefaultValueText = isset($_GET[$postcodeTagName]) ? $_GET[$postcodeTagName] : '';                     
                } else {
                    if($value == "placeholder") {
                        if($tag->raw_values) {
                            if($optCount < count($tag->raw_values)) {
                                $postcodePlaceHolderText = $tag->raw_values[$optCount];
                            }
                        }
                    }
                }
            }
        }

        // Set Placeholder if supplied
        if(!empty($postcodePlaceHolderText)){
            $postcodePlaceHolderText = ' placeholder= "'.$postcodePlaceHolderText.'"';
        }
        // Set Default Value if supplied in Url
        if(!empty($postcodeDefaultValueText)){
            $postcodeDefaultValue = ' value= "'.$postcodeDefaultValueText.'"';
        }

        /* Include Postcodes4u Options */
        global $pc4u_options;


	$validation_error = wpcf7_get_validation_error( $postcodeTagName );

	$class = wpcf7_form_controls_class( $postcodeTagType);

	if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->is_required() || substr($tag->name, -1) == '*' ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$atts = wpcf7_format_atts( $atts );

        $pc4uKey = trim($pc4u_options["user_key"]);
        $pc4uUser = trim($pc4u_options["user_name"]);
        $pc4uAltAddressDisp = $pc4u_options["alt_address_disp"];
        $pc4uCountyDisp = $pc4u_options["county_address_disp"];
        $pc4uShowWarnings = '';

        // -- Show Warnings Flag - KRF 2021.07.30
        if(isset($pc4u_options['show_warnings'])) {
            $pc4uShowWarnings = $pc4u_options["show_warnings"];
        }


        // Customisable Button Text - Added 2021.07.29
        $pc4uLookupButtonText = "Lookup";
        $pc4uAddressSelectDropdownText = "Select an address:";
        $pc4uManualAddressButtonText = "Manual Address";
        
        if(isset($pc4u_options['lookup_button_text'])) {
            $pc4uLookupButtonText = trim($pc4u_options["lookup_button_text"]);
            if($pc4uLookupButtonText == '') {
                $pc4uLookupButtonText = 'Lookup';
            }
        }
        
        if(isset($pc4u_options['address_select_dropdown_text'])) {
            $pc4uAddressSelectDropdownText = trim($pc4u_options["address_select_dropdown_text"]);
            if($pc4uAddressSelectDropdownText == '') {
                $pc4uAddressSelectDropdownText = "Select an address:";
            }
        }
        if(isset($pc4u_options['manual_address_button_text'])) {   
            $pc4uManualAddressButtonText = trim($pc4u_options["manual_address_button_text"]);
            if($pc4uManualAddressButtonText == '') {
                $pc4uManualAddressButtonText = 'Manual Address';
            }
        }
        // --------------------------------------------------------------------
        
        
        
        
        $html = sprintf(
            '<span style="display:block" class="Pc4uCF7lookupControls">

                <span class="wpcf7-form-control-wrap '.$postcodeTagName.'">
                    <input id="'.$postcodeTagBase. "_" . $postcodeTagName .'" name="'.$postcodeTagName.'" type="text"  size="20" ' . $postcodeDefaultValue . ' ' .$postcodePlaceHolderText . '  style="text-transform:uppercase;"
                         />
                    <button type="button" id="pc4uCF7Lookup_'.$postcodeTagName.'"onclick="event.stopImmediatePropagation(); Pc4uCF7SearchBegin(this.closest(\'.pc4uCf7AddrForm\'),this.closest(\'fieldset\'),this.form,\''. $postcodeTagName. '\', \''.$postcodeTagBase. "_" . $postcodeTagName . '\');return false;" class="Pc4uCF7lookup wpcf7-form-control" style="display:inline-block;margin-top:4px"  >' . $pc4uLookupButtonText . '</button>

						                     <!-- ORG Button - With wpcf7-submit Styling - KRF 2020.03.31 - Pre v1.6.1 -->
						                     <!-- <button type="button" id="pc4uCF7Lookup_'.$postcodeTagName.'"onclick="event.stopImmediatePropagation(); Pc4uCF7SearchBegin(this.closest(\'.pc4uCf7AddrForm\'),this.closest(\'fieldset\'),this.form,\''. $postcodeTagName. '\', \''.$postcodeTagBase. "_" . $postcodeTagName . '\');return false;" class="Pc4uCF7lookup wpcf7-form-control wpcf7-submit" style="display:inline-block;margin-top:4px"  >Lookup</button>   -->
                </span>

                <span class="wpcf7-form-control-wrap">
                    <select id="pc4uCF7Dropdown_'.$postcodeTagName.'" name="pc4uCF7Dropdown" class="Pc4uCF7DropDown wpcf7-form-control wpcf7-select" onchange="Pc4uSearchIdCf7Begin(this.closest(\'.pc4uCf7AddrForm\'), this.closest(\'fieldset\'),this.form)"
                         style="display:none; margin-top:4px;" readonly="true"  ><option>' . $pc4uAddressSelectDropdownText. '</option></select>


                    <div class="clear">
                        <div id="postcodes4ukey" style="display:none">'.$pc4uKey.'</div>
                        <div id="postcodes4uuser" style="display:none">'.$pc4uUser.'</div>
                        <div id="pc4ualt_address_disp" style="display:none">'.$pc4uAltAddressDisp.'</div>
                        <div id="pc4ucounty_disp" style="display:none">'.$pc4uCountyDisp.'</div>
                        <div id="pc4ushow_warnings" style="display:none">' . $pc4uShowWarnings . '</div>
                        <div id="pc4uselect_text" style="display:none">' . $pc4uAddressSelectDropdownText . '</div>
                    </div>
                </span>
            </span>
            <script>



(function (ElementProto) {
	if (typeof ElementProto.matches !== "function") {
		ElementProto.matches = ElementProto.msMatchesSelector || ElementProto.mozMatchesSelector || ElementProto.webkitMatchesSelector || function matches(selector) {
			var element = this;
			var elements = (element.document || element.ownerDocument).querySelectorAll(selector);
			var index = 0;

			while (elements[index] && elements[index] !== element) {
				++index;
			}

			return Boolean(elements[index]);
		};
	}

	if (typeof ElementProto.closest !== "function") {
		ElementProto.closest = function closest(selector) {
			var element = this;

			while (element && element.nodeType === 1) {
				if (element.matches(selector)) {
					return element;
				}

				element = element.parentNode;
			}

			return null;
		};
	}
})(window.Element.prototype);

           </script>',

            sanitize_html_class( $tag->name ), $atts, $validation_error );

	return $html;
}


function wpcf7_pc4upostcode_validation_filter( $result, $tag ) {
    $tag = new WPCF7_FormTag( $tag );

    $name = $tag->name;

    if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
        foreach ( $_POST[$name] as $key => $value ) {
            if ( '' === $value ) {
                unset( $_POST[$name][$key] );
            }
        }
    }

    $empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

    if ( $tag->is_required() && $empty ) {
        $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
    }

    return $result;
}
