<?php
/** POSTCODES4u - Gravity Forms Integration
 *  Description: Adds Gravity Forms Address Class For Postcodes4u Lookup
 *
 *    Updates 2019.07.04 - 'Trimmed' Key & Username to stop extra space on Key
 *    Updates 2020.05.18 - Add 'Blank County'
 *    Updates 2021.05.13 - Fix Admin Form Editor Issues with GravityForms 2.5
 *    Updates 2021.07.30 - More Updates to GF Address field for GravityForms 2.5 compatibility
 *                         .. Introduce Optional Manual Address Entry Button                  
 *                         .. Move Styling Overrides to plugin css file (pc4u_styles.css)
 *
 *   Updates 2021.09.30 - Updates & Bug Fixes to GF Address field for GravityForms 2.6 compatibility
 *                         .. Elementor &nbsp Issue
 *                         .. Move Styling Overrides to plugin css file (pc4u_styles.css)
 *                         .. Fixed Not displayed Country Stopping Address Field Validation
 *                         .. Fixed Postcode Lookup Label Field Stopping Lookup Button 
 *                              working on some themes 
 *                         .. Updates for very rare Aria Attribute Issue
 * 
 *   Updates 2022.02.16 - Remove Aria Attribute Coding for Gravity Forms due to customer reported errors.
 *                           (All Aria Values defaulted to empty)
 *
 *   Updates 2022.06.27 - RESTORED ARIA PROCESSING as detailed in 22.02.16 to reverse change.
 *                            Far more users were affected by the code removal than were suffering with it 
 *                          SO... This version has introduced a settings flag that allows the GravityForm's 
 *                            Address Form ARIA processing to be disabled. (gravityintegrate_noaria) default OFF
 *                           
 *   Updates 2023.01.24 - RESTORED ARIA PROCESSING - I Beleive it is related to using an OLder Version of Gravity Forms ( < 2.5)
 *                           SO Have 'recommended' this is the earliest preferred version- but code added to silectly trap ARIA calls
 * 
 * Version: 1.4.5
 * Author: Kevin Fisher - 3X Software
 * Plugin URI: plugins.3xsoftware.co.uk/wordpress-uk-address-finder
 * 
 *************************************************************************************/

/* Ensure Means exists to check Plugins Present */
if (!defined('ABSPATH')) {
    die("You can't do anything by accessing this file directly.");
}
if (!function_exists('is_plugin_active')) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

add_action('plugins_loaded', 'pc4u_GravityFormIntegration_init', 20);

function pc4u_GravityFormIntegration_init() {

    /* Include Postcodes4u Options */
    global $pc4u_options;   
    
    // Check for Postcodes4u Loaded and Gravity Forms Integration Required
    if(isset($pc4u_options) && isset($pc4u_options['gravityintegrate'])) {
        // Gravity Integration Setting Exists - Check Value
        if ($pc4u_options['gravityintegrate'] == true || $pc4u_options['gravityintegrate'] == '1') {
            // Check For Gravity Forms Loaded & Active
            if (is_plugin_active('gravityforms/gravityforms.php')) {
            
                if (class_exists('GFForms')) {

                    // EXTEND GRAVITY FORMS FIELDS CLASS
                    class GF_Fields_Pc4u extends GF_Fields {

                        public static function pc4uIsGfFieldsStatePrivate() {
                            $testGfFields = new GF_Fields();
                            $reflectionClass = new ReflectionClass($testGfFields);
                            $testPrivate = $reflectionClass->getProperty('_fields')->isPrivate();
                            return $testPrivate;
                        }

                        public static function pc4uRegister($field) {
                            if (!is_subclass_of($field, 'GF_Field')) {
                                throw new Exception('Must be a subclass of GF_Field');
                            }
                            if (empty($field->type)) {
                                throw new Exception('The type must be set');
                            }
                            // Check Updating Address Fields Will Work
                            try {
                                // Check If Integration Valid
                                if (GF_Fields_Pc4u::pc4uIsGfFieldsStatePrivate()) {
                                    // GF-Fields _Fields value is Private - Cannot Update
                                    echo 'CANNOT INTEGRATE POSTCODES4u WITH GRAVITY FORMS';
                                } else {
                                    // Unset If Field Type is Already Set
                                    if (isset(self::$_fields[$field->type])) {
                                        unset(self::$_fields[$field->type]);
                                    }
                                    // ALWAYS SET
                                    self::$_fields[$field->type] = $field;
                                }
                            }
                            catch (Exception $mEx) {
                                echo 'CANNOT INTEGRATE POSTCODES4u WITH GRAVITY FORMS (EX)';
                            }
                        }

                        // Unregister Specified Field
                        public static function pc4uUnRegister($fieldTypeName) {
                            if (isset(self::$_fields[$fieldTypeName])) {
                                unset(self::$_fields[$fieldTypeName]);
                            }
                        }

                    }

                    class GF_Field_Pc4uAddress extends GF_Field {

                        public $type = 'address';
                        

                        function get_form_editor_field_settings() {
                            return array(
                                'conditional_logic_field_setting',
                                'prepopulate_field_setting',
                                'error_message_setting',
                                'label_setting',
                                'admin_label_setting',
                                'label_placement_setting',
                                'sub_label_placement_setting',
                                'default_input_values_setting',
                                'input_placeholders_setting',
                                'address_setting',
                                'rules_setting',
                                'copy_values_option',
                                'description_setting',
                                'visibility_setting',
                                'css_class_setting',
                                'autocomplete_setting',
                                'pc4uPostcode_setting',
                            );
                        }
                      
                        public function is_conditional_logic_supported() {
                            return true;
                        }
                        
                        public function get_form_editor_field_title() {
                            return esc_attr__('Pc4u Addresses', 'gravityforms');
                        }


                        public function get_form_editor_button() {
                            return array(
                                'group' => 'advanced_fields',
                                'text' => __('Pc4u Address', 'gravityforms')
                            );
                        }

                        /**
                	 * Returns the field's form editor description.
                         *
                	 * @since 2.5
                         *
                         * @return string
                        */
                        public function get_form_editor_field_description() {
                            return esc_attr__( 'Allows users to enter a physical address using Postcodes4u.', 'gravityforms' );
                        }

                        /**
                        * Returns the field's form editor icon.
                        *
                        * This could be an icon url or a gform-icon class.
                        *
                        * @since 2.5
                        *
                        * @return string
                        */
                        public function get_form_editor_field_icon() {
                            return 'gform-icon--place';
                        }

                	/**
                        * Defines the IDs of required inputs.
                        *
                        * @since 2.5
                        *
                        * @return string[]
                        */
                        public function get_required_inputs_ids() {
                            return array( '1', '3', '4', '5', '6' );
                        }


                        /**
                        * Returns the HTML tag for the field container.
                        *
                        * @since 2.5
                        *
                        * @param array $form The current Form object.
                        *
                        * @return string
                        */
                        public function get_field_container_tag( $form ) {
                            
                            if ( GFCommon::is_legacy_markup_enabled( $form ) ) {
                                return parent::get_field_container_tag( $form );
                            }
                            
                            return 'fieldset';
                            
                        }                                              
 
                        function validate($value, $form) {               
                            
                            if ($this->isRequired) {
                                $copy_values_option_activated = $this->enableCopyValuesOption && rgpost('input_' . $this->id . '_copy_values_activated');
                                if ($copy_values_option_activated) {
                                    // validation will occur in the source field
                                    return;
                                }
                                $street = rgpost('input_' . $this->id . '_1');
                                $city = rgpost('input_' . $this->id . '_3');
                                $state = rgpost('input_' . $this->id . '_4');
                                $zip = rgpost('input_' . $this->id . '_5');
                                $country = rgpost('input_' . $this->id . '_6');



                                $pc4uValidateCounty = false;
                                if($this->pc4uPostcodeValidateCounty) {
                                    $pc4uValidateCounty = true;
                                }
                                                            
                                if (empty($street) && !$this->get_input_property($this->id . '.1', 'isHidden') ||
                                        empty($city) && !$this->get_input_property($this->id . '.3', 'isHidden') ||
                                        empty($zip) && !$this->get_input_property($this->id . '.5', 'isHidden') ||
                                        ( empty($state) && !( $this->hideState || $this->get_input_property($this->id . '.4', 'isHidden') || !$pc4uValidateCounty ) )  
                                ) {
                                    $this->failed_validation = true;
                                    $this->validation_message = empty($this->errorMessage) ? esc_html__('This field is required. Please enter a complete address.', 'gravityforms') : $this->errorMessage;
                                }
                            }
                        }

                        public function get_value_submission( $field_values, $get_from_post_global_var = true ) {
                            
                            $value                                         = parent::get_value_submission( $field_values, $get_from_post_global_var );
                            $value[ $this->id . '_copy_values_activated' ] = (bool) rgpost( 'input_' . $this->id . '_copy_values_activated' );

                            return $value;
                        }
                        
                        
                        
                        // ARIA Handlers In Case Gravity FormParent Not Available
                        
                        // get_aria_attributes
                        // ===================
                        // If Parent Class has 'get_aria_attributes' - call as normal, otherwise return empty string
                        public function get_aria_attributes(  $values, $input_id = '' ){
                            if (method_exists(get_parent_class($this), 'get_aria_attributes')) {
                                // method exist
                                try {
                                     return parent::get_aria_attributes($values, $input_id);
                                } catch (Exception $ex) {
                                      // Ignore
                                    return "";
                                }
                            }
                            return "";
                        }
                        
                        // maybe_add_aria_describedby
                        // ==========================
                        // If Parent Class has 'maybe_add_aria_describedby' - call as normal,
                        //  otherwise return empty string
                        public function maybe_add_aria_describedby( $input, $field_id, $form_id ) {
                          if (method_exists(get_parent_class($this), 'maybe_add_aria_describedby')) {
                                // method exist
                                try {
                                     return parent::maybe_add_aria_describedby($input, $field_id, $form_id);
                                } catch (Exception $ex) {
                                      // Ignore
                                    return "";
                                }
                            }
                            return "";   
                        }
                        
                        // END OF ARIA Handlers
                        
                        
                        
                        public function get_field_input($form, $value = '', $entry = null) {
                            /* Include Postcodes4u Options */
                            global $pc4u_options;

                            $pc4uEnabled = false ;
                            $pc4uGravityFormsEnabled = false;

                            $pc4uKey = "";
                            $pc4uUser = "";
                            $pc4uAltAddressDisp = "";
                            $pc4uCountyDisp = "";
                            
                            $pc4uLookupButtonText = "Lookup";
                            $pc4uAddressSelectDropdownText = "Select an address:";
                            $pc4uManualAddressButtonText = "Manual Address";
                            $pc4uShowWarnings = '';
                                    
                            if(isset($pc4u_options)) {
                                // Check Active'Master Switch'
                                if(isset($pc4u_options['enable'])) {
                                    $pc4uEnabled = $pc4u_options["enable"];
                                }
                                if ($pc4uEnabled) {
                                    if(isset($pc4u_options['gravityintegrate'])) {
                                        $pc4uGravityFormsEnabled = $pc4u_options["gravityintegrate"];
                                    }
                                    $pc4uKey = trim($pc4u_options["user_key"]);
                                    $pc4uUser = trim($pc4u_options["user_name"]);
                                    $pc4uAltAddressDisp = $pc4u_options["alt_address_disp"];
                                    $pc4uCountyDisp = $pc4u_options["county_address_disp"];
                                 
                                    // -- Show Warnings Flag - KRF 2021.07.30
                                     if(isset($pc4u_options['show_warnings'])) {
                                        $pc4uShowWarnings = $pc4u_options["show_warnings"];                                    
                                    }
                                    // -- Customisable Button Texts - KRF 2021.07.29
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
                                    
                                    // ------------------------------------------------------------------
                                }
                            }



                            /* ------------------------------------------------------------- */

                            $is_entry_detail = $this->is_entry_detail();
                            $is_form_editor = $this->is_form_editor();
                            $is_admin = $is_entry_detail || $is_form_editor;

                            $form_id = absint($form['id']);
                            $id = intval($this->id);
                            $field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
                            $form_id = ( $is_entry_detail || $is_form_editor ) && empty($form_id) ? rgget('id') : $form_id;

                            $disabled_text = $is_form_editor ? "disabled='disabled'" : '';
                            
                            // Allow Access To Zip Field In Editor to allow demonstration of Pc4u address lookup
                            $disabled_ziptext = $is_form_editor ?  '' : '';
//                            $disabled_ziptext = $is_form_editor ?  "disabled='disabled'" : '';
                             
                            $class_suffix = $is_entry_detail ? '_admin' : '';

                            
                            $form_sub_label_placement = rgar($form, 'subLabelPlacement');
                            $field_sub_label_placement = $this->subLabelPlacement;
                            $is_sub_label_above = $field_sub_label_placement == 'above' || ( empty($field_sub_label_placement) && $form_sub_label_placement == 'above' );
                            $sub_label_class_attribute = $field_sub_label_placement == 'hidden_label' ? "class='hidden_sub_label screen-reader-text'" : '';

                            $street_value = '';
                            $street2_value = '';
                            $city_value = '';
                            $state_value = '';
                            $zip_value = '';
                            $country_value = '';

                            if (is_array($value)) {
                                $street_value = esc_attr(rgget($this->id . '.1', $value));
                                $street2_value = esc_attr(rgget($this->id . '.2', $value));
                                $city_value = esc_attr(rgget($this->id . '.3', $value));
                                $state_value = esc_attr(rgget($this->id . '.4', $value));
                                $zip_value = esc_attr(rgget($this->id . '.5', $value));
                                $country_value = esc_attr(rgget($this->id . '.6', $value));
                            }

                            // Inputs.
                            $address_street_field_input = GFFormsModel::get_input($this, $this->id . '.1');
                            $address_street2_field_input = GFFormsModel::get_input($this, $this->id . '.2');
                            $address_city_field_input = GFFormsModel::get_input($this, $this->id . '.3');
                            $address_state_field_input = GFFormsModel::get_input($this, $this->id . '.4');
                            $address_zip_field_input = GFFormsModel::get_input($this, $this->id . '.5');
                            $address_country_field_input = GFFormsModel::get_input($this, $this->id . '.6');

                            // Placeholders.
                            $street_placeholder_attribute = GFCommon::get_input_placeholder_attribute($address_street_field_input);
                            $street2_placeholder_attribute = GFCommon::get_input_placeholder_attribute($address_street2_field_input);
                            $city_placeholder_attribute = GFCommon::get_input_placeholder_attribute($address_city_field_input);
                            $zip_placeholder_attribute = GFCommon::get_input_placeholder_attribute($address_zip_field_input);

                            $address_types = $this->get_address_types($form_id);
                            $addr_type = empty($this->addressType) ? $this->get_default_address_type($form_id) : $this->addressType;
                            $address_type = rgar($address_types, $addr_type);

                            $state_label = empty($address_type['state_label']) ? esc_html__('State', 'gravityforms') : $address_type['state_label'];
                            $zip_label = empty($address_type['zip_label']) ? esc_html__('Zip Code', 'gravityforms') : $address_type['zip_label'];
                            $hide_country = !empty($address_type['country']) || $this->hideCountry || rgar($address_country_field_input, 'isHidden');

                            if (empty($country_value)) {
                                $country_value = $this->defaultCountry;
                            }

                            if (empty($state_value)) {
                                $state_value = $this->defaultState;
                            }

                            $country_placeholder = GFCommon::get_input_placeholder_value($address_country_field_input);
                            $country_list = $this->get_country_dropdown($country_value, $country_placeholder);

                            // Changing css classes based on field format to ensure proper display.
                            $address_display_format = apply_filters('gform_address_display_format', 'default', $this);
                            $city_location = $address_display_format == 'zip_before_city' ? 'right' : 'left';
                            $zip_location = $address_display_format != 'zip_before_city' && ( $this->hideState || rgar($address_state_field_input, 'isHidden') ) ? 'right' : 'left'; // support for $this->hideState legacy property
                            $state_location = $address_display_format == 'zip_before_city' ? 'left' : 'right';
                            $country_location = $this->hideState || rgar($address_state_field_input, 'isHidden') ? 'left' : 'right'; // support for $this->hideState legacy property
                            // -------------------------------------------------------------------
                            // Postcodes4u Values
                            $Pc4uLookup_location = $zip_location == 'left' ? 'right' : 'left';

                            // Ensure Postcodes4u is Enabled for Gravity Forms and Required Here
                            $pc4uPostcodeLookupRequired = false;
                            if ($pc4uGravityFormsEnabled == true) {
                                $pc4uPostcodeLookupRequired = $this->pc4uUsePostcodeLookup;
                            }
                            $putPostcodeFieldAtTop = $this->pc4uPostcodeAtTop;
                            
                            // Load Additional Show Address After Lookup Options
                            $showAddressAfterPostcodeLookup = false ;
                            $showAddressAfterPostcodeLookupManual = false ;

                            // Show Postcode at Top($putPostcodeFieldAtTop) must be selected
                            if($putPostcodeFieldAtTop) {
                                // Show Address after lookup only valid if Postcode is at top
                                $showAddressAfterPostcodeLookup = $this->pc4uPostcodeAtTopShow;
                            
                                if($showAddressAfterPostcodeLookup) {
                                    // Show Manual Addres (Display Address Fields) if Postcode is at top and Display after selected all true
                                    $showAddressAfterPostcodeLookupManual = $this->pc4uPostcodeAtTopShowManual;
                                }
                            }
                            
                            // -------------------------------------------------------------------
                            // Labels.
                            $address_street_sub_label  = rgar( $address_street_field_input, 'customLabel' ) != '' ? $address_street_field_input['customLabel'] : esc_html__( 'Street Address', 'gravityforms' );
                            $address_street_sub_label  = gf_apply_filters( array( 'gform_address_street', $form_id, $this->id ), $address_street_sub_label, $form_id );
                            $address_street2_sub_label = rgar( $address_street2_field_input, 'customLabel' ) != '' ? $address_street2_field_input['customLabel'] : esc_html__( 'Address Line 2', 'gravityforms' );
                            $address_street2_sub_label = gf_apply_filters( array( 'gform_address_street2', $form_id, $this->id ), $address_street2_sub_label, $form_id );
                            $address_zip_sub_label     = rgar( $address_zip_field_input, 'customLabel' ) != '' ? $address_zip_field_input['customLabel'] : $zip_label;
                            $address_zip_sub_label     = gf_apply_filters( array( 'gform_address_zip', $form_id, $this->id ), $address_zip_sub_label, $form_id );
                            $address_city_sub_label    = rgar( $address_city_field_input, 'customLabel' ) != '' ? $address_city_field_input['customLabel'] : esc_html__( 'City', 'gravityforms' );
                            $address_city_sub_label    = gf_apply_filters( array( 'gform_address_city', $form_id, $this->id ), $address_city_sub_label, $form_id );
                            $address_state_sub_label   = rgar( $address_state_field_input, 'customLabel' ) != '' ? $address_state_field_input['customLabel'] : $state_label;
                            $address_state_sub_label   = gf_apply_filters( array( 'gform_address_state', $form_id, $this->id ), $address_state_sub_label, $form_id );
                            $address_country_sub_label = rgar( $address_country_field_input, 'customLabel' ) != '' ? $address_country_field_input['customLabel'] : esc_html__( 'Country', 'gravityforms' );
                            $address_country_sub_label = gf_apply_filters( array( 'gform_address_country', $form_id, $this->id ), $address_country_sub_label, $form_id );
                            
                            // Autocomplete attributes.
                            $address_street_autocomplete  = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $address_street_field_input ) : '';
                            $address_street2_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $address_street2_field_input ) : '';
                            $address_city_autocomplete    = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $address_city_field_input ) : '';
                            $address_zip_autocomplete     = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $address_zip_field_input ) : '';
                            $address_country_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $address_country_field_input ) : '';

                            // Aria attributes.
                            $street_aria_attributes  = $this->get_aria_attributes( $value, '1' );
                            $street2_aria_attributes = $this->get_aria_attributes( $value, '2' );
                            $city_aria_attributes    = $this->get_aria_attributes( $value, '3' );
                            $zip_aria_attributes     = $this->get_aria_attributes( $value, '5' );
                            $country_aria_attributes = $this->get_aria_attributes( $value, '6' );                            
                                                        
                            // Address field.
                            $street_address = '';
                            $tabindex = $this->get_tabindex();
                            $style = ( $is_admin && rgar($address_street_field_input, 'isHidden') ) ? "style='display:none;'" : '';
   
                            if ($is_admin || !rgar($address_street_field_input, 'isHidden')) {
                               if ($is_sub_label_above) {
                                    $street_address = " <span class='ginput_full{$class_suffix} address_line_1  ginput_address_line_1' id='{$field_id}_1_container' {$style}>
                                        <label for='{$field_id}_1' id='{$field_id}_1_label' {$sub_label_class_attribute}>{$address_street_sub_label}</label>
                                        <input type='text' name='input_{$id}.1' id='{$field_id}_1' value='{$street_value}' {$tabindex} {$disabled_text} {$street_placeholder_attribute} {$street_aria_attributes} {$address_street_autocomplete} {$this->maybe_add_aria_describedby( $address_street_field_input, $field_id, $this['formId'] )}/>
                                    </span>";
                                } else {
                                    $street_address = " <span class='ginput_full{$class_suffix} address_line_1 ginput_address_line_1' id='{$field_id}_1_container' {$style}>
                                        <input type='text' name='input_{$id}.1' id='{$field_id}_1' value='{$street_value}' {$tabindex} {$disabled_text} {$street_placeholder_attribute} {$street_aria_attributes} {$address_street_autocomplete} {$this->maybe_add_aria_describedby( $address_street_field_input, $field_id, $this['formId'] )}/>                                                                                
                                        <label for='{$field_id}_1' id='{$field_id}_1_label' {$sub_label_class_attribute}>{$address_street_sub_label}</label>
                                    </span>";
                                }
                            }

                            // Address line 2 field.
                            $street_address2 = '';
                            $style = ( $is_admin && ( $this->hideAddress2 || rgar($address_street2_field_input, 'isHidden') ) ) ? "style='display:none;'" : ''; // support for $this->hideAddress2 legacy property
                            if ($is_admin || (!$this->hideAddress2 && !rgar($address_street2_field_input, 'isHidden') )) {
                                $tabindex = $this->get_tabindex();
                                if ($is_sub_label_above) {
                                    $street_address2 = "<span class='ginput_full{$class_suffix} address_line_2 ginput_address_line_2' id='{$field_id}_2_container' {$style}>
                                        <label for='{$field_id}_2' id='{$field_id}_2_label' {$sub_label_class_attribute}>{$address_street2_sub_label}</label>
                                        <input type='text' name='input_{$id}.2' id='{$field_id}_2' value='{$street2_value}' {$tabindex} {$disabled_text} {$street2_placeholder_attribute} {$address_street2_autocomplete} {$street2_aria_attributes} {$this->maybe_add_aria_describedby( $address_street2_field_input, $field_id, $this['formId'] )}/>                                                                                
                                    </span>";
                                } else {
                                    $street_address2 = "<span class='ginput_full{$class_suffix} address_line_2 ginput_address_line_2' id='{$field_id}_2_container' {$style}>
                                        <input type='text' name='input_{$id}.2' id='{$field_id}_2' value='{$street2_value}' {$tabindex} {$disabled_text} {$street2_placeholder_attribute} {$address_street2_autocomplete} {$street2_aria_attributes} {$this->maybe_add_aria_describedby( $address_street2_field_input, $field_id, $this['formId'] )}/>                                        
                                        <label for='{$field_id}_2' id='{$field_id}_2_label' {$sub_label_class_attribute}>{$address_street2_sub_label}</label>
                                    </span>";
                                }
                            }

                            if ($address_display_format == 'zip_before_city') {
                                // Zip field.
                                //   KRF ADD POSTCODE LOOKUP
                                $zip = '';
                                $tabindex = $this->get_tabindex();
                                $style = ( $is_admin && rgar($address_zip_field_input, 'isHidden') ) ? "style='display:none;'" : '';
                                if ($is_admin || !rgar($address_zip_field_input, 'isHidden')) {

                                    if ($pc4uPostcodeLookupRequired) {
                                        // Add Postcodes4u Address Lookup
                                        if ($is_sub_label_above) {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                            </span>
                                            <span class='Pc4uGfLookupSpan ginput_{$Pc4uLookup_location}' >
                                                <label for='{$field_id}Lookup_5' id='{$field_id}Lookup_5_label'></label>
                                                <button onclick='Pc4uGfSearchBegin(\"{$field_id}\");return false;'  class='Pc4uGfLookup' >{$pc4uLookupButtonText}</button>";
                                                // Show Manual Address Lookup If Required
                                                if ($showAddressAfterPostcodeLookupManual  ) {    
                                                     $zip = $zip . "<button id='{$field_id}_pc4uGFManualAddress' onclick='showPc4uGFHiddenAddress(\"{$field_id}\");return false;'  class='Pc4uGfManLookup'>{$pc4uManualAddressButtonText}</button>";
                                                }
                                            $zip = $zip . "</span>
                                            <span class='ginput_full{$class_suffix}'>
                                                <select  name='{$field_id}_pc4uGfDropdown' id='{$field_id}_pc4uGfDropdown' class='Pc4uGfDropDown' onchange='Pc4uSearchIdGfBegin()' style='display:none; margin-top:4px;'><option>{$pc4uAddressSelectDropdownText}</option></select>
                                            </span>
                                            <div class='clear'>
                                                <div id='postcodes4ukey' style='display:none'>" . $pc4uKey . "</div>
                                                <div id='postcodes4uuser' style='display:none'>" . $pc4uUser . "</div>
                                                <div id='pc4ualt_address_disp' style='display:none'>" . $pc4uAltAddressDisp . "</div>
                                                <div id='pc4ucounty_disp' style='display:none'>" . $pc4uCountyDisp . "</div>
                                                <div id='pc4ushow_warnings' style='display:none'>" . $pc4uShowWarnings . "</div>
                                                <div id='pc4uselect_text' style='display:none'>" . $pc4uAddressSelectDropdownText . "</div>
                                            </div>";
                                        } else {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                            </span>
                                            <span class='Pc4uGfLookupSpan ginput_{$Pc4uLookup_location}' >
                                                <button onclick='Pc4uGfSearchBegin(\"{$field_id}\");return false;'  class='Pc4uGfLookup' >{$pc4uLookupButtonText}</button>";
                                                // Show Manual Address Lookup If Required
                                                if ($showAddressAfterPostcodeLookupManual ) {    
                                                     $zip = $zip . "<button id='{$field_id}_pc4uGFManualAddress' onclick='showPc4uGFHiddenAddress(\"{$field_id}\");return false;'  class='Pc4uGfManLookup' >{$pc4uManualAddressButtonText}</button>";
                                                }
                                                $zip = $zip . " <label for='{$field_id}Lookup_5' id='{$field_id}Lookup_5_label'></label>
                                            </span>                                            
                                            <span class='ginput_full{$class_suffix}'>
                                                <select name='{$field_id}_pc4uGfDropdown' id='{$field_id}_pc4uGfDropdown'  class='Pc4uGfDropDown' onchange='Pc4uSearchIdGfBegin()' style='display:none; margin-top:4px;'><option>{$pc4uAddressSelectDropdownText}</option></select>
                                            </span>
                                            <div class='clear'>
                                                <div id='postcodes4ukey' style='display:none'>" . $pc4uKey . "</div>
                                                <div id='postcodes4uuser' style='display:none'>" . $pc4uUser . "</div>
                                                <div id='pc4ualt_address_disp' style='display:none'>" . $pc4uAltAddressDisp . "</div>
                                                <div id='pc4ucounty_disp' style='display:none'>" . $pc4uCountyDisp . "</div>
                                                <div id='pc4ushow_warnings' style='display:none'>" . $pc4uShowWarnings . "</div>
                                                <div id='pc4uselect_text' style='display:none'>" . $pc4uAddressSelectDropdownText . "</div>
                                            </div>";
                                        }
                                    } else {
                                        // Default 'Zip Before City' Address Display
                                        if ($is_sub_label_above) {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                            </span>";
                                        } else {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                            </span>";
                                        }
                                    }
                                }

                                // City field.
                                $city = '';
                                $tabindex = $this->get_tabindex();
                                $style = ( $is_admin && rgar($address_city_field_input, 'isHidden') ) ? "style='display:none;'" : '';
                                if ($is_admin || !rgar($address_city_field_input, 'isHidden')) {
                                    if ($is_sub_label_above) {
                                        $city = "<span class='ginput_{$city_location}{$class_suffix} address_city ginput_address_city' id='{$field_id}_3_container' {$style}>
                                            <label for='{$field_id}_3' id='{$field_id}_3_label' {$sub_label_class_attribute}>{$address_city_sub_label}</label>
                                            <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$city_value}' {$tabindex} {$disabled_text} {$city_placeholder_attribute} {$city_aria_attributes} {$address_city_autocomplete} {$this->maybe_add_aria_describedby( $address_city_field_input, $field_id, $this['formId'] )}/>
                                        </span>";
                                    } else {
                                        $city = "<span class='ginput_{$city_location}{$class_suffix} address_city ginput_address_city' id='{$field_id}_3_container' {$style}>
                                            <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$city_value}' {$tabindex} {$disabled_text} {$city_placeholder_attribute} {$city_aria_attributes} {$address_city_autocomplete} {$this->maybe_add_aria_describedby( $address_city_field_input, $field_id, $this['formId'] )}/>
                                            <label for='{$field_id}_3' id='{$field_id}_3_label' {$sub_label_class_attribute}>{$address_city_sub_label}</label>
                                        </span>";
                                    }
                                }

                                // State field.
                                $style = ( $is_admin && ( $this->hideState || rgar($address_state_field_input, 'isHidden') ) ) ? "style='display:none;'" : ''; // support for $this->hideState legacy property
                                if ($is_admin || (!$this->hideState && !rgar($address_state_field_input, 'isHidden') )) {
                                    $aria_attributes = $this->get_aria_attributes( $value, '4' );
                                    $state_field = $this->get_state_field( $id, $field_id, $state_value, $disabled_text, $form_id, $aria_attributes, $address_state_field_input );
                                    if ($is_sub_label_above) {
                                        $state = "<span class='ginput_{$state_location}{$class_suffix} address_state  ginput_address_state' id='{$field_id}_4_container' {$style}>
                                            <label for='{$field_id}_4' id='{$field_id}_4_label' {$sub_label_class_attribute}>{$address_state_sub_label}</label>
                                            $state_field
                                        </span>";
                                    } else {
                                        $state = "<span class='ginput_{$state_location}{$class_suffix} address_state ginput_address_state' id='{$field_id}_4_container' {$style}>
                                            $state_field
                                            <label for='{$field_id}_4' id='{$field_id}_4_label' {$sub_label_class_attribute}>{$address_state_sub_label}</label>
                                        </span>";
                                    }
                                } else {
                                    $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
                                }
                            } else {
                                // City field.
                                $city = '';
                                $tabindex = $this->get_tabindex();
                                $style = ( $is_admin && rgar($address_city_field_input, 'isHidden') ) ? "style='display:none;'" : '';
                                if ($is_admin || !rgar($address_city_field_input, 'isHidden')) {
                                    if ($is_sub_label_above) {
                                        $city = "<span class='ginput_{$city_location}{$class_suffix} address_city ginput_address_city' id='{$field_id}_3_container' {$style}>
                                            <label for='{$field_id}_3' id='{$field_id}_3_label' {$sub_label_class_attribute}>{$address_city_sub_label}</label>
                                            <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$city_value}' {$tabindex} {$disabled_text} {$city_placeholder_attribute} {$city_aria_attributes} {$address_city_autocomplete} {$this->maybe_add_aria_describedby( $address_city_field_input, $field_id, $this['formId'] )}  />
                                        </span>";
                                    } else {
                                        $city = "<span class='ginput_{$city_location}{$class_suffix} address_city ginput_address_city' id='{$field_id}_3_container' {$style}>
                                            <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$city_value}' {$tabindex} {$disabled_text} {$city_placeholder_attribute} {$city_aria_attributes} {$address_city_autocomplete} {$this->maybe_add_aria_describedby( $address_city_field_input, $field_id, $this['formId'] )}  />
                                            <label for='{$field_id}_3' id='{$field_id}_3_label' {$sub_label_class_attribute}>{$address_city_sub_label}</label>
                                        </span>";
                                    }
                                }
                                // State field.
                                $style = ( $is_admin && ( $this->hideState || rgar($address_state_field_input, 'isHidden') ) ) ? "style='display:none;'" : ''; // support for $this->hideState legacy property
                                if ($is_admin || (!$this->hideState && !rgar($address_state_field_input, 'isHidden') )) {
                                    $aria_attributes = $this->get_aria_attributes( $value, '4' );
                                    $state_field = $this->get_state_field( $id, $field_id, $state_value, $disabled_text, $form_id, $aria_attributes, $address_state_field_input );
                                    if ($is_sub_label_above) {
                                        $state = "<span class='ginput_{$state_location}{$class_suffix} address_state ginput_address_state' id='{$field_id}_4_container' {$style}>
                                            <label for='{$field_id}_4' id='{$field_id}_4_label' {$sub_label_class_attribute}>$address_state_sub_label</label>
                                            $state_field
                                        </span>";
                                    } else {
                                        $state = "<span class='ginput_{$state_location}{$class_suffix} address_state ginput_address_state' id='{$field_id}_4_container' {$style}>
                                            $state_field
                                            <label for='{$field_id}_4' id='{$field_id}_4_label' {$sub_label_class_attribute}>$address_state_sub_label</label>
                                        </span>";
                                    }
                                } else {
                                    $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
                                }

                                // Zip field.
                                //   KRF ADD POSTCODE LOOKUP
                                $zip = '';
                                $tabindex = GFCommon::get_tabindex();
                                $style = ( $is_admin && rgar($address_zip_field_input, 'isHidden') ) ? "style='display:none;'" : '';
                                if ($is_admin || !rgar($address_zip_field_input, 'isHidden')) {

                                    if ($pc4uPostcodeLookupRequired) {
                                        // Add Postcodes4u Address Lookup
                                        if ($is_sub_label_above) {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                            </span>
                                            <span class='Pc4uGfLookupSpan ginput_{$Pc4uLookup_location}' >                                               
                                                <button onclick='Pc4uGfSearchBegin(\"{$field_id}\");return false;'  class='Pc4uGflookup' >{$pc4uLookupButtonText}</button>";
                                                // Show Manual Address Lookup If Required
                                                if ($showAddressAfterPostcodeLookupManual  ) {    
                                                    $zip = $zip . "<button id='{$field_id}_pc4uGFManualAddress' onclick='showPc4uGFHiddenAddress(\"{$field_id}\");return false;'  class='Pc4uGfManLookup'>{$pc4uManualAddressButtonText}</button>";
                                                }
                                                $zip = $zip . " <label for='{$field_id}Lookup_5' id='{$field_id}Lookup_5_label'></label>
                                            </span>                                                    

                                            <span class='ginput_full{$class_suffix}'>
                                                <select name='{$field_id}_pc4uGfDropdown' id='{$field_id}_pc4uGfDropdown'  class='Pc4uGfDropDown' onchange='Pc4uSearchIdGfBegin()' style='display:none; margin-top:4px;'><option>{$pc4uAddressSelectDropdownText}</option></select>
                                            </span>
                                            <div class='clear'>
                                                <div id='postcodes4ukey' style='display:none'>" . $pc4uKey . "</div>
                                                <div id='postcodes4uuser' style='display:none'>" . $pc4uUser . "</div>
                                                <div id='pc4ualt_address_disp' style='display:none'>" . $pc4uAltAddressDisp . "</div>
                                                <div id='pc4ucounty_disp' style='display:none'>" . $pc4uCountyDisp . "</div>
                                                <div id='pc4ushow_warnings' style='display:none'>" . $pc4uShowWarnings . "</div>
                                                <div id='pc4uselect_text' style='display:none'>" . $pc4uAddressSelectDropdownText . "</div>
                                            </div>";
                                        } else {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip  ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )} />
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                            </span>
                                            <span class='Pc4uGfLookupSpan ginput_{$Pc4uLookup_location}' >
                                                <button onclick='Pc4uGfSearchBegin(\"{$field_id}\");return false;'  class='Pc4uGfLookup' >{$pc4uLookupButtonText}</button>";
                                                // Show Manual Address Lookup If Required
                                                if ($showAddressAfterPostcodeLookupManual  ) {    
                                                     $zip = $zip . "<button id='{$field_id}_pc4uGFManualAddress' onclick='showPc4uGFHiddenAddress(\"{$field_id}\");return false;'  class='Pc4uGfManLookup'>{$pc4uManualAddressButtonText}</button>";
                                                }
                                                $zip = $zip . "</span>   <label for='{$field_id}Lookup_5' id='{$field_id}Lookup_5_label'></label>
                                            </span>
                                            <span class='ginput_full{$class_suffix}'>
                                                <select name='{$field_id}_pc4uGfDropdown' id='{$field_id}_pc4uGfDropdown'  class='Pc4uGfDropDown' onchange='Pc4uSearchIdGfBegin()' style='display:none; margin-top:4px;' ><option>Select an address:</option></select>
                                            </span>
                                            <div class='clear'>
                                                <div id='postcodes4ukey' style='display:none'>" . $pc4uKey . "</div>
                                                <div id='postcodes4uuser' style='display:none'>" . $pc4uUser . "</div>
                                                <div id='pc4ualt_address_disp' style='display:none'>" . $pc4uAltAddressDisp . "</div>
                                                <div id='pc4ucounty_disp' style='display:none'>" . $pc4uCountyDisp . "</div>
                                                <div id='pc4ushow_warnings' style='display:none'>" . $pc4uShowWarnings . "</div>
                                                <div id='pc4uselect_text' style='display:none'>" . $pc4uAddressSelectDropdownText . "</div>
                                            </div>";
                                        }
                                    } else {
                                        // Default Zip Field Display
                                        if ($is_sub_label_above) {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                            </span>";
                                        } else {
                                            $zip = "<span class='ginput_{$zip_location}{$class_suffix} address_zip ginput_address_zip' id='{$field_id}_5_container' {$style}>
                                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$zip_value}' {$tabindex} {$disabled_ziptext} {$zip_placeholder_attribute} {$zip_aria_attributes} {$address_zip_autocomplete} {$this->maybe_add_aria_describedby( $address_zip_field_input, $field_id, $this['formId'] )}/>
                                                <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$address_zip_sub_label}</label>
                                            </span>";
                                        }
                                    }
                                }
                            }
                            if ($is_admin || !$hide_country) {
                                $style = $hide_country ? "style='display:none;'" : '';
                                $tabindex = $this->get_tabindex();
                                if ($is_sub_label_above) {
                                    $country = "<span class='ginput_{$country_location}{$class_suffix} address_country ginput_address_country' id='{$field_id}_6_container' {$style}>
                                        <label for='{$field_id}_6' id='{$field_id}_6_label' {$sub_label_class_attribute}>{$address_country_sub_label}</label>
                                        <select name='input_{$id}.6' id='{$field_id}_6' {$tabindex} {$disabled_text} {$country_aria_attributes} {$address_country_autocomplete} {$this->maybe_add_aria_describedby( $address_country_field_input, $field_id, $this['formId'] )}>{$country_list}</select>
                                    </span>";
                                } else {
                                    $country = "<span class='ginput_{$country_location}{$class_suffix} address_country  ginput_address_country' id='{$field_id}_6_container' {$style}>
                                        <select name='input_{$id}.6' id='{$field_id}_6' {$tabindex} {$disabled_text} {$country_aria_attributes} {$address_country_autocomplete} {$this->maybe_add_aria_describedby( $address_country_field_input, $field_id, $this['formId'] )}>{$country_list}</select>
                                        <label for='{$field_id}_6' id='{$field_id}_6_label' {$sub_label_class_attribute}>{$address_country_sub_label}</label>
                                    </span>";
                                }
                            } else {
                                $country = sprintf( "<input type='hidden' class='gform_hidden' name='input_%d.6' id='%s_6' value='%s' {$this->maybe_add_aria_describedby( $address_country_field_input, $field_id, $this['formId'] )}/>", $id, $field_id, $country_value );                                
                            }
                            
                            // Check If Postcode at Top Required
                            if ($pc4uPostcodeLookupRequired && $putPostcodeFieldAtTop) {
                                // Postcode4u - Show Field at Top Placement
                                // Check If Hide Address Until Lookup Complete - If Not Admin and Address Not Displayed
                                if ($showAddressAfterPostcodeLookup && $is_admin === false && ($street_value == '' && $city_value == '')) {
                                    $inputs = $zip . "<div id='{$field_id}_pc4uGfPostcodeLookupAddress' class='pc4uGfPostcodeLookupAddress' style= 'display:none'>" . $street_address . $street_address2 . $city . $state . $country . "</div>";
                                } else {
                                    //$inputs = $zip . $street_address . $street_address2 . $city . $state . $country . "</div>";
                                    $inputs = $zip . $street_address . $street_address2 . $city . $state . $country ;
                                }
                            } else {
                                // Normal Zip/Postcode Placement
                                $inputs = $address_display_format == 'zip_before_city' ? $street_address . $street_address2 . $zip . $city . $state . $country : $street_address . $street_address2 . $city . $state . $zip . $country;
                            }
                            $copy_values_option = '';
                            $input_style = '';
                            if (( $this->enableCopyValuesOption || $is_form_editor ) && !$is_entry_detail) {
                                $copy_values_label = esc_html( $this->copyValuesOptionLabel );
                                $copy_values_style = $is_form_editor && !$this->enableCopyValuesOption ? "style='display:none;'" : '';
                                $copy_values_is_checked = isset($value[$this->id . '_copy_values_activated']) ? $value[$this->id . '_copy_values_activated'] == true : $this->copyValuesOptionDefault == true;
                                $copy_values_checked = checked(true, $copy_values_is_checked, false);
                                $copy_values_option = "<div id='{$field_id}_copy_values_option_container' class='copy_values_option_container' {$copy_values_style}>
                                    <input type='checkbox' id='{$field_id}_copy_values_activated' class='copy_values_activated' value='1' data-source_field_id='" . absint( $this->copyValuesOptionField ) . "' name='input_{$id}_copy_values_activated' {$disabled_text} {$copy_values_checked}/>                                    
                                    <label for='{$field_id}_copy_values_activated' id='{$field_id}_copy_values_option_label' class='copy_values_option_label inline'>{$copy_values_label}</label>                                                                    
                                </div>";
                                // ** <input type='checkbox' id='{$field_id}_copy_values_activated' class='copy_values_activated' value='1' name='input_{$id}_copy_values_activated' {$disabled_text} {$copy_values_checked}/>
                                if ($copy_values_is_checked) {
                                    $input_style = "style='display:none;'";
                                }
                            }

                            $css_class = $this->get_css_class();

                            return "    {$copy_values_option}
                                <div class='ginput_complex{$class_suffix} ginput_container {$css_class}' id='$field_id' {$input_style}>
                                    {$inputs}
                                    <div class='gf_clear gf_clear_complex'></div>
                                </div>";
                        }

                      //  public function get_field_label_class() {
                      //      return 'gfield_label gfield_label_before_complex';
                      //  }

                        public function get_css_class() {

                            $address_street_field_input = GFFormsModel::get_input($this, $this->id . '.1');
                            $address_street2_field_input = GFFormsModel::get_input($this, $this->id . '.2');
                            $address_city_field_input = GFFormsModel::get_input($this, $this->id . '.3');
                            $address_state_field_input = GFFormsModel::get_input($this, $this->id . '.4');
                            $address_zip_field_input = GFFormsModel::get_input($this, $this->id . '.5');
                            $address_country_field_input = GFFormsModel::get_input($this, $this->id . '.6');

                            $css_class = '';
                            if (!rgar($address_street_field_input, 'isHidden')) {
                                $css_class .= 'has_street ';
                            }
                            if (!rgar($address_street2_field_input, 'isHidden')) {
                                $css_class .= 'has_street2 ';
                            }
                            if (!rgar($address_city_field_input, 'isHidden')) {
                                $css_class .= 'has_city ';
                            }
                            if (!rgar($address_state_field_input, 'isHidden')) {
                                $css_class .= 'has_state ';
                            }
                            if (!rgar($address_zip_field_input, 'isHidden')) {
                                $css_class .= 'has_zip ';
                            }
                            if (!rgar($address_country_field_input, 'isHidden')) {
                                $css_class .= 'has_country ';
                            }

                            $css_class .= 'ginput_container_address';

                            return trim($css_class);
                        }

                        public function get_address_types($form_id) {

                            $addressTypes = array(
                                'international' => array('label' => esc_html__('International', 'gravityforms'),
                                    'zip_label' => gf_apply_filters(array('gform_address_zip', $form_id), esc_html__('ZIP / Postal Code', 'gravityforms'), $form_id),
                                    'state_label' => gf_apply_filters(array('gform_address_state', $form_id), esc_html__('State / Province / Region', 'gravityforms'), $form_id)
                                ),
                                'us' => array(
                                    'label' => esc_html__('United States', 'gravityforms'),
                                    'zip_label' => gf_apply_filters(array('gform_address_zip', $form_id), esc_html__('ZIP Code', 'gravityforms'), $form_id),
                                    'state_label' => gf_apply_filters(array('gform_address_state', $form_id), esc_html__('State', 'gravityforms'), $form_id),
                                    'country' => 'United States',
                                    'states' => array_merge(array(''), $this->get_us_states())
                                ),
                                'canadian' => array(
                                    'label' => esc_html__('Canadian', 'gravityforms'),
                                    'zip_label' => gf_apply_filters(array('gform_address_zip', $form_id), esc_html__('Postal Code', 'gravityforms'), $form_id),
                                    'state_label' => gf_apply_filters(array('gform_address_state', $form_id), esc_html__('Province', 'gravityforms'), $form_id),
                                    'country' => 'Canada',
                                    'states' => array_merge(array(''), $this->get_canadian_provinces())
                                )
                            );

                            /**
                             * Filters the address types available.
                             *
                             * @since Unknown
                             *
                             * @param array $addressTypes Contains the details for existing address types.
                             * @param int   $form_id      The form ID.
                             */
                            return gf_apply_filters(array('gform_address_types', $form_id), $addressTypes, $form_id);
                        }

                        /**
                         * Retrieve the default address type for this field.
                         *
                         * @param int $form_id The current form ID.
                         *
                         * @return string
                         */
                        public function get_default_address_type($form_id) {
                            $default_address_type = 'international';

                            /**
                             * Allow the default address type to be overridden.
                             *
                             * @param string $default_address_type The default address type of international.
                             */
                            $default_address_type = apply_filters('gform_default_address_type', $default_address_type, $form_id);

                            return apply_filters('gform_default_address_type_' . $form_id, $default_address_type, $form_id);
                        }
                        

                        
                        /**
                         * Generates state field markup.
                	 *
                	 * @since unknown
                	 * @since 2.5                       added new params $$aria_attributes.
                	 *
                         * @param integer $id               Input id.
                	 * @param integer $field_id         Field id.
                	 * @param string  $state_value      State value.
                	 * @param string  $disabled_text    Disabled attribute.
                    	 * @param integer $form_id          Current form id being processed.
                	 * @param string  $aria_attributes  Aria attributes values.
                	 *
                	 * @return string
                	 */
                        public function get_state_field( $id, $field_id, $state_value, $disabled_text, $form_id, $aria_attributes = '', $address_state_field_input = '' ) {

                            $is_entry_detail = $this->is_entry_detail();
                            $is_form_editor  = $this->is_form_editor();
                            $is_admin        = $is_entry_detail || $is_form_editor;

                            $state_dropdown_class = $state_text_class = $state_style = $text_style = $state_field_id = '';

                            if ( empty( $state_value ) ) {
                                $state_value = $this->defaultState;

                                // For backwards compatibility (Canadian address type used to store the default state into the defaultProvince property).
                                if ( $this->addressType == 'canadian' && ! empty( $this->defaultProvince ) ) {
                                    $state_value = $this->defaultProvince;
                                }
                            }

                            $address_type        = empty( $this->addressType ) ? $this->get_default_address_type( $form_id ) : $this->addressType;
                            $address_types       = $this->get_address_types( $form_id );
                            $has_state_drop_down = isset( $address_types[ $address_type ]['states'] ) && is_array( $address_types[ $address_type ]['states'] );

                            if ( $is_admin && rgget('view') != 'entry' ) {
                                $state_dropdown_class = "class='state_dropdown'";
                                $state_text_class     = "class='state_text'";
                                $state_style          = ! $has_state_drop_down ? "style='display:none;'" : '';
                                $text_style           = $has_state_drop_down ? "style='display:none;'" : '';
                                $state_field_id       = '';
                            } else {
                                // ID only displayed on front end.
                                $state_field_id = "id='" . $field_id . "_4'";
                            }
                   
                            $tabindex           = $this->get_tabindex();
                            $state_input        = GFFormsModel::get_input( $this, $this->id . '.4' );
                            $state_placeholder  = GFCommon::get_input_placeholder_value( $state_input );
                            $state_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $state_input ) : '';
                            $states             = empty( $address_types[ $address_type ]['states'] ) ? array() : $address_types[ $address_type ]['states'];
                            $state_dropdown     = sprintf( "<select name='input_%d.4' %s {$tabindex} %s {$state_dropdown_class} {$state_style} {$aria_attributes} {$state_autocomplete} {$this->maybe_add_aria_describedby( $address_state_field_input, $field_id, $this['formId'] )}>%s</select>", $id, $state_field_id, $disabled_text, $this->get_state_dropdown( $states, $state_value, $state_placeholder ) );
                            
                            $tabindex                    = $this->get_tabindex();
                            $state_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $state_input );
                            $state_text                  = sprintf( "<input type='text' name='input_%d.4' %s value='%s' {$tabindex} %s {$state_text_class} {$text_style} {$state_placeholder_attribute} {$aria_attributes} {$state_autocomplete} {$this->maybe_add_aria_describedby( $address_state_field_input, $field_id, $this['formId'] )}/>", $id, $state_field_id, $state_value, $disabled_text );
                            
                            if ( $is_admin && rgget('view') != 'entry' ) {
                                return $state_dropdown . $state_text;
                            } elseif ( $has_state_drop_down ) {
                                return $state_dropdown;
                            } else {
                                return $state_text;
                            }
                        }

                        /**
                         * Returns a list of countries.
                         *
                         * @since Unknown
                         * @since 2.4     Updated to use ISO 3166-1 list of countries.
                         * @since 2.4.20  Updated to use GF_Field_Address::get_default_countries() and to sort the countries.
                         *
                         * @return array
                         */
                        public function get_countries() {

                            $countries = array_values( $this->get_default_countries() );
                            sort( $countries );

                            /**
                             * A list of countries displayed in the Address field country drop down.
                             *
                             * @since Unknown
                             *
                             * @param array $countries ISO 3166-1 list of countries.
                             */
                            return apply_filters( 'gform_countries', $countries );
                            
                        }
                        
                        /**
                         * Returns the default array of countries using the ISO 3166-1 alpha-2 code as the key to the country name.
                         *
                         * @since 2.4.20
                         *
                         * @return array
                        */                
                        public function get_default_countries() {
                            return array(
                                'AF' => __( 'Afghanistan', 'gravityforms' ),
                                'AX' => __( 'Åland Islands', 'gravityforms' ),
                                'AL' => __( 'Albania', 'gravityforms' ),
                                'DZ' => __( 'Algeria', 'gravityforms' ),
                                'AS' => __( 'American Samoa', 'gravityforms' ),
                                'AD' => __( 'Andorra', 'gravityforms' ),
                                'AO' => __( 'Angola', 'gravityforms' ),
                                'AI' => __( 'Anguilla', 'gravityforms' ),
                                'AQ' => __( 'Antarctica', 'gravityforms' ),
                                'AG' => __( 'Antigua and Barbuda', 'gravityforms' ),
                                'AR' => __( 'Argentina', 'gravityforms' ),
                                'AM' => __( 'Armenia', 'gravityforms' ),
                                'AW' => __( 'Aruba', 'gravityforms' ),
                                'AU' => __( 'Australia', 'gravityforms' ),
                                'AT' => __( 'Austria', 'gravityforms' ),
                                'AZ' => __( 'Azerbaijan', 'gravityforms' ),
                                'BS' => __( 'Bahamas', 'gravityforms' ),
                                'BH' => __( 'Bahrain', 'gravityforms' ),
                                'BD' => __( 'Bangladesh', 'gravityforms' ),
                                'BB' => __( 'Barbados', 'gravityforms' ),
                                'BY' => __( 'Belarus', 'gravityforms' ),
                                'BE' => __( 'Belgium', 'gravityforms' ),
                                'BZ' => __( 'Belize', 'gravityforms' ),
                                'BJ' => __( 'Benin', 'gravityforms' ),
                                'BM' => __( 'Bermuda', 'gravityforms' ),
                                'BT' => __( 'Bhutan', 'gravityforms' ),
                                'BO' => __( 'Bolivia', 'gravityforms' ),
                                'BQ' => __( 'Bonaire, Sint Eustatius and Saba', 'gravityforms' ),
                                'BA' => __( 'Bosnia and Herzegovina', 'gravityforms' ),
                                'BW' => __( 'Botswana', 'gravityforms' ),
                                'BV' => __( 'Bouvet Island', 'gravityforms' ),
                                'BR' => __( 'Brazil', 'gravityforms' ),
                                'IO' => __( 'British Indian Ocean Territory', 'gravityforms' ),
                                'BN' => __( 'Brunei Darussalam', 'gravityforms' ),
                                'BG' => __( 'Bulgaria', 'gravityforms' ),
                                'BF' => __( 'Burkina Faso', 'gravityforms' ),
                                'BI' => __( 'Burundi', 'gravityforms' ),
                                'CV' => __( 'Cabo Verde', 'gravityforms' ),
                                'KH' => __( 'Cambodia', 'gravityforms' ),
                                'CM' => __( 'Cameroon', 'gravityforms' ),
                                'CA' => __( 'Canada', 'gravityforms' ),
                                'KY' => __( 'Cayman Islands', 'gravityforms' ),
                                'CF' => __( 'Central African Republic', 'gravityforms' ),
                                'TD' => __( 'Chad', 'gravityforms' ),
                                'CL' => __( 'Chile', 'gravityforms' ),
                                'CN' => __( 'China', 'gravityforms' ),
                                'CX' => __( 'Christmas Island', 'gravityforms' ),
                                'CC' => __( 'Cocos Islands', 'gravityforms' ),
                                'CO' => __( 'Colombia', 'gravityforms' ),
                                'KM' => __( 'Comoros', 'gravityforms' ),
                                'CD' => __( 'Congo, Democratic Republic of the', 'gravityforms' ),
                                'CG' => __( 'Congo', 'gravityforms' ),
                                'CK' => __( 'Cook Islands', 'gravityforms' ),
                                'CR' => __( 'Costa Rica', 'gravityforms' ),
                                'CI' => __( "Côte d'Ivoire", 'gravityforms' ),
                                'HR' => __( 'Croatia', 'gravityforms' ),
                                'CU' => __( 'Cuba', 'gravityforms' ),
                                'CW' => __( 'Curaçao', 'gravityforms' ),
                                'CY' => __( 'Cyprus', 'gravityforms' ),
                                'CZ' => __( 'Czechia', 'gravityforms' ),
                                'DK' => __( 'Denmark', 'gravityforms' ),
                                'DJ' => __( 'Djibouti', 'gravityforms' ),
                                'DM' => __( 'Dominica', 'gravityforms' ),
                                'DO' => __( 'Dominican Republic', 'gravityforms' ),
                                'EC' => __( 'Ecuador', 'gravityforms' ),
                                'EG' => __( 'Egypt', 'gravityforms' ),
                                'SV' => __( 'El Salvador', 'gravityforms' ),
                                'GQ' => __( 'Equatorial Guinea', 'gravityforms' ),
                                'ER' => __( 'Eritrea', 'gravityforms' ),
                                'EE' => __( 'Estonia', 'gravityforms' ),
                                'SZ' => __( 'Eswatini', 'gravityforms' ),
                                'ET' => __( 'Ethiopia', 'gravityforms' ),
                                'FK' => __( 'Falkland Islands', 'gravityforms' ),
                                'FO' => __( 'Faroe Islands', 'gravityforms' ),
                                'FJ' => __( 'Fiji', 'gravityforms' ),
                                'FI' => __( 'Finland', 'gravityforms' ),
                                'FR' => __( 'France', 'gravityforms' ),
                                'GF' => __( 'French Guiana', 'gravityforms' ),
                                'PF' => __( 'French Polynesia', 'gravityforms' ),
                                'TF' => __( 'French Southern Territories', 'gravityforms' ),
                                'GA' => __( 'Gabon', 'gravityforms' ),
                                'GM' => __( 'Gambia', 'gravityforms' ),
                                'GE' => _x( 'Georgia', 'Country', 'gravityforms' ),
                                'DE' => __( 'Germany', 'gravityforms' ),
                                'GH' => __( 'Ghana', 'gravityforms' ),
                                'GI' => __( 'Gibraltar', 'gravityforms' ),
                                'GR' => __( 'Greece', 'gravityforms' ),
                                'GL' => __( 'Greenland', 'gravityforms' ),
                                'GD' => __( 'Grenada', 'gravityforms' ),
                                'GP' => __( 'Guadeloupe', 'gravityforms' ),
                                'GU' => __( 'Guam', 'gravityforms' ),
                                'GT' => __( 'Guatemala', 'gravityforms' ),
                                'GG' => __( 'Guernsey', 'gravityforms' ),
                                'GN' => __( 'Guinea', 'gravityforms' ),
                                'GW' => __( 'Guinea-Bissau', 'gravityforms' ),
                                'GY' => __( 'Guyana', 'gravityforms' ),
                                'HT' => __( 'Haiti', 'gravityforms' ),
                                'HM' => __( 'Heard Island and McDonald Islands', 'gravityforms' ),
                                'VA' => __( 'Holy See', 'gravityforms' ),
                                'HN' => __( 'Honduras', 'gravityforms' ),
                                'HK' => __( 'Hong Kong', 'gravityforms' ),
                                'HU' => __( 'Hungary', 'gravityforms' ),
                                'IS' => __( 'Iceland', 'gravityforms' ),
                                'IN' => __( 'India', 'gravityforms' ),
                                'ID' => __( 'Indonesia', 'gravityforms' ),
                                'IR' => __( 'Iran', 'gravityforms' ),
                                'IQ' => __( 'Iraq', 'gravityforms' ),
                                'IE' => __( 'Ireland', 'gravityforms' ),
                                'IM' => __( 'Isle of Man', 'gravityforms' ),
                                'IL' => __( 'Israel', 'gravityforms' ),
                                'IT' => __( 'Italy', 'gravityforms' ),
                                'JM' => __( 'Jamaica', 'gravityforms' ),
                                'JP' => __( 'Japan', 'gravityforms' ),
                                'JE' => __( 'Jersey', 'gravityforms' ),
                                'JO' => __( 'Jordan', 'gravityforms' ),
                                'KZ' => __( 'Kazakhstan', 'gravityforms' ),
                                'KE' => __( 'Kenya', 'gravityforms' ),
                                'KI' => __( 'Kiribati', 'gravityforms' ),
                                'KP' => __( "Korea, Democratic People's Republic of", 'gravityforms' ),
                                'KR' => __( 'Korea, Republic of', 'gravityforms' ),
                                'KW' => __( 'Kuwait', 'gravityforms' ),
                                'KG' => __( 'Kyrgyzstan', 'gravityforms' ),
                                'LA' => __( "Lao People's Democratic Republic", 'gravityforms' ),
                                'LV' => __( 'Latvia', 'gravityforms' ),
                                'LB' => __( 'Lebanon', 'gravityforms' ),
                                'LS' => __( 'Lesotho', 'gravityforms' ),
                                'LR' => __( 'Liberia', 'gravityforms' ),
                                'LY' => __( 'Libya', 'gravityforms' ),
                                'LI' => __( 'Liechtenstein', 'gravityforms' ),
                                'LT' => __( 'Lithuania', 'gravityforms' ),
                                'LU' => __( 'Luxembourg', 'gravityforms' ),
                                'MO' => __( 'Macao', 'gravityforms' ),
                                'MG' => __( 'Madagascar', 'gravityforms' ),
                                'MW' => __( 'Malawi', 'gravityforms' ),
                                'MY' => __( 'Malaysia', 'gravityforms' ),
                                'MV' => __( 'Maldives', 'gravityforms' ),
                                'ML' => __( 'Mali', 'gravityforms' ),
                                'MT' => __( 'Malta', 'gravityforms' ),
                                'MH' => __( 'Marshall Islands', 'gravityforms' ),
                                'MQ' => __( 'Martinique', 'gravityforms' ),
                                'MR' => __( 'Mauritania', 'gravityforms' ),
                                'MU' => __( 'Mauritius', 'gravityforms' ),
                                'YT' => __( 'Mayotte', 'gravityforms' ),
                                'MX' => __( 'Mexico', 'gravityforms' ),
                                'FM' => __( 'Micronesia', 'gravityforms' ),
                                'MD' => __( 'Moldova', 'gravityforms' ),
                                'MC' => __( 'Monaco', 'gravityforms' ),
                                'MN' => __( 'Mongolia', 'gravityforms' ),
                                'ME' => __( 'Montenegro', 'gravityforms' ),
                                'MS' => __( 'Montserrat', 'gravityforms' ),
                                'MA' => __( 'Morocco', 'gravityforms' ),
                                'MZ' => __( 'Mozambique', 'gravityforms' ),
                                'MM' => __( 'Myanmar', 'gravityforms' ),
                                'NA' => __( 'Namibia', 'gravityforms' ),
                                'NR' => __( 'Nauru', 'gravityforms' ),
                                'NP' => __( 'Nepal', 'gravityforms' ),
                                'NL' => __( 'Netherlands', 'gravityforms' ),
                                'NC' => __( 'New Caledonia', 'gravityforms' ),
                                'NZ' => __( 'New Zealand', 'gravityforms' ),
                                'NI' => __( 'Nicaragua', 'gravityforms' ),
                                'NE' => __( 'Niger', 'gravityforms' ),
                                'NG' => __( 'Nigeria', 'gravityforms' ),
                                'NU' => __( 'Niue', 'gravityforms' ),
                                'NF' => __( 'Norfolk Island', 'gravityforms' ),
                                'MK' => __( 'North Macedonia', 'gravityforms' ),
                                'MP' => __( 'Northern Mariana Islands', 'gravityforms' ),
                                'NO' => __( 'Norway', 'gravityforms' ),
                                'OM' => __( 'Oman', 'gravityforms' ),
                                'PK' => __( 'Pakistan', 'gravityforms' ),
                                'PW' => __( 'Palau', 'gravityforms' ),
                                'PS' => __( 'Palestine, State of', 'gravityforms' ),
                                'PA' => __( 'Panama', 'gravityforms' ),
                                'PG' => __( 'Papua New Guinea', 'gravityforms' ),
                                'PY' => __( 'Paraguay', 'gravityforms' ),
                                'PE' => __( 'Peru', 'gravityforms' ),
                                'PH' => __( 'Philippines', 'gravityforms' ),
                                'PN' => __( 'Pitcairn', 'gravityforms' ),
                                'PL' => __( 'Poland', 'gravityforms' ),
                                'PT' => __( 'Portugal', 'gravityforms' ),
                                'PR' => __( 'Puerto Rico', 'gravityforms' ),
                                'QA' => __( 'Qatar', 'gravityforms' ),
                                'RE' => __( 'Réunion', 'gravityforms' ),
                                'RO' => __( 'Romania', 'gravityforms' ),
                                'RU' => __( 'Russian Federation', 'gravityforms' ),
                                'RW' => __( 'Rwanda', 'gravityforms' ),
                                'BL' => __( 'Saint Barthélemy', 'gravityforms' ),
                                'SH' => __( 'Saint Helena, Ascension and Tristan da Cunha', 'gravityforms' ),
                                'KN' => __( 'Saint Kitts and Nevis', 'gravityforms' ),
                                'LC' => __( 'Saint Lucia', 'gravityforms' ),
                                'MF' => __( 'Saint Martin', 'gravityforms' ),
                                'PM' => __( 'Saint Pierre and Miquelon', 'gravityforms' ),
                                'VC' => __( 'Saint Vincent and the Grenadines', 'gravityforms' ),
                                'WS' => __( 'Samoa', 'gravityforms' ),
                                'SM' => __( 'San Marino', 'gravityforms' ),
                                'ST' => __( 'Sao Tome and Principe', 'gravityforms' ),
                                'SA' => __( 'Saudi Arabia', 'gravityforms' ),
                                'SN' => __( 'Senegal', 'gravityforms' ),
                                'RS' => __( 'Serbia', 'gravityforms' ),
                                'SC' => __( 'Seychelles', 'gravityforms' ),
                                'SL' => __( 'Sierra Leone', 'gravityforms' ),
                                'SG' => __( 'Singapore', 'gravityforms' ),
                                'SX' => __( 'Sint Maarten', 'gravityforms' ),
                                'SK' => __( 'Slovakia', 'gravityforms' ),
                                'SI' => __( 'Slovenia', 'gravityforms' ),
                                'SB' => __( 'Solomon Islands', 'gravityforms' ),
                                'SO' => __( 'Somalia', 'gravityforms' ),
                                'ZA' => __( 'South Africa', 'gravityforms' ),
                                'GS' => _x( 'South Georgia and the South Sandwich Islands', 'Country', 'gravityforms' ),
                                'SS' => __( 'South Sudan', 'gravityforms' ),
                                'ES' => __( 'Spain', 'gravityforms' ),
                                'LK' => __( 'Sri Lanka', 'gravityforms' ),
                                'SD' => __( 'Sudan', 'gravityforms' ),
                                'SR' => __( 'Suriname', 'gravityforms' ),
                                'SJ' => __( 'Svalbard and Jan Mayen', 'gravityforms' ),
                                'SE' => __( 'Sweden', 'gravityforms' ),
                                'CH' => __( 'Switzerland', 'gravityforms' ),
                                'SY' => __( 'Syria Arab Republic', 'gravityforms' ),
                                'TW' => __( 'Taiwan', 'gravityforms' ),
                                'TJ' => __( 'Tajikistan', 'gravityforms' ),
                                'TZ' => __( 'Tanzania, the United Republic of', 'gravityforms' ),
                                'TH' => __( 'Thailand', 'gravityforms' ),
                                'TL' => __( 'Timor-Leste', 'gravityforms' ),
                                'TG' => __( 'Togo', 'gravityforms' ),
                                'TK' => __( 'Tokelau', 'gravityforms' ),
                                'TO' => __( 'Tonga', 'gravityforms' ),
                                'TT' => __( 'Trinidad and Tobago', 'gravityforms' ),
                                'TN' => __( 'Tunisia', 'gravityforms' ),
                                'TR' => __( 'Türkiye', 'gravityforms' ),
                                'TM' => __( 'Turkmenistan', 'gravityforms' ),
                                'TC' => __( 'Turks and Caicos Islands', 'gravityforms' ),
                                'TV' => __( 'Tuvalu', 'gravityforms' ),
                                'UG' => __( 'Uganda', 'gravityforms' ),
                                'UA' => __( 'Ukraine', 'gravityforms' ),
                                'AE' => __( 'United Arab Emirates', 'gravityforms' ),
                                'GB' => __( 'United Kingdom', 'gravityforms' ),
                                'US' => __( 'United States', 'gravityforms' ),
                                'UY' => __( 'Uruguay', 'gravityforms' ),
                                'UM' => __( 'US Minor Outlying Islands', 'gravityforms' ),
                                'UZ' => __( 'Uzbekistan', 'gravityforms' ),
                                'VU' => __( 'Vanuatu', 'gravityforms' ),
                                'VE' => __( 'Venezuela', 'gravityforms' ),
                                'VN' => __( 'Viet Nam', 'gravityforms' ),
                                'VG' => __( 'Virgin Islands, British', 'gravityforms' ),
                                'VI' => __( 'Virgin Islands, U.S.', 'gravityforms' ),
                                'WF' => __( 'Wallis and Futuna', 'gravityforms' ),
                                'EH' => __( 'Western Sahara', 'gravityforms' ),
                                'YE' => __( 'Yemen', 'gravityforms' ),
                                'ZM' => __( 'Zambia', 'gravityforms' ),
                                'ZW' => __( 'Zimbabwe', 'gravityforms' ),
                            );
                        }

                  	/**
                	 * Returns the ISO 3166-1 alpha-2 code for the supplied country name.
                         *
                	 * @since Unknown
                    	 *
                	 * @param string $country_name The country name.
                	 *
                         * @return string|null
                        */
                        public function get_country_code($country_name) {
                                    $codes = $this->get_country_codes();

                                    return rgar($codes, GFCommon::safe_strtoupper($country_name));
                        }

                        /**
                         * Returns the default countries array updated to use the uppercase country name as the key to the ISO 3166-1 alpha-2 code.
                         *
                         * @since Unknown
                         * @since 2.4     Updated to use ISO 3166-1 list of countries.
                         * @since 2.4.20  Updated to use GF_Field_Address::get_default_countries().
                         *
                         * @return array
                         */
                        public function get_country_codes() {
                                $countries = array_map( array( 'GFCommon', 'safe_strtoupper' ), $this->get_default_countries() );

                                return array_flip( $countries );
                        }

                        public function get_us_states() {
                            return apply_filters(
                                'gform_us_states', array(
                                    __( 'Alabama', 'gravityforms' ),
                                    __( 'Alaska', 'gravityforms' ),
                                    __( 'American Samoa', 'gravityforms' ),
                                    __( 'Arizona', 'gravityforms' ),
                                    __( 'Arkansas', 'gravityforms' ),
                                    __( 'California', 'gravityforms' ),
                                    __( 'Colorado', 'gravityforms' ),
                                    __( 'Connecticut', 'gravityforms' ),
                                    __( 'Delaware', 'gravityforms' ),
                                    __( 'District of Columbia', 'gravityforms' ),
                                    __( 'Florida', 'gravityforms' ),
                                    _x( 'Georgia', 'US State', 'gravityforms' ),
                                    __( 'Guam', 'gravityforms' ),
                                    __( 'Hawaii', 'gravityforms' ),
                                    __( 'Idaho', 'gravityforms' ),
                                    __( 'Illinois', 'gravityforms' ),
                                    __( 'Indiana', 'gravityforms' ),
                                    __( 'Iowa', 'gravityforms' ),
                                    __( 'Kansas', 'gravityforms' ),
                                    __( 'Kentucky', 'gravityforms' ),
                                    __( 'Louisiana', 'gravityforms' ),
                                    __( 'Maine', 'gravityforms' ),
                                    __( 'Maryland', 'gravityforms' ),
                                    __( 'Massachusetts', 'gravityforms' ),
                                    __( 'Michigan', 'gravityforms' ),
                                    __( 'Minnesota', 'gravityforms' ),
                                    __( 'Mississippi', 'gravityforms' ),
                                    __( 'Missouri', 'gravityforms' ),
                                    __( 'Montana', 'gravityforms' ),
                                    __( 'Nebraska', 'gravityforms' ),
                                    __( 'Nevada', 'gravityforms' ),
                                    __( 'New Hampshire', 'gravityforms' ),
                                    __( 'New Jersey', 'gravityforms' ),
                                    __( 'New Mexico', 'gravityforms' ),
                                    __( 'New York', 'gravityforms' ),
                                    __( 'North Carolina', 'gravityforms' ),
                                    __( 'North Dakota', 'gravityforms' ),
                                    __( 'Northern Mariana Islands', 'gravityforms' ),
                                    __( 'Ohio', 'gravityforms' ),
                                    __( 'Oklahoma', 'gravityforms' ),
                                    __( 'Oregon', 'gravityforms' ),
                                    __( 'Pennsylvania', 'gravityforms' ),
                                    __( 'Puerto Rico', 'gravityforms' ),
                                    __( 'Rhode Island', 'gravityforms' ),
                                    __( 'South Carolina', 'gravityforms' ),
                                    __( 'South Dakota', 'gravityforms' ),
                                    __( 'Tennessee', 'gravityforms' ),
                                    __( 'Texas', 'gravityforms' ),
                                    __( 'Utah', 'gravityforms' ),                                    
                                    __( 'U.S. Virgin Islands', 'gravityforms' ),
                                    __( 'Vermont', 'gravityforms' ),
                                    __( 'Virginia', 'gravityforms' ),
                                    __( 'Washington', 'gravityforms' ),
                                    __( 'West Virginia', 'gravityforms' ),
                                    __( 'Wisconsin', 'gravityforms' ),
                                    __( 'Wyoming', 'gravityforms' ),
                                    __( 'Armed Forces Americas', 'gravityforms' ),
                                    __( 'Armed Forces Europe', 'gravityforms' ),
                                    __( 'Armed Forces Pacific', 'gravityforms' ),
                                )
                            );
                        }

                        public function get_us_state_code( $state_name ) {
                            $states = array(
                                GFCommon::safe_strtoupper( __( 'Alabama', 'gravityforms' ) )                  => 'AL',
                                GFCommon::safe_strtoupper( __( 'Alaska', 'gravityforms' ) )                   => 'AK',
                                GFCommon::safe_strtoupper( __( 'American Samoa', 'gravityforms' ) )           => 'AS',
                                GFCommon::safe_strtoupper( __( 'Arizona', 'gravityforms' ) )                  => 'AZ',
                                GFCommon::safe_strtoupper( __( 'Arkansas', 'gravityforms' ) )                 => 'AR',
                                GFCommon::safe_strtoupper( __( 'California', 'gravityforms' ) )               => 'CA',
                                GFCommon::safe_strtoupper( __( 'Colorado', 'gravityforms' ) )                 => 'CO',
                                GFCommon::safe_strtoupper( __( 'Connecticut', 'gravityforms' ) )              => 'CT',
                                GFCommon::safe_strtoupper( __( 'Delaware', 'gravityforms' ) )                 => 'DE',
                                GFCommon::safe_strtoupper( __( 'District of Columbia', 'gravityforms' ) )     => 'DC',
                                GFCommon::safe_strtoupper( __( 'Florida', 'gravityforms' ) )                  => 'FL',
                                GFCommon::safe_strtoupper( _x( 'Georgia', 'US State', 'gravityforms' ) )      => 'GA',
                                GFCommon::safe_strtoupper( __( 'Guam', 'gravityforms' ) )                     => 'GU',
                                GFCommon::safe_strtoupper( __( 'Hawaii', 'gravityforms' ) )                   => 'HI',
                                GFCommon::safe_strtoupper( __( 'Idaho', 'gravityforms' ) )                    => 'ID',
                                GFCommon::safe_strtoupper( __( 'Illinois', 'gravityforms' ) )                 => 'IL',
                                GFCommon::safe_strtoupper( __( 'Indiana', 'gravityforms' ) )                  => 'IN',
                                GFCommon::safe_strtoupper( __( 'Iowa', 'gravityforms' ) )                     => 'IA',
                                GFCommon::safe_strtoupper( __( 'Kansas', 'gravityforms' ) )                   => 'KS',
                                GFCommon::safe_strtoupper( __( 'Kentucky', 'gravityforms' ) )                 => 'KY',
                                GFCommon::safe_strtoupper( __( 'Louisiana', 'gravityforms' ) )                => 'LA',
                                GFCommon::safe_strtoupper( __( 'Maine', 'gravityforms' ) )                    => 'ME',
                                GFCommon::safe_strtoupper( __( 'Maryland', 'gravityforms' ) )                 => 'MD',
                                GFCommon::safe_strtoupper( __( 'Massachusetts', 'gravityforms' ) )            => 'MA',
                                GFCommon::safe_strtoupper( __( 'Michigan', 'gravityforms' ) )                 => 'MI',
                                GFCommon::safe_strtoupper( __( 'Minnesota', 'gravityforms' ) )                => 'MN',
                                GFCommon::safe_strtoupper( __( 'Mississippi', 'gravityforms' ) )              => 'MS',
                                GFCommon::safe_strtoupper( __( 'Missouri', 'gravityforms' ) )                 => 'MO',
                                GFCommon::safe_strtoupper( __( 'Montana', 'gravityforms' ) )                  => 'MT',
                                GFCommon::safe_strtoupper( __( 'Nebraska', 'gravityforms' ) )                 => 'NE',
                                GFCommon::safe_strtoupper( __( 'Nevada', 'gravityforms' ) )                   => 'NV',
                                GFCommon::safe_strtoupper( __( 'New Hampshire', 'gravityforms' ) )            => 'NH',
                                GFCommon::safe_strtoupper( __( 'New Jersey', 'gravityforms' ) )               => 'NJ',
                                GFCommon::safe_strtoupper( __( 'New Mexico', 'gravityforms' ) )               => 'NM',
                                GFCommon::safe_strtoupper( __( 'New York', 'gravityforms' ) )                 => 'NY',
                                GFCommon::safe_strtoupper( __( 'North Carolina', 'gravityforms' ) )           => 'NC',
                                GFCommon::safe_strtoupper( __( 'North Dakota', 'gravityforms' ) )             => 'ND',
                                GFCommon::safe_strtoupper( __( 'Northern Mariana Islands', 'gravityforms' ) ) => 'MP',
                                GFCommon::safe_strtoupper( __( 'Ohio', 'gravityforms' ) )                     => 'OH',
                                GFCommon::safe_strtoupper( __( 'Oklahoma', 'gravityforms' ) )                 => 'OK',
                                GFCommon::safe_strtoupper( __( 'Oregon', 'gravityforms' ) )                   => 'OR',
                                GFCommon::safe_strtoupper( __( 'Pennsylvania', 'gravityforms' ) )             => 'PA',
                                GFCommon::safe_strtoupper( __( 'Puerto Rico', 'gravityforms' ) )              => 'PR',
                                GFCommon::safe_strtoupper( __( 'Rhode Island', 'gravityforms' ) )             => 'RI',
                                GFCommon::safe_strtoupper( __( 'South Carolina', 'gravityforms' ) )           => 'SC',
                                GFCommon::safe_strtoupper( __( 'South Dakota', 'gravityforms' ) )             => 'SD',
                                GFCommon::safe_strtoupper( __( 'Tennessee', 'gravityforms' ) )                => 'TN',
                                GFCommon::safe_strtoupper( __( 'Texas', 'gravityforms' ) )                    => 'TX',
                                GFCommon::safe_strtoupper( __( 'Utah', 'gravityforms' ) )                     => 'UT',
                                GFCommon::safe_strtoupper( __( 'U.S. Virgin Islands', 'gravityforms' ) )      => 'VI',                                
                                GFCommon::safe_strtoupper( __( 'Vermont', 'gravityforms' ) )                  => 'VT',
                                GFCommon::safe_strtoupper( __( 'Virginia', 'gravityforms' ) )                 => 'VA',
                                GFCommon::safe_strtoupper( __( 'Washington', 'gravityforms' ) )               => 'WA',
                                GFCommon::safe_strtoupper( __( 'West Virginia', 'gravityforms' ) )            => 'WV',
                                GFCommon::safe_strtoupper( __( 'Wisconsin', 'gravityforms' ) )                => 'WI',
                                GFCommon::safe_strtoupper( __( 'Wyoming', 'gravityforms' ) )                  => 'WY',
                                
                                GFCommon::safe_strtoupper( __( 'Armed Forces Americas', 'gravityforms' ) )    => 'AA',
                                GFCommon::safe_strtoupper( __( 'Armed Forces Europe', 'gravityforms' ) )      => 'AE',
                                GFCommon::safe_strtoupper( __( 'Armed Forces Pacific', 'gravityforms' ) )     => 'AP',
                            );

                            $state_name = GFCommon::safe_strtoupper( $state_name );
                            $code       = isset( $states[ $state_name ] ) ? $states[ $state_name ] : $state_name;

                            return $code;
                        }
                        
                        public function get_canadian_provinces() {
                            return array(
                                __( 'Alberta', 'gravityforms' ),
                                __( 'British Columbia', 'gravityforms' ),
                                __( 'Manitoba', 'gravityforms' ),
                                __( 'New Brunswick', 'gravityforms' ),
                                __( 'Newfoundland and Labrador', 'gravityforms' ),
                                __( 'Northwest Territories', 'gravityforms' ),
                                __( 'Nova Scotia', 'gravityforms' ),
                                __( 'Nunavut', 'gravityforms' ),
                                __( 'Ontario', 'gravityforms' ),
                                __( 'Prince Edward Island', 'gravityforms' ),
                                __( 'Quebec', 'gravityforms' ),
                                __( 'Saskatchewan', 'gravityforms' ),
                                __( 'Yukon', 'gravityforms' )
                            );
                        }

                        public function get_state_dropdown($states, $selected_state = '', $placeholder = '') {
                            $str = '';
                            foreach ($states as $code => $state) {
                                if (is_array($state)) {
                                    $str .= sprintf('<optgroup label="%1$s">%2$s</optgroup>', esc_attr($code), $this->get_state_dropdown($state, $selected_state, $placeholder));
                                } else {
                                    if (is_numeric($code)) {
                                        $code = $state;
                                    }
                                    if (empty($state)) {
                                        $state = $placeholder;
                                    }

                                    $str .= $this->get_select_option($code, $state, $selected_state);
                                }
                            }

                            return $str;
                        }

                        /**
                         * Returns the option tag for the current choice.
                         *
                         * @param string $value The choice value.
                         * @param string $label The choice label.
                         * @param string $selected_value The value for the selected choice.
                         *
                         * @return string
                         */
                        public function get_select_option( $value, $label, $selected_value ) {
                                $selected = $value == $selected_value ? "selected='selected'" : '';

                                return sprintf( "<option value='%s' %s>%s</option>", esc_attr( $value ), $selected, esc_html( $label ) );
                        }

                        public function get_us_state_dropdown( $selected_state = '' ) {
                            $states = array_merge( array( '' ), $this->get_us_states() );
                            $str    = '';
                            foreach ( $states as $code => $state ) {
                                if ( is_numeric( $code ) ) {
                                    $code = $state;
                                }

                                $selected = $code == $selected_state ? "selected='selected'" : '';
                                $str .= "<option value='" . esc_attr( $code ) . "' $selected>" . esc_html( $state ) . '</option>';
                            }

                            return $str;
                        }

                       public function get_canadian_provinces_dropdown( $selected_province = '' ) {
                            $states = array_merge( array( '' ), $this->get_canadian_provinces() );
                            $str    = '';
                            foreach ( $states as $state ) {
                                $selected = $state == $selected_province ? "selected='selected'" : '';
                                $str .= "<option value='" . esc_attr( $state ) . "' $selected>" . esc_html( $state ) . '</option>';
                            }

                            return $str;
                        }

                        public function get_country_dropdown( $selected_country = '', $placeholder = '' ) {
                            $str       = '';
                            $selected_country = strtolower( $selected_country );
                            $countries = array_merge( array( '' ), $this->get_countries() );
                            foreach ( $countries as $code => $country ) {
                                if ( is_numeric( $code ) ) {
                                    $code = $country;
        			}
                		if ( empty( $country ) ) {
                        		$country = $placeholder;
                                }
                                $selected = strtolower( esc_attr( $code ) ) == $selected_country ? "selected='selected'" : '';
                                $str .= "<option value='" . esc_attr( $code ) . "' $selected>" . esc_html( $country ) . '</option>';
                            }
                            
                            return $str;
                	}

                        public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen') {
                            if (is_array($value)) {
                                $street_value = trim(rgget($this->id . '.1', $value));
                                $street2_value = trim(rgget($this->id . '.2', $value));
                                $city_value = trim(rgget($this->id . '.3', $value));
                                $state_value = trim(rgget($this->id . '.4', $value));
                                $zip_value = trim(rgget($this->id . '.5', $value));
                                $country_value = trim(rgget($this->id . '.6', $value));

                                if ($format === 'html') {
                                    $street_value = esc_html($street_value);
                                    $street2_value = esc_html($street2_value);
                                    $city_value = esc_html($city_value);
                                    $state_value = esc_html($state_value);
                                    $zip_value = esc_html($zip_value);
                                    $country_value = esc_html($country_value);

                                    $line_break = '<br />';
                                } else {
                                    $line_break = "\n";
                                }

                                /**
                                 * Filters the format that the address is displayed in.
                                 *
                                 * @since Unknown
                                 *
                                 * @param string           'default' The format to use. Defaults to 'default'.
                                 * @param GF_Field_Address $this     An instance of the GF_Field_Address object.
                                 */
                                $address_display_format = apply_filters('gform_address_display_format', 'default', $this);
                                if ($address_display_format == 'zip_before_city') {
                                    /*
                                      Sample:
                                      3333 Some Street
                                      suite 16
                                      2344 City, State
                                      Country
                                     */

                                    $addr_ary = array();
                                    $addr_ary[] = $street_value;

                                    if (!empty($street2_value)) {
                                        $addr_ary[] = $street2_value;
                                    }

                                    $zip_line = trim($zip_value . ' ' . $city_value);
                                    $zip_line .= !empty($zip_line) && !empty($state_value) ? ", {$state_value}" : $state_value;
                                    $zip_line = trim($zip_line);
                                    if (!empty($zip_line)) {
                                        $addr_ary[] = $zip_line;
                                    }

                                    if (!empty($country_value)) {
                                        $addr_ary[] = $country_value;
                                    }

                                    $address = implode('<br />', $addr_ary);
                                    
                                } else {
                                    $address = $street_value;
                                    $address .= !empty($address) && !empty($street2_value) ? $line_break . $street2_value : $street2_value;
                                    $address .= !empty($address) && (!empty($city_value) || !empty($state_value) ) ? $line_break . $city_value : $city_value;
                                    $address .= !empty($address) && !empty($city_value) && !empty($state_value) ? ", $state_value" : $state_value;
                                    $address .= !empty($address) && !empty($zip_value) ? " $zip_value" : $zip_value;
                                    $address .= !empty($address) && !empty($country_value) ? $line_break . $country_value : $country_value;
                                }

                                // Adding map link.
                                /**
                                 * Disables the Google Maps link from displaying in the address field.
                                 *
                                 * @since 1.9
                                 *
                                 * @param bool false Determines if the map link should be disabled. Set to true to disable. Defaults to false.
                                 */
                                $map_link_disabled = apply_filters('gform_disable_address_map_link', false);
                                if (!empty($address) && $format == 'html' && !$map_link_disabled) {
                                    $address_qs = str_replace($line_break, ' ', $address); //replacing <br/> and \n with spaces
                                    $address_qs = urlencode($address_qs);
                                    $address .= "<br/><a href='http://maps.google.com/maps?q={$address_qs}' target='_blank' class='map-it-link'>Map It</a>";
                                }

                                return $address;
                            } else {
                                return '';
                            }
                        }

                       // public function get_input_property($input_id, $property_name) {
                       //     $input = GFFormsModel::get_input($this, $input_id);

                       //       return rgar($input, $property_name);
                       // }

                        public function sanitize_settings() {
                            parent::sanitize_settings();
                            if ($this->addressType) {
                                $this->addressType = wp_strip_all_tags($this->addressType);
                            }

                            if ($this->defaultCountry) {
                                $this->defaultCountry = wp_strip_all_tags($this->defaultCountry);
                            }

                            if ($this->defaultProvince) {
                                $this->defaultProvince = wp_strip_all_tags($this->defaultProvince);
                            }
                            
                            if ( $this->copyValuesOptionLabel ) {
        			$this->copyValuesOptionLabel = wp_strip_all_tags( $this->copyValuesOptionLabel );
                            }
                            
                        }

                        public function get_value_export($entry, $input_id = '', $use_text = false, $is_csv = false) {
                            if (empty($input_id)) {
                                $input_id = $this->id;
                            }

                            if (absint($input_id) == $input_id) {
                                $street_value = str_replace('  ', ' ', trim(rgar($entry, $input_id . '.1')));
                                $street2_value = str_replace('  ', ' ', trim(rgar($entry, $input_id . '.2')));
                                $city_value = str_replace('  ', ' ', trim(rgar($entry, $input_id . '.3')));
                                $state_value = str_replace('  ', ' ', trim(rgar($entry, $input_id . '.4')));
                                $zip_value = trim(rgar($entry, $input_id . '.5'));
                                $country_value = $this->get_country_code(trim(rgar($entry, $input_id . '.6')));

                                $address = $street_value;
                                $address .= !empty($address) && !empty($street2_value) ? "  $street2_value" : $street2_value;
                                $address .= !empty($address) && (!empty($city_value) || !empty($state_value) ) ? ", $city_value," : $city_value;
                                $address .= !empty($address) && !empty($city_value) && !empty($state_value) ? "  $state_value" : $state_value;
                                $address .= !empty($address) && !empty($zip_value) ? "  $zip_value," : $zip_value;
                                $address .= !empty($address) && !empty($country_value) ? "  $country_value" : $country_value;

                                return $address;
                            } else {

                                return rgar($entry, $input_id);
                            }
                        }
                        
                        /**
                        * Removes the "for" attribute in the field label. Inputs are only allowed one label (a11y) and the inputs already have labels.
                        *
                        * @since  2.4
                        * @access public
                        *
                        * @param array $form The Form Object currently being processed.
                        *
                        * @return string
                        */
                        public function get_first_input_id( $form ) {
                            return '';
                        }
                    }
                    // END OF POSTCODES4u ADDRESS FIELD DEFINITION 
                    // -------------------------------------------------------
                    

                    
                    // Add a custom field button to the advanced to the field editor
                    function pc4u_add_pc4uAddress_field($field_groups) {
                        foreach ($field_groups as &$group) {
                            if ($group["name"] == "advanced_fields") { // to add to the Advanced Fields
                                $group["fields"][] = array(
                                    "class" => "button",
                                    "value" => __("Postcode Lookup Address", "gravityforms"),
                                    "onclick" => "StartAddField('pc4uAddress');"
                                );
                                break;
                            }
                        }
                        return $field_groups;
                    }

                    function pc4u_add_pc4uAddress_title($type) {
                        if ($type == 'pc4uAddress')
                            return __('Postcodes4u Address', 'gravityforms');
                    }

                    // ---------------------------------------------------------------------------------
                    function add_uk_counties($address_types, $form_id) {
                        $address_types["uk"] = array(
                            "label" => "UK",
                            "country" => "United Kingdom",
                            "zip_label" => "Postcode",
                            "state_label" => "County",
                            "states" => array(
                                /* Blank (Empty) County */
                                ' ' => ' ',
                                /* English Administrative Counties */
                                'Avon' => 'Avon',
                                'Bedfordshire' => 'Bedfordshire',
                                'Berkshire' => 'Berkshire',
                                'Bristol' => 'Bristol',
                                'Buckinghamshire' => 'Buckinghamshire',
                                'Cambridgeshire' => 'Cambridgeshire',
                                'Cheshire' => 'Cheshire',
                                'Cornwall' => 'Cornwall',
                                'Cumbria' => 'Cumbria',
                                'Derbyshire' => 'Derbyshire',
                                'Devon' => 'Devon',
                                'Dorset' => 'Dorset',
                                'Durham' => 'Durham',
                                'East Yorkshire' => 'East Yorkshire',
                                'East Sussex' => 'East Sussex',
                                'Essex' => 'Essex',
                                'Gloucestershire' => 'Gloucestershire',
                                'Greater Manchester' => 'Greater Manchester',
                                'Hampshire' => 'Hampshire',
                                'Hereford and Worcester' => 'Hereford and Worcester',
                                'Hertfordshire' => 'Hertfordshire',
                                'Isle of Man' => 'Isle of Man',
                                'Isle of Wight' => 'Isle of Wight',
                                'Kent' => 'Kent',
                                'Lancashire' => 'Lancashire',
                                'Leicestershire' => 'Leicestershire',
                                'Lincolnshire' => 'Lincolnshire',
                                'London' => 'London',
                                'Merseyside' => 'Merseyside',
                                'Middlesex' => 'Middlesex',
                                'Norfolk' => 'Norfolk',
                                'North Yorkshire' => 'North Yorkshire',
                                'Northamptonshire' => 'Northamptonshire',
                                'Northumberland' => 'Northumberland',
                                'Nottinghamshire' => 'Nottinghamshire',
                                'Oxfordshire' => 'Oxfordshire',
                                'Rutland' => 'Rutland',
                                'Shropshire' => 'Shropshire',
                                'Somerset' => 'Somerset',
                                'South Yorkshire' => 'South Yorkshire',
                                'Staffordshire' => 'Staffordshire',
                                'Suffolk' => 'Suffolk',
                                'Surrey' => 'Surrey',
                                'Tyne and Wear' => 'Tyne and Wear',
                                'Warwickshire' => 'Warwickshire',
                                'West Midlands' => 'West Midlands',
                                'West Sussex' => 'West Sussex',
                                'West Yorkshire' => 'West Yorkshire',
                                'Wiltshire' => 'Wiltshire',

                                /* Scotish Administrative Counties */
                                'Aberdeenshire' => 'Aberdeenshire',
                                'Angus' => 'Angus',
                                'Argyll and Bute' => 'Argyll and Bute',
                                'Ayrshire' => 'Ayrshire',
                                'Clackmannanshire' => 'Clackmannanshire',
                                'Dumfries and Galloway' => 'Dumfries and Galloway',
                                'Dunbartonshire' => 'Dunbartonshire',
                                'Dundee' => 'Dundee',
                                'East Lothian' => 'East Lothian',
                                'Falkirk' => 'Falkirk',
                                'Fife' => 'Fife',
                                'Glasgow' => 'Glasgow',
                                'Highland' => 'Highland',
                                'Inverclyde' => 'Inverclyde',
                                'Lanarkshire' => 'Lanarkshire',
                                'Midlothian' => 'Midlothian',
                                'Moray' => 'Moray',
                                'Orkney' => 'Orkney',
                                'Perth and Kinross' => 'Perth and Kinross',
                                'Renfrewshire' => 'Renfrewshire',
                                'Scottish Borders' => 'Scottish Borders',
                                'Shetland Isles' => 'Shetland Isles',
                                'Stirlingshire' => 'Stirlingshire',
                                'West Lothian' => 'West Lothian',
                                'Western Isles' => 'Western Isles',

                                /* Welsh Administrative Counties */
                                'Anglesey' => 'Anglesey',
                                'Blaenau Gwent' => 'Blaenau Gwent',
                                'Bridgend' => 'Bridgend',
                                'Caerphilly' => 'Caerphilly',
                                'Cardiff' => 'Cardiff',
                                'Carmarthenshire' => 'Carmarthenshire',
                                'Ceredigion' => 'Ceredigion',
                                'Conwy' => 'Conwy',
                                'Denbighshire' => 'Denbighshire',
                                'Flintshire' => 'Flintshire',
                                'Glamorgan' => 'Glamorgan',
                                'Gwynedd' => 'Gwynedd',
                                'Merthyr Tydfil' => 'Merthyr Tydfil',
                                'Monmouthshire' => 'Monmouthshire',
                                'Neath Port Talbot' => 'Neath Port Talbot',
                                'Newport' => 'Newport',
                                'Pembrokeshire' => 'Pembrokeshire',
                                'Powys' => 'Powys',
                                'Rhondda Cynon Taff' => 'Rhondda Cynon Taff',
                                'Swansea' => 'Swansea',
                                'Torfaen' => 'Torfaen',
                                'Wrexham' => 'Wrexham',

                                /* Northen Ireland Administrative Counties */
                                'County Antrim' => 'County Antrim',
                                'County Armagh' => 'County Armagh',
                                'County Down' => 'County Down',
                                'County Fermanagh' => 'County Fermanagh',
                                'County Londonderry' => 'County Londonderry',
                                'County Tyrone' => 'County Tyrone',
                                'County Londonderry/Derry' => 'County Londonderry/Derry',

                                /* English Additional Administrative Regions */
                                'Barking and Dagenham' => 'Barking and Dagenham',
                                'Barnet' => 'Barnet',
                                'Barnsley' => 'Barnsley',
                                'Bath and North East Somerset' => 'Bath and North East Somerset',
                                'Bedford' => 'Bedford',
                                'Bexley' => 'Bexley',
                                'Birmingham' => 'Birmingham',
                                'Blackburn with Darwen' => 'Blackburn with Darwen',
                                'Blackpool' => 'Blackpool',
                                'Bolton' => 'Bolton',
                                'Bournemouth' => 'Bournemouth',
                                'Bracknell Forest' => 'Bracknell Forest',
                                'Bradford' => 'Bradford',
                                'Brent' => 'Brent',
                                'Brighton and Hove' => 'Brighton and Hove',
                                'Bristol, City Of' => 'Bristol, City Of',
                                'Bromley' => 'Bromley',
                                'Bury' => 'Bury',
                                'Calderdale' => 'Calderdale',
                                'Camden' => 'Camden',
                                'Central Bedfordshire' => 'Central Bedfordshire',
                                'Cheshire East' => 'Cheshire East',
                                'Cheshire West and Chester' =>'Cheshire West and Chester',
                                'City of London' => 'City of London',
                                'City of Westminister' => 'City of Westminister',
                                'County Duram' => 'County Duram',
                                'Coventry' => 'Coventry',
                                'Croydon' => 'Croydon' ,
                                'Darlington' => 'Darlington',
                                'Derby' =>'Derby',
                                'Doncaster' => 'Doncaster',
                                'Dudley' => 'Dudley',
                                'Ealing' => 'Ealing',
                                'East Riding Of Yorkshire' => 'East Riding Of Yorkshire',
                                'Enfield' => 'Enfield',
                                'Gateshead' => 'Gateshead',
                                'Greenwich' => 'Greenwich',
                                'Hackney' => 'Hackney',
                                'Halton' => 'Halton',
                                'Hammersmith and Fulham' => 'Hammersmith and Fulham',
                                'Haringey' => 'Haringey',
                                'Harrow' => 'Harrow',
                                'Hartlepool' => 'Hartlepool',
                                'Havering' => 'Havering',
                                'Hillingdon' => 'Hillingdon',
                                'Hounslow' => 'Hounslow',
                                'Isles Of Scilly' => 'Isles Of Scilly',
                                'Islington' => 'Islington',
                                'Kensington and Chelsea' => 'Kensington and Chelsea',
                                'Kingston Upon Hull, City Of' => 'Kingston Upon Hull, City Of',
                                'Kingston upon Thames' => 'Kingston upon Thames',
                                'Kirklees' => 'Kirklees',
                                'Knowsley' => 'Knowsley',
                                'Lambeth' => 'Lambeth',
                                'Leeds' => 'Leeds',
                                'Leicester' => 'Leicester',
                                'Lewisham' => 'Lewisham',
                                'Liverpool' => 'Liverpool',
                                'Luton' => 'Luton',
                                'Manchester' => 'Manchester',
                                'Medway' => 'Medway',
                                'Merton' => 'Merton',
                                'Middlesbrough' => 'Middlesbrough',
                                'Milton Keynes' => 'Milton Keynes',
                                'Newcastle upon Tyne' => 'Newcastle upon Tyne',
                                'Newham' => 'Newham',
                                'North East Lincolnshire' => 'North East Lincolnshire',
                                'North Lincolnshire' => 'North Lincolnshire',
                                'North Somerset' => 'North Somerset',
                                'North Tyneside' => 'North Tyneside',
                                'Nottingham' => 'Nottingham',
                                'Oldham' => 'Oldham',
                                'Peterborough' => 'Peterborough',
                                'Plymouth' => 'Plymouth',
                                'Poole' => 'Poole',
                                'Portsmouth' => 'Portsmouth',
                                'Reading' => 'Reading',
                                'Redbridge' => 'Redbridge',
                                'Redcar and Cleveland' => 'Redcar and Cleveland',
                                'Richmond upon Thames' => 'Richmond upon Thames',
                                'Rochdale' => 'Rochdale',
                                'Rotherham' => 'Rotherham',
                                'Salford' =>'Salford',
                                'Sandwell' => 'Sandwell',
                                'Sefton' => 'Sefton',
                                'Sheffield' => 'Sheffield',
                                'Slough' => 'Slough',
                                'Solihull' => 'Solihull',
                                'South Gloucestershire' => 'South Gloucestershire',
                                'South Tyneide' => 'South Tyneide',
                                'Southampton' => 'Southampton',
                                'Southend-On-Sea' => 'Southend-On-Sea',
                                'Southwark' => 'Southwark',
                                'St. Helens' => 'St. Helens',
                                'Stockport' => 'Stockport',
                                'Stockton-On-Tees' => 'Stockton-On-Tees',
                                'Sunderland' => 'Sunderland',
                                'Swindon' => 'Swindon',
                                'Tameside' => 'Tameside',
                                'Telford and Wrekin' => 'Telford and Wrekin',
                                'Thurrock' => 'Thurrock',
                                'Torbay' =>'Torbay',
                                'Tower Hamlets' => 'Tower Hamlets',
                                'Trafford' => 'Trafford',
                                'Wakefield' => 'Wakefield',
                                'Walsall' => 'Walsall',
                                'Waltham Forest' => 'Waltham Forest',
                                'Wandsworth' => 'Wandsworth',
                                'Warrington' => 'Warrington',
                                'West Berkshire' => 'West Berkshire',
                                'Westrminister' => 'Westrminister',
                                'Wigan' => 'Wigan',
                                'Windsor and Maidenhead' => 'Windsor and Maidenhead',
                                'Wirral' => 'Wirral',
                                'Wokingham' => 'Wokingham',
                                'Wolverhampton' => 'Wolverhampton',
                                'York' => 'York',

                                /* English Traditional Counties */
                                'Berwickshire' => 'Berwickshire',
                                'Cumberland' => 'Cumberland',
                                'Herefordshire' => 'Herefordshire',
                                'Huntingdonshire' => 'Huntingdonshire',
                                'Sussex' => 'Sussex',
                                'Westmorland' =>  'Westmorland',
                                'Worcestershire' => 'Worcestershire',
                                'Yorkshire' => 'Yorkshire',

                                 /* English Additional Postal Counties */
                                'Cleveland' => 'Cleveland',
                                'County Durham' => 'County Durham',

                                /* Scotish Additional Administrative Regions */
                                'Aberdeen City' => 'Aberdeen City',
                                'Comhairle Nan Eilean Siar' =>  'Comhairle Nan Eilean Siar',
                                'Dundee City' => 'Dundee City',
                                'East Ayrshire' => 'East Ayrshire',
                                'East Dunbartonshire' => 'East Dunbartonshire',
                                'East Renfrewshire' => 'East Renfrewshire',
                                'Edinburgh, Cito Of' => 'Edinburgh, Cito Of',
                                'Glasgow City' => 'Glasgow City',
                                'Inverclyde' => 'Inverclyde',
                                'North Ayrshire' => 'North Ayrshire',
                                'North Lanarkshire' => 'North Lanarkshire',
                                'Orkney Islands' => 'Orkney Islands',
                                'South Ayrshire' => 'South Ayrshire',
                                'South Lanarkshire' => 'South Lanarkshire',
                                'Stirling' => 'Stirling',
                                'West Dunbartonshire' => 'West Dunbartonshire',

                                /* Scotish Traditional Counties */
                                'Argyllshire' => 'Argyllshire',
                                'Banffshire'=> 'Banffshire',
                                'Buteshire' => 'Buteshire',
                                'Caithness' => 'Buteshire',
                                'Cromartyshire' => 'Cromartyshire',
                                'Dumfriesshire' => 'Dumfriesshire',
                                'Inverness-Shire' => 'Inverness-Shire',
                                'Kincardineshire' => 'Kincardineshire',
                                'Kinross-Shire' => 'Kinross-Shire',
                                'Kirkcudbrightshire' => 'Kirkcudbrightshire',
                                'Morayshire' => 'Morayshire',
                                'Peeblesshire' => 'Peeblesshire',
                                'Perthshire' => 'Perthshire',
                                'Nairnshire' => 'Nairnshire' ,
                                'Ross-Shire' => 'Ross-Shire',
                                'Roxburghshire' => 'Roxburghshire',
                                'Selkirkshire' => 'Selkirkshire',
                                'Shetland' => 'Shetland',
                                'Sutherland' => 'Sutherland',
                                'Wigtownshire' => 'Wigtownshire',

                                /* Scotish Additional Postal Counties */
                                'Argyll' => 'Argyll',
                                'Isle of Harriss' => 'Isle of Harriss',
                                'Isle of Islay' => 'Isle of Islay',
                                'Isle of Lewis' => 'Isle of Lewis',
                                'Isle of Skye' => 'Isle of Skye',

                                /* Welsh Additional Administrative Regions */
                                'Bridgend' => 'Bridgend',
                                'Caerphilly' => 'Caerphilly',
                                'Cardiff' => 'Cardiff',
                                'Herefordshire, County of' => 'Herefordshire, County of',
                                'Isle of Anglesey' => 'Isle of Anglesey',
                                'Swansea' => 'Swansea',
                                'Torfaen' => 'Torfaen',
                                'Vale Of Glamorgan' => 'Vale Of Glamorgan',

                                /* Welsh Traditional Counties */
                                'Anglesey/Sir Fon' => 'Anglesey/Sir Fon',
                                'Brecknockshire' => 'Brecknockshire',
                                'Caernarfonshire'=> 'Caernarfonshire',
                                'Cardiganshire' => 'Cardiganshire',
                                'Merionethshire' => 'Merionethshire',
                                'Montgomeryshire' => 'Montgomeryshire',
                                'Radnorshire' => 'Radnorshire',

                                /* Welsh Additional Postal Counties */
                                'Clwyd' => 'Clwyd',
                                'Dyfed' => 'Dyfed',
                                'Gwent' => 'Gwent',
                                'Mid Glamorgam' => 'Mid Glamorgam',
                                'South Glamorgan' => 'South Glamorgan',
                                'West Glamorgan' => 'West Glamorgan',

                                /* Northern Ireland Ad`ditional Administrative Regions */
                                'Northern Ireland' => 'Northern Ireland',
                            )
                        );
                        return $address_types;
                    }

                    // Add UK Addresses
                    add_filter("gform_address_types", "add_uk_counties", 10, 2);

                    // Change to Town/City

                    function set_gravityform_city($label, $form_id) {
                        return "Town/City";
                    }

                    add_filter("gform_address_city", "set_gravityform_city", 10, 2);

                    // Change "Street Address"
                    function change_address_street($label, $form_id) {
                        return "Name/Number and Street";
                    }

                    add_filter("gform_address_street", "change_address_street", 10, 2);

                    // ---------------------------------------------------------------------------------
                    // Add Additional Postcodes 4u Settings - Modified for GravityForms 2.5
                    add_action('gform_field_appearance_settings', 'pc4u_settings', 10, 2);

                    function pc4u_settings($placement, $form_id) {
                        if ($placement == 50) {
                            //Insert new appearance field HTML here
                            ?>
                            <div class="pc4uPostcode_setting_container">
                                <h3 for="field_single_pc4uUsePostcodeLookup" class ="section_label"><strong>Postcodes4u Settings</strong></h3>
                                <div id="field_input_pc4uUsePostcodeLookup_container">
                                    <li class="pc4uPostcode_setting field_setting">
                                        <input type="checkbox" id="field_pc4uUsePostcodeLookup_value"  onclick="SetFieldProperty('pc4uUsePostcodeLookup', this.checked)" />
					<label for="field_pc4uUsePostcodeLookup_value" class ="inline">Use Postcode Lookup In Address</label>
					<?php gform_tooltip("form_field_pc4uUsePostcodeLookup_value") ?>
                                    </li>
                                </div>
                                <div id="field_input_pc4uPostcodeAtTop_container">
                                    <li class="pc4uPostcode_setting field_setting">
					<input type="checkbox" id="field_pc4uPostcodeAtTop_value"  onclick="SetFieldProperty('pc4uPostcodeAtTop', this.checked)" />
					<label for="field_pc4uPostcodeAtTop_value" class ="inline">Show Postcode field at top</label>
					<?php gform_tooltip("form_field_pc4uPostcodeAtTop_value") ?>
                                    </li>
                                </div>
                                <div id="field_input_pc4uPostcodeAtTopShow_container">
                                    <li class="pc4uPostcode_setting field_setting">
					<input type="checkbox" id="field_pc4uPostcodeAtTopShow_value"  onclick="SetFieldProperty('pc4uPostcodeAtTopShow', this.checked)" />
					<label for="field_pc4uPostcodeAtTopShow_value" class ="inline">Hide Address Fields until Address Selected</label>
					<?php gform_tooltip("form_field_pc4uPostcodeAtTopShow_value") ?>
                                    </li>
                                </div>
                                 <div id="field_input_pc4uPostcodeAtTopShowManual_container">
                                    <li class="pc4uPostcode_setting field_setting">
					<input type="checkbox" id="field_pc4uPostcodeAtTopShowManual_value"  onclick="SetFieldProperty('pc4uPostcodeAtTopShowManual', this.checked)" />
					<label for="field_pc4uPostcodeAtTopShowManual_value" class ="inline">If Hide Address Fields Set-Show Manual Entry Button</label>
					<?php gform_tooltip("form_field_pc4uPostcodeAtTopShowManual_value") ?>
                                    </li>
                                </div>
				<div id="field_input_pc4uPostcodeValidateCounty_container">
                                    <li class="pc4uPostcode_setting field_setting">
					<input type="checkbox" id="field_pc4uPostcodeValidateCounty_value"  onclick="SetFieldProperty('pc4uPostcodeValidateCounty', this.checked)" />
					<label for="field_pc4uPostcodeValidateCounty_value" class ="inline">Include County in Address Validation</label>
					<?php gform_tooltip("form_field_pc4uPostcodeValidateCounty_value") ?>
                                    </li>
                                </div>
				<li class="pc4uPostcode_setting field_setting">
                                    <p>&nbsp;</p>
				</li>
                            </div>
                            <?php
                        }
                    }

                    // Action to inject supporting script to the form editor page
                    //  - Modified for GravityForms 2.5
                    add_action('gform_editor_js', 'pc4u_editor_script');

                    function pc4u_editor_script() {
                        ?>
                        <script type='text/javascript'>

                            //adding setting to fields of type "pc4uaddress"
                            fieldSettings.pc4uaddress += ', .pc4uPostcode_setting';

                            //binding to the load field settings event to initialize the checkbox
							jQuery(document).on('gform_load_field_settings', function(event, field, form){
								jQuery('#field_pc4uUsePostcodeLookup_value').prop('checked', Boolean( rgar( field, 'pc4uUsePostcodeLookup' ) ) );
								jQuery('#field_pc4uPostcodeAtTop_value').prop('checked', Boolean( rgar( field, 'pc4uPostcodeAtTop' ) ) );
								jQuery('#field_pc4uPostcodeAtTopShow_value').prop('checked', Boolean( rgar( field, 'pc4uPostcodeAtTopShow' ) ) );
                                                                jQuery('#field_pc4uPostcodeAtTopShowManual_value').prop('checked', Boolean( rgar( field, 'pc4uPostcodeAtTopShowManual' ) ) );
								jQuery('#field_pc4uPostcodeValidateCounty_value').prop('checked', Boolean( rgar( field, 'pc4uPostcodeValidateCounty' ) ) );
							});
                        </script>
                        <?php
                    }

                    //Filter to add Postcodes 4u new tooltips
                    add_filter('gform_tooltips', 'add_usePostcodeLookup_tooltips');

                    function add_usePostcodeLookup_tooltips($tooltips) {
                        $tooltips['form_field_pc4uUsePostcodeLookup_value'] = "<h6>Use Postcode Lookup</h6>Uncheck this box if Postcode Lookup is NOT required for this address field.";

                        return $tooltips;
                    }

                    add_filter('gform_tooltips', 'add_postcodeAtTop_tooltips');

                    function add_postcodeAtTop_tooltips($tooltips) {
                        $tooltips['form_field_pc4uPostcodeAtTop_value'] = "<h6>Postcode at Top</h6>Check this box to make the postcode field the first field displayed";

                        return $tooltips;
                    }


                    add_filter('gform_tooltips', 'add_postcodeAtTopShow_tooltips');

                    function add_postcodeAtTopShow_tooltips($tooltips) {
                        $tooltips['form_field_pc4uPostcodeAtTopShow_value'] = "<h6>Postcode at Top - Show Hidden Address Fields when Address Selected</h6>When this AND <strong>'Show Postcode at Top'</strong> is selected the other address fields are hidden until an address is selected.";

                        return $tooltips;
                    }
                    
                    // New Manual Form Entry Button Option
                    add_filter('gform_tooltips', 'add_postcodeAtTopShowManual_tooltips');

                    function add_postcodeAtTopShowManual_tooltips($tooltips) {
                        $tooltips['form_field_pc4uPostcodeAtTopShowManual_value'] = "<h6>Postcode at Top - For Hidden Address - Show Manual Entry Button</h6>When this AND <strong>'Show Postcode at Top + Show Hidden Address Fields when Address Selected'</strong> are selected this option displays a 'Manual Address' button to allow an address to be entered without a UK Postcode.</h6>.";
                        return $tooltips;
                    }

                    add_filter('gform_tooltips', 'add_postcodeValidateCounty_tooltips');

                    function add_postcodeValidateCounty_tooltips($tooltips) {

                        $tooltips['field_pc4uPostcodeValidateCounty_value'] = "<h6>Include County an Address Validation - If Address is 'Required' and County is displayed then ensure an Address County is set.</h6>When this AND '<strong>Show Postcode at Top</strong>' is selected the other address fields are hidden until an address is selected.";
                        return $tooltips;
                    }


                }

                //Ensure Gravity Forms 'GF_Fields' class has been updated to support Postcodes4u
                $gravityFileUpdateReq = false;
                if(class_exists("GF_Fields_Pc4u")  && GF_Fields_Pc4u::pc4uIsGfFieldsStatePrivate()) {
                    $gravityFileUpdateReq = true;
                }

                if ($gravityFileUpdateReq) {
                    // CHECK IF AUTO UPDATE IS ENABLED
                    if ($pc4u_options['gravityintegrate_noauto'] == false || $pc4u_options['gravityintegrate'] == '0') {

                        // ATTEMPT TO UPDATE GRAVITY FORMS GF-FIELDS file.
                        // Copy Updated GF_FIELDS Definition
                        $sGfDirPath = WP_PLUGIN_DIR . "/gravityforms/includes/fields/";
                        $sPc4uDirPath = plugin_dir_path(__FILE__) . "gravity/";

                        $gfFieldsPath = $sGfDirPath . "class-gf-fields.php";
                        $gfFieldsBackupPath = $sGfDirPath . "class-gf-fields.php.Pc4uBackup";

                        $gfPc4uNewFieldsPath = $sPc4uDirPath . "pc4u_class-gf-fields.php";


                        // BACKUP ORIGINAL GF_FIELDS FILE ========================
                        // Check If Backup File Already Exists
                        if (file_exists($gfFieldsBackupPath)) {
                            $gfFieldsBackupPath = $gfFieldsBackupPath . "_2";
                        }
                        copy($gfFieldsPath, $gfFieldsBackupPath);

                        // Copy Over Updated GF_FIELDS FILE  =====================
                        copy($gfPc4uNewFieldsPath, $gfFieldsPath);

                        // Refresh Page
                        header("Refresh:0");

                        // Attempt To Register Pc4u Address
                        if (class_exists('GF_Fields_Pc4u')) {
                            try {
                                GF_Fields_Pc4u::pc4uRegister(new GF_Field_Pc4uAddress());
                            } catch (Exception $mEx) {
                                echo 'CANNOT INTEGRATE POSTCODES4u WITH GRAVITY FORMS (Auto Register Failed)';
                            }
                        }
                    } else {
                        // Auto Update Not Enabled - Integration Will Fail
                        echo 'CANNOT INTEGRATE POSTCODES4u WITH GRAVITY FORMS (Manual Update Required)';
                    }
                } else {
                    // GravityForms is already Set Up - Attempt To Register Pc4u Address
                    if (class_exists('GF_Fields_Pc4u')) {
                        try {
                            GF_Fields_Pc4u::pc4uRegister(new GF_Field_Pc4uAddress());
                        } catch (Exception $mEx) {
                            echo 'CANNOT INTEGRATE POSTCODES4u WITH GRAVITY FORMS (GF Fields Checked)';
                        }
                    }
                }
            }
        }
    }
}
