// POSTCODES4U JavaScript
//   Updated 06/02/2019
//   GF Issue Fixed 18/02/19
//   CF7 Single Line Address Issue Fixed
//   New 'No County' option
//   Modified to work with Material Design Framework &
//     yith-woocommerce-checkout-manager
//   Version 9. (1.5.9) 30/03/2020
//
//   Version 10. (1.5.10) - 18/05/2020
//     Modified for work with Select2 JQuery select boxes
//
//   Version 11. (1.5.11) - 06/01/2021
//     Modified for work CF7 Conditional Fields - repeating fields
//
//   Version 12+13 - Internal Test Versions
//
//   Version 14. (1.5.14) - 22/03/2021
//     Tided up Error Handling
//
//   Version 15. (1.5.15) Jquery not available enhancements 25/04/2021
//
//   Version 16. (1.5.16) Added Show Postcode Lookup Warning Messages Option 20/07/2021
//   
//   Version 17. (1.5.17) Updated GF Form Show Hidden Address Option and Custom Button Labels 30/07/2021
//
//   Version 18. (1.5.18) Updated CF for Multiple Addresses (CF7 5.8 Issue) - Add AdHoc AddressLine and Address Block Fields 14/08/2023
//
//   Version 19. (1.5.19) Updated CF for Multiple Addresses (CF7 5.8 Issue) - Add AdHoc AddressLine and Address Block Fields 14/08/2023



// Public Variables
var pc4uCallingFormCode = "";
var pc4uFormPostcodeFieldId = "";
var pc4uFormPostcodeFieldName = "";
var pc4uFieldObjectForPostcode = null;
//  General Field Data for 'AdHoc' Lookup Call
var pc4uFormAddressCompanyFieldId = "";
var pc4uFormAddressLine1FieldId = "";
var pc4uFormAddressLine2FieldId = "";
var pc4uFormAddressLine3FieldId = "";
var pc4uFormAddressTownFieldId = "";
var pc4uFormAddressCountyFieldId = "";
// Address Line and Block KRF 2023.08.14
var pc4uFormAddressLineFieldId = "";
var pc4uFormAddressBlockFieldId = "";

// New Option To Show Postcode Lookup Warnings. - KRF 2021.07.20
var pc4uShowLookupWarnings = '0';

// New Option For custom Postcode dropdown message - KRF 2021.07.29 
var pc4uSelectAddressText = 'Select an address:';


// Address Summary Fields
var pc4uFormAddressSummaryLineId = "";
var pc4uFormAddressSummaryBlockId = "";

// Address Country and Country Type
var pc4uFormAddressNationCountryFieldId = "";
var pc4uFormAddressNationCountryType = "text"; // Defaults to text - other options are 'dropdown' and 'wp_dropdown'
// Address Select Dropdown
var pc4uFormAddressDropdownFieldId = "";
// Lookup Code - Source of Lookup 'CF7, 'WooCommerce' etc
var pc4uLookupFormCode = "";

// Postcodes4u Lookup Called From Basic 'Blog' Form
// ===============================================
function Pc4uSearchBegin() {

    pc4uCallingFormCode = "";
    pc4uFormPostcodeFieldId = "pc4uPostcode";
    pc4uFormAddressDropdownFieldId = "pc4uDropdown";
    pc4uFormPostcodeFieldName = "";
    pc4uFieldObjectForPostcode = null;
    var oPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);

    if(oPostcodeField) {
        oPostcodeField.value.toUpperCase();

        pc4uPostcodeSearchBegin(oPostcodeField.value);
    } else {
         // Cannot Find Postcode Field
        alert("Postcodes4u Form - Postcode Search Call Invalid - No Postcode Field Specified.");

        // Ensure No Address Lookups are displayed
        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;
    }
}

// Postcodes4u Lookup Called From Gravity Form
// ===============================================
function Pc4uAdHocSearchBegin(adhoc_postcode_fieldId, adhoc_dropdown_fieldId) {

    pc4uCallingFormCode = "pc4uAdHocPostcode";
    pc4uFormPostcodeFieldId = adhoc_postcode_fieldId;
    pc4uFormPostcodeFieldName = "";
    pc4uFieldObjectForPostcode = null;
    pc4uFormAddressDropdownFieldId = adhoc_dropdown_fieldId;
    //
    var oPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);
    var oDropdownField = document.getElementById(pc4uFormAddressDropdownFieldId);

    if(oPostcodeField && oDropdownField) {
        pc4uPostcodeSearchBegin(oPostcodeField.value);
     } else {
        if(!oPostcodeField) {
            // Cannot Find Postcode Field
            alert("Postcodes4u Form Error - Postcode Search Call Invalid - No Postcode Field Specified.");
        } else {
            alert("Postcodes4u Form Error - Postcode Search Call Invalid - No Postcode Address 'Dropdown'Specified.");
        }
        // Ensure No Address Lookups are displayed
        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7 & GF) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
        pc4uFieldObjectForPostcode = null;
    }
}

// Postcodes4u Lookup Called From Gravity Form
// ===============================================
function Pc4uGfSearchBegin(grav_fieldId) {

pc4uCallingFormCode = "";
    pc4uFormPostcodeFieldId = "pc4uPostcode";
    pc4uFormAddressDropdownFieldId = pc4uFormPostcodeFieldId +  '_' + "pc4uGfDropdown";

    pc4uCallingFormCode = "pc4uGravPostcode";
    pc4uFormPostcodeFieldId = grav_fieldId;
    pc4uFormPostcodeFieldName = "";
    pc4uFieldObjectForPostcode = null;
    var oPostcodeField = document.getElementById(grav_fieldId + '_5');

    if(oPostcodeField) {
        oPostcodeField.value.toUpperCase();

        pc4uPostcodeSearchBegin(oPostcodeField.value);
    } else {
         // Cannot Find Postcode Field
        alert("Postcodes4u Gravity Form Error- Postcode Search Call Invalid - No Postcode Field Specified.");

        // Ensure No Address Lookups are displayed
        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7 & GF) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
        pc4uFieldObjectForPostcode = null;
    }
}

// Postcodes4u Lookup Called From Gra Checkout
// - Billing Address
// ===============================================
function Pc4uWooSearchBillingBegin() {
    // Ensure Billing county is set to UK
   var dCountry = document.getElementById("billing_country");
    if (dCountry == null || dCountry.value == "GB" || dCountry.value == null || dCountry.value == "") {
        // Process WooCommerce Billing Postcode
        pc4uCallingFormCode = "pc4uWooBilling";
        // Clear Advanced Form (CF7) Values
        pc4uFormPostcodeFieldId = "billing_postcode";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;
        pc4uFormAddressDropdownFieldId ="pc4uWooBillingDropdown";

        var oPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);
        if(oPostcodeField) {
            oPostcodeField.value.toUpperCase();
            pc4uPostcodeSearchBegin(oPostcodeField.value);
        } else {
             // Cannot Find Postcode Field
            alert("WooCommerce Billing Form Error - Postcode Search Call Invalid - No Postcode Field Specified.");

            // Ensure No Address Lookups are displayed
            pc4uCallingFormCode = "";

            // Clear Advanced Form (CF7 & GF) Values
            pc4uFormPostcodeFieldId = "";
            pc4uFormPostcodeFieldName ="";
            pc4uFieldObjectForPostcode = null;
        }
    } else {
        // Invalid Country
        alert("WooCommerce Billing Postcode Search is only valid when the country is set as the UK.");

        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7 & GF) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;

        // Ensure No Address Lookups are displayed
        var lookupDropdown = document.getElementById("pc4uWooBillingDropdown");

        // Use New 'Hide' Function KRF 2020.04.30
        // -- Do Not Clear Dropdown
        hidePc4uAddressDropdown(lookupDropdown, false);
    }
}

// Postcodes4u Lookup Called From WooCommerce Checkout
// - Shipping Address
// ===============================================
function Pc4uWooSearchShippingBegin() {
    // Ensure Shipping county is set to UK
    var dCountry = document.getElementById("shipping_country");
    if (dCountry == null || dCountry.value == "GB" || dCountry.value == null || dCountry.value == "") {
        // Process WooCommerce Shipping Postcode
        pc4uCallingFormCode = "pc4uWooShipping";

        // Clear Advanced Form (CF7 & GF) Values
        pc4uFormPostcodeFieldId = "shipping_postcode";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;
        pc4uFormAddressDropdownFieldId = "pc4uWooShippingDropdown";

        var oPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);
        if(oPostcodeField) {
            oPostcodeField.value.toUpperCase();
            pc4uPostcodeSearchBegin(oPostcodeField.value);
        } else {
             // Cannot Find Postcode Field
            alert("WooCommerce Shipping Form - Postcode Search Call Invalid - No Postcode Field Specified.");

            // Ensure No Address Lookups are displayed
            pc4uCallingFormCode = "";

            // Clear Advanced Form (CF7 & GF) Values
            pc4uFormPostcodeFieldId = "";
            pc4uFormPostcodeFieldName ="";
            pc4uFieldObjectForPostcode = null;
        }

        var oPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);
        oPostcodeField.value.toUpperCase();

        pc4uPostcodeSearchBegin(oPostcodeField.value);

    } else {
        // Invalid Country
        alert("WooCommerce Shipping Postcode Search is only valid when the country is set as the UK.");
        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7 && GF) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;

        // Ensure No Address Lookups are displayed
        var lookupDropdown = document.getElementById("pc4uWooShippingDropdown");
        if (lookupDropdown !== null) {
            // Use New 'Hide' Function KRF 2020.04.30
            // -- Do Not Clear Dropdown
            hidePc4uAddressDropdown(lookupDropdown, false);
        }
    }
}

// Postcodes4u Lookup Called From Contact Form 7
// Added support for Multi Forms
// ===============================================
function Pc4uCF7SearchBegin(cf7PostCodeFieldDiv, cf7PostCodeFieldSet, cf7PostCodeForm, cf7PostCodeTagName, cf7TagField) {
    pc4uFieldObjectForPostcode = null;
    var postcodeSearchValue = "" ;

    // Process Contact Form 7 Postcode
    pc4uCallingFormCode = "pc4uCF7Postcode";
    pc4uFormPostcodeFieldId = "";
    pc4uFormPostcodeFieldName = "";
    pc4uFormPostcodeDropdownFieldName = "";

    // Ensure Contact Form 7 Field Set
    // Decode Postcode Field Id
    if(cf7TagField) {
        if(cf7TagField.id) {
            pc4uFormPostcodeFieldId = cf7TagField.id;
            pc4uFormPostcodeFieldName = cf7PostCodeTagName;
            pc4uFieldObjectForPostcode = null;
            cf7TagField.value.toUpperCase();
            postcodeSearchValue = cf7TagField.value;
        } else {
            // Check For Single Entry Array
            if(cf7TagField.length == 1) {
                pc4uFormPostcodeFieldId = cf7TagField["0"].id;
                pc4uFormPostcodeFieldName = cf7TagField["0"].name;
                pc4uFieldObjectForPostcode = null;
                cf7TagField["0"].value.toUpperCase();
                postcodeSearchValue = cf7TagField["0"].value;
            }
        }
    }

    // Decode Postcode DropdownID
    pc4uFormAddressDropdownFieldId = "pc4uCF7Dropdown";
    if(cf7PostCodeTagName) {
         pc4uFormAddressDropdownFieldId = pc4uFormAddressDropdownFieldId + "_" + cf7PostCodeTagName;
    }
//    // Ensure Dropdown field exists
//    if(document.getElementById(pc4uFormAddressDropdownFieldId)){
//        // Do Nothing
//    } else {
//
//        if(newDropdown = document.querySelector("[data-orig_id=" + pc4uFormAddressDropdownFieldId +" ]")) {
//            pc4uFormAddressDropdownFieldId = newDropdown.id;
//        }
//    }

    // Check For Div Group
    if(cf7PostCodeFieldDiv || cf7PostCodeFieldSet) {
        if(cf7PostCodeFieldDiv) {
            pc4uFieldObjectForPostcode = cf7PostCodeFieldDiv;
        } else {
            // Process Form/Fieldsets
            pc4uFieldObjectForPostcode = cf7PostCodeFieldSet;
        }

        var oChildField = pc4uFieldObjectForPostcode.querySelectorAll("[id*='pc4upostcode']");  // Search for fields containing
        if(oChildField && oChildField.length > 0) {
            pc4uFormPostcodeFieldId  = oChildField.id;
            //pc4uFormPostcodeFieldId  = "pc4upostcode"
            pc4uFormPostcodeFieldName = "pc4upostcode";
            oChildField[0].value.toUpperCase();
            postcodeSearchValue = oChildField[0].value
        }

        // Get Dropdown
        oChildField = pc4uFieldObjectForPostcode.querySelectorAll("[id*='pc4uCF7Dropdown']");  // Search for fields containing
        if(oChildField && oChildField.length > 0) {
            pc4uFormAddressDropdownFieldId  = oChildField[0].id;
        }
    }

    if(pc4uFormPostcodeFieldName == "") {
        if(cf7PostCodeForm) {
            oFieldElements = cf7PostCodeForm.elements;
            if(oFieldElements && oFieldElements.length > 0) {
                for (var i = 0; i < oFieldElements.length; i++) {
                    if(oFieldElements[i].id.indexOf("pc4upostcode") !== -1) {
                        pc4uFormPostcodeFieldId  = oFieldElements[i].id;
                        pc4uFormPostcodeFieldName = "pc4upostcode";
                        pc4uFieldObjectForPostcode = cf7PostCodeForm;
                        //
                        oFieldElements[i].value.toUpperCase();
                        postcodeSearchValue = oFieldElements[i].value
                        break ;
                    }
                }
            } else {
                // IE Compatability  - Use QuerySelect
                //
                var oChildForm = cf7PostCodeForm.querySelectorAll("[id*='pc4upostcode']"); // Search for 'Includes'
                if(oChildForm && oChildForm.length > 0) {
                    pc4uFormPostcodeFieldId  = oChildForm[0].id;
                    // pc4uFormPostcodeFieldId  = "pc4upostcode"
                    pc4uFormPostcodeFieldName = "pc4upostcode";
                    pc4uFieldObjectForPostcode = cf7PostCodeForm;

                    oChildForm[0].value.toUpperCase();
                    postcodeSearchValue = oChildForm[0].value
                }
            }
        }
    }

    if(pc4uFormPostcodeFieldId || pc4uFormPostcodeFieldName) {

        pc4uPostcodeSearchBegin(postcodeSearchValue);
    } else {
        // Invalid Field Tag
        alert("Contact Form 7 Postcode Search Call Invalid - No Postcode Field Specified.");

        // Ensure No Address Lookups are displayed
        var lookupDropdown = null;
        if(pc4uCf7PostcodeForm.elements && pc4uCf7PostcodeForm.elements.length > 0) {
            lookupDropdown = pc4uCf7PostcodeForm.elements[pc4uFormAddressDropdownFieldId];
        } else {
             // IE Compatability  - Use QuerySelect
             var oChildDrop = cf7PostCodeFieldSet.querySelectorAll("[id*='" + pc4uCF7Dropdown + "']"); // Search For 'Includes'
             if(oChildDrop && oChildDrop.length > 0) {
                 lookupDropdown = oChildDrop[0];
             }
        }

        if(lookupDropdown) {
            // Use New 'Hide' Function KRF 2020.04.30
            // -- Do Not Clear Dropdown
            hidePc4uAddressDropdown(lookupDropdown, false);
        }
        pc4uCallingFormCode = "";

        // Clear Advanced Form (CF7 & GF) Values
        pc4uFormPostcodeFieldId = "";
        pc4uFormPostcodeFieldName ="";
	pc4uFieldObjectForPostcode = null;
    }
}

// ===============================================
// Postcodes4u Lookup Common 'Start Search Process'
//  Generate Postcodes4u Search Request
//  Updated 2020.01.30 To Suppress Lookups
//     when no address selected
// ===============================================
function pc4uPostcodeSearchBegin( searchPostcode) {
    var scriptTag = document.getElementById("postcodes4youelement");
    var headTag = document.getElementsByTagName("head").item(0);
    var strUrl = "";

    var pc4uKey = "";
    var pc4uUser = "";
    var pc4uShowWarnings = '0';
    var pc4uSelectAddressDropdownText = "Select an address:";
    var searchError = false ;


    // Get 'Show Warnings' Option
    var oField = document.getElementById("pc4ushow_warnings");
    if(oField) {
        pc4uShowWarnings = (oField.textContent).trim();
    } else {
        oField = document.querySelector("[id^='pc4ushow_warnings']");
        //oField = document.querySelector("[data-orig_id='pc4ushow_warnings']");
        if(oField) {
            pc4uShowWarnings = (oField.textContent).trim();
        }
    }
    pc4uShowLookupWarnings = pc4uShowWarnings;

    // Get 'Select Address Dropdown' Text
    var oField = document.getElementById("pc4uselect_text");
    if(oField) {
        pc4uSelectAddressDropdownText = (oField.textContent).trim();
    } else {
        oField = document.querySelector("[id^='pc4uselect_text']");
        //oField = document.querySelector("[data-orig_id='pc4ushow_warnings']");
        if(oField) {
            pc4uSelectAddressDropdownText = (oField.textContent).trim();
        }
        if(pc4uSelectAddressDropdownText == '') {
            pc4uSelectAddressDropdownText = "Select an address:";
        }
    }
    
    pc4uSelectAddressText = pc4uSelectAddressDropdownText;


    // Ensure Postcode is specified
    if (searchPostcode !== null && searchPostcode != "" && searchPostcode.trim() != "") {
        // Get Key and User
        var oField = document.getElementById("postcodes4ukey");
        if(oField) {
            pc4uKey = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4ukey']");
            //oField = document.querySelector("[data-orig_id='postcodes4ukey']");
            if(oField) {
                pc4uKey = (oField.textContent).trim();
            }
        }

        var oField = document.getElementById("postcodes4uuser");
        if(oField) {
            pc4uUser = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4uuser']");
            //oField = document.querySelector("[data-orig_id='postcodes4uuser']");
            if(oField) {
                pc4uUser = (oField.textContent).trim();
            }
        }


        // Ensure Pc4u Username and Key set up
        if (pc4uKey !== null && pc4uKey != "") {
            if (pc4uUser !== null && pc4uUser != "") {
                //Build the url
                //  Check if HTTPS
                if (window.location.protocol == "'https:" || window.location.protocol == "https:") {
                    strUrl = "https://";
                } else {
                    strUrl = "http://";
                }
                strUrl += "services.3xsoftware.co.uk/Search/ByPostcode/json?";
                strUrl += "username=" + encodeURI(pc4uUser);
                strUrl += "&key=" + encodeURI(pc4uKey);
                strUrl += "&postcode=" + encodeURI(searchPostcode);
                strUrl += "&callback=Pc4uSearchEnd";

                //Make the request
                if (scriptTag) {
                    try {
                        headTag.removeChild(scriptTag);
                    }
                    catch (e) {
                        if(pc4uShowWarnings ==='1') {
                            alert("pc4uPostcodeSearchBegin Error:" + e.message);
                        }
                    }
                }
                scriptTag = document.createElement("script");
                scriptTag.src = strUrl;
                scriptTag.type = "text/javascript";
                scriptTag.id = "postcodes4youelement";
                headTag.appendChild(scriptTag);

                searchError = false ;

            } else {
                // No Pc4u Username
                alert("Postcodes4u Configuration Error: No Username")
                searchError = true;
            }
        } else {
            // No Pc4u Key
            alert("Postcodes4u Configuration Error: No User Key")
            searchError = true;
        }
    } else {
        // NO POSTCODE SPECIFIED
        if(pc4uShowWarnings === '1') {
            alert("Postcode Lookup Error -  NO POSTCODE SPECIFIED");
        }
        searchError = true;
    }

    // Display Hidden Gravity Form If Error
    if(searchError) {        
        if (pc4uCallingFormCode == "pc4uGravPostcode") {
            // If Gravity Form Ensure NOT Hidden
            showPc4uGFHiddenAddress(pc4uFormPostcodeFieldId)
        } 
    }
}

// =================================================================
// Postcodes4u Lookup 'Return 'Callback' Function
//  Show matching Postcode Addresses Postcodes4u Search Request
//  Modified for 1.4.2 For Multiple CF7 Forms
//  Modified for 1.4.3 For Gravity Form
// =================================================================
function Pc4uSearchEnd(response) {
    var pc4uDropdownFieldID = "pc4uDropdown";
    var pc4uDropdownFieldName = "";
    var oForm = null;
    var searchError = false ;

    var lookupDropdown;
    // Decode Calling Form Code - Dropdown List  Field
    if (pc4uCallingFormCode == "pc4uWooBilling") {
        pc4uDropdownFieldID = "pc4uWooBillingDropdown"

    } else {
        if (pc4uCallingFormCode == "pc4uWooShipping") {
            pc4uDropdownFieldID = "pc4uWooShippingDropdown"
        } else {
            if(pc4uCallingFormCode == "pc4uCF7Postcode") {
                pc4uDropdownFieldID = pc4uFormAddressDropdownFieldId
                pc4uDropdownFieldName = "pc4uCF7Dropdown";
                oForm = pc4uFieldObjectForPostcode;
            } else {
                if (pc4uCallingFormCode == "pc4uGravPostcode") {
                    pc4uDropdownFieldID = pc4uFormPostcodeFieldId +  '_' + "pc4uGfDropdown";
                } else {
                    if (pc4uCallingFormCode == "pc4uAdHocPostcode") {
                        pc4uDropdownFieldID = pc4uFormAddressDropdownFieldId;

                    } else {
                        if (pc4uCallingFormCode == "" ||pc4uCallingFormCode == "pc4u") {
                            pc4uDropdownFieldID = "pc4uDropdown";
                        }
                    }
		}
            }
        }
    }

    // Get Dropdown - Use Form Object If Provides (Initially for CF7)
    if(oForm ) {
        // Check Elements Object Exists (Modern Browser)
        if(oForm.elements && oForm.elements.length > 0) {
            lookupDropdown = oForm.elements[pc4uFormAddressDropdownFieldId];
        } else {
            // Legacy Browser (IE)
            var oLegacyObj = oForm.querySelectorAll("[id*='"+pc4uDropdownFieldID+"']");
            if(oLegacyObj && oLegacyObj.length > 0) {
                lookupDropdown = oLegacyObj[0];
            }
        }
    } else {
        lookupDropdown = document.getElementById(pc4uDropdownFieldID);
    }

    //Test for an error
    if (response != null && response['Error'] != null) {
        // Clear DropDown List
        if(lookupDropdown) {

            // Use New 'Hide' Function KRF 2020.04.30
            // -- Clear Dropdown
            hidePc4uAddressDropdown(lookupDropdown, true);
        }
        //Show the error message
        alert("Postcode Search Call  Error: "+response['Error'].Cause);
        searchError = true ;
    }
    else {
      var addresslist = response["Summaries"];

        //Check if there were any items found
        if (addresslist.length == 0) {
            // Clear DropDown List - ID HardCoded in Plugin
            if(lookupDropdown) {

                // Use New 'Hide' Function KRF 2020.04.30
                // -- Clear Dropdown
                hidePc4uAddressDropdown(lookupDropdown, true);
            }
            if(pc4uShowLookupWarnings === '1') {
                alert("Sorry, no matching items found for postcode");
            }
            searchError = true ;
        } else {
            try{
                lookupDropdown.innerHTML = "";
                lookupDropdown.options.add(new Option(pc4uSelectAddressText, "", true, true));
                for (var j = 0; j < addresslist.length; j++) {
                    lookupDropdown.options.add(new Option(addresslist[j].StreetAddress + ", " + addresslist[j].Place, addresslist[j].Id));
                }

                // Use New 'Show' Pc4u Address Dropdown Function KRF 2020.04.30
                showPc4uAddressDropdown(lookupDropdown);
            }
            catch (e) {
                //Ignore
                //if(pc4uShowLookupWarnings === '1') {
                //    alert("SearchEnd Dropdown Error:"+ e.message);
                //}
            }
        }
    }

    if(searchError) {
        var showAddressWhenComplete;
        if (pc4uCallingFormCode == "pc4uGravPostcode") {
            // Gravity Forms
            showAddressWhenComplete = document.getElementById(pc4uFormPostcodeFieldId+"_pc4uGfPostcodeLookupAddress");
        

            // If Gravity Form Ensure NOT Hidden
            if(showAddressWhenComplete) {
                showAddressWhenComplete.style.display = ''; 
            }
        }
    }
    // Clear Calling Form Details
    pc4uCallingFormCode = "";
}

// ===========================================================
//// Postcodes4u Lookup Contact Form 7 'Select Address Process'
////  Return Selected Address Details
//// ===========================================================
function Pc4uSearchIdGfBegin() {
    return Pc4uSearchIdBegin("pc4uGravPostcode");
}

// ===========================================================
// Postcodes4u Lookup Contact Form 7 'Select Address Process'
//  Return Selected Address Details
//  - Modified for 1.4.2 to work with Multiple Forms
// ===========================================================
function Pc4uSearchIdCf7Begin(cf7PostCodeFieldDiv, cf7PostCodeFieldSet, cf7PostCodeForm) {
    return Pc4uFormSearchIdBegin(cf7PostCodeFieldDiv, cf7PostCodeFieldSet, cf7PostCodeForm,"pc4uCF7Postcode");
}

// ==============================================================
//   Postcodes4u Lookup Contact Form 7 'Select Address Process'
//    Return Selected Address Details
//  All Fields are Optional - Esp Country COuntry Type, 
//   AddressLIne & AddressBlock
//   Updated KRF 23.08.14 Include Optional AddressLine and Block
//   Fields.
// ==============================================================
function Pc4uSearchIdAdHocBegin(addCompanyId, addAddress1Id, addAddress2Id, addAddress3Id, addAddressTownId, addAddressCountyId, addressNationCountryId, addressNationCountryType, addAddressLineId, addAddressBlockId) {
    pc4uFormAddressCompanyFieldId = addCompanyId;
    pc4uFormAddressLine1FieldId = addAddress1Id;
    pc4uFormAddressLine2FieldId = addAddress2Id;
    pc4uFormAddressLine3FieldId = addAddress3Id;
    pc4uFormAddressTownFieldId = addAddressTownId;
    pc4uFormAddressCountyFieldId = addAddressCountyId;
    // Optional Country Display Options
    pc4uFormAddressNationCountryFieldId = addressNationCountryId;    
    pc4uFormAddressNationCountryType = "";
    
    // Optional Address Line and Block Display Options
    pc4uFormAddressLineFieldId = "";
    pc4uFormAddressBlockFieldId = "";
    // Country
    if(pc4uFormAddressNationCountryFieldId){
        if(addressNationCountryType) {
            pc4uFormAddressNationCountryType = addressNationCountryType;
        } else {
            pc4uFormAddressNationCountryType = "text";
        }
    }
    // Address Line
    if(addAddressLineId) {
        pc4uFormAddressLineFieldId = addAddressLineId;
    }
    
    // Address Block
    if(addAddressBlockId) {
        pc4uFormAddressBlockFieldId = addAddressBlockId;
    }
    
    return Pc4uSearchIdBegin("pc4uAdHocPostcode");
}

// ==================================================
// Postcodes4u Lookup Common 'Select Address Process'
//  Return Selected Address Details
//  Updated 2020.01.30 TO Suppress Lookups
//     when no address selected
// ==================================================
function Pc4uSearchIdBegin(lookupcode) {
    var pc4uDropdownFieldID = "pc4uDropdown";
    pc4uLookupFormCode = lookupcode;

    if (pc4uLookupFormCode == "pc4uWooBilling") {
        pc4uDropdownFieldID = "pc4uWooBillingDropdown";
    } else {
        if (pc4uLookupFormCode == "pc4uWooShipping") {
            pc4uDropdownFieldID = "pc4uWooShippingDropdown";
        } else {
            if (pc4uLookupFormCode == "pc4uCF7Postcode") {
                pc4uDropdownFieldID = "pc4uCF7Dropdown";
            }else {
            	if (pc4uLookupFormCode == "pc4uGravPostcode") {
            	    pc4uDropdownFieldID =  pc4uFormPostcodeFieldId +  '_' + "pc4uGfDropdown";
                }else {
                    if (pc4uLookupFormCode == "pc4uAdHocPostcode") {
                        pc4uDropdownFieldID = pc4uFormAddressDropdownFieldId;
                    } else {
                         if (pc4uLookupFormCode == "" ||  (pc4uLookupFormCode == "pc4u")) {
                            pc4uDropdownFieldID = "pc4uDropdown";
                         }
                    }
                }
            }
        }
    }

    var scriptTag = document.getElementById("postcodes4youelement");
    var headTag = document.getElementsByTagName("head").item(0);
    var strUrl = "";

    var AddrId = "";
    var pc4ukey = "";
    var pc4uUser = "";
    var pc4uShowWarnings = '0';

    var oField = document.getElementById(pc4uDropdownFieldID);
    if(oField) {
        AddrId = oField.value;
    }

    // Ensure Value id an Address ID
    if(AddrId && !isNaN(AddrId)) {
        // Get Postcodes4u Key
        var oField = document.getElementById("postcodes4ukey");
        if(oField) {
            pc4ukey = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4ukey']");
            //oField = document.querySelector("[data-orig_id='postcodes4ukey']");
            if(oField) {
                pc4ukey = (oField.textContent).trim();
            }
        }
        // Get Postcodes4u User
        var oField = document.getElementById("postcodes4uuser");
        if(oField) {
            pc4uUser = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4uuser']");
            //oField = document.querySelector("[data-orig_id='postcodes4uuser']");
            if(oField) {
                pc4uUser = (oField.textContent).trim();
            }
        }

        // Get 'Show Warnings' Option
        var oField = document.getElementById("pc4ushow_warnings");
        if(oField) {
            pc4uShowWarnings = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='pc4ushow_warnings']");
            //oField = document.querySelector("[data-orig_id='pc4ushow_warnings']");
            if(oField) {
                pc4uShowWarnings = (oField.textContent).trim();
            }
        }
        pc4uShowLookupWarnings = pc4uShowWarnings;

        //Build the url
        //  Check if HTTPS
         if (window.location.protocol == "'https:" || window.location.protocol == "https:") {
            strUrl = "https://services.3xsoftware.co.uk";
        } else {
            strUrl = "http://services.3xsoftware.co.uk";
        }
        strUrl += "/search/byid/json?";
        strUrl += "username=" + encodeURI(pc4uUser);
        strUrl += "&key=" + encodeURI(pc4ukey);
        strUrl += "&id=" + encodeURI(AddrId);

        strUrl += "&callback=Pc4uSearchIdEnd";
        //Make the request
        if (scriptTag) {
            try {
                headTag.removeChild(scriptTag);
            }
            catch (e) {
                //Ignore
                if(pc4uShowWarnings === '1') {
                    alert("SearchIdBegin Error:" + e.message);
                }
            }
        }
        scriptTag = document.createElement("script");
        scriptTag.src = strUrl;
        scriptTag.type = "text/javascript";
        scriptTag.id = "postcodes4youelement";
        headTag.appendChild(scriptTag);
    } else {
        // NO ID - Assume Form Not Displayed - Ignore
    }
}



// ====================================================
// Postcodes4u Lookup Common 'Select Address Process'
//  Return Selected Address Details
//   - Modified for 1.4.2 to work with Multiple Forms
// ====================================================
function Pc4uFormSearchIdBegin(cf7PostCodeFieldDiv, cf7PostCodeFieldSet, cf7PostCodeForm, lookupcode) {
    var pc4uDropdownFieldID = "pc4uDropdown";
    var pc4uDropdownFieldName = "";
    var pc4ObjElements = null;
    pc4uLookupFormCode = lookupcode;

    var lookupDropdown;

    if (pc4uLookupFormCode == "pc4uWooBilling") {
        pc4uDropdownFieldID = "pc4uWooBillingDropdown";
        pc4uFieldObjectForPostcode = null;
        lookupDropdown = document.getElementById(pc4uDropdownFieldID);
    } else {
        if (pc4uLookupFormCode == "pc4uWooShipping") {
            pc4uDropdownFieldID = "pc4uWooShippingDropdown";
            pc4uFieldObjectForPostcode = null;
            lookupDropdown = document.getElementById(pc4uDropdownFieldID);
        } else {
            if (pc4uLookupFormCode == "pc4uCF7Postcode") {
                pc4uDropdownFieldID = pc4uFormAddressDropdownFieldId
                pc4uDropdownFieldName = "pc4uCF7Dropdown";

                if(cf7PostCodeFieldDiv || cf7PostCodeFieldSet || cf7PostCodeForm) {
                    if(cf7PostCodeFieldDiv) {
                        pc4ObjElements = cf7PostCodeFieldDiv.querySelectorAll("*")
                        if(pc4ObjElements && pc4ObjElements.length > 0) {
                            //Legacy (IE) Browser Code - Use QuerySelect
                            var oFieldSetDrop = cf7PostCodeFieldDiv.querySelectorAll("[id*='"+pc4uDropdownFieldID+"']");
                            if(oFieldSetDrop && oFieldSetDrop.length > 0) {
                                lookupDropdown = oFieldSetDrop[0];
                                pc4uFieldObjectForPostcode = cf7PostCodeFieldDiv;
                            }
                        }
                    }
                    if(!lookupDropdown) {
                        if(cf7PostCodeFieldSet) {
                            //Legacy (IE) Browser Code - Use QuerySelect
                            var oFieldSetDrop = cf7PostCodeFieldSet.querySelectorAll("[id*='"+pc4uDropdownFieldID+"']");
                            if(oFieldSetDrop && oFieldSetDrop.length > 0) {
                                lookupDropdown = oFieldSetDrop[0];
                                pc4uFieldObjectForPostcode = cf7PostCodeFieldSet;
                            }
                        }
                    }
                    if(!lookupDropdown) {
                        if(cf7PostCodeForm) {
                            if(cf7PostCodeForm.elements && cf7PostCodeForm.elements.length > 0) {
                                // Modern Browser Code
                                lookupDropdown = cf7PostCodeForm.elements[pc4uDropdownFieldName];
                                pc4uFieldObjectForPostcode = cf7PostCodeForm;
                            } else {
                                //Legacy (IE) Browser Code - Use QuerySelect
                                var oFormDrop = cf7PostCodeForm.querySelectorAll("[id*='"+pc4uDropdownFieldID+"']");
                                if(oFormDrop && oFormDrop.length > 0) {
                                    lookupDropdown = oFormDrop[0];
                                    pc4uFieldObjectForPostcode = cf7PostCodeForm;
                                }
                            }
                        }
                    }
                } else {
                    pc4uFieldObjectForPostcode = null;
                }

                if(!lookupDropdown) {
                     // Last Change - Check Document
                    if(document.elements && document.elements.length > 0) {
                        lookupDropdown = document.elements(pc4uDropdownFieldName);
                    } else {
                        //Legacy (IE) Browser Code - Use QuerySelect
                        var oFormDrop = cf7PostCodeForm.querySelectorAll("[id*='"+pc4uDropdownFieldID+"']");
                        if(oFormDrop && oFormDrop.length > 0) {
                            lookupDropdown = oFormDrop[0];
                        }
                    }
                }
            }
        }
    }

    var AddrId = lookupDropdown.value;
    var pc4ukey  = "";
    var pc4uUser = "";
    var pc4uShowWarnings = '0';

    // Ensure Value id an Address ID
    if(AddrId && !isNaN(AddrId)) {
        var scriptTag = document.getElementById("postcodes4youelement");
        var headTag = document.getElementsByTagName("head").item(0);
        var strUrl = "";

        // Get Pc4u Key
        var oField = document.getElementById("postcodes4ukey");
        if(oField) {
            pc4ukey = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4ukey']");
            if(oField) {
                pc4ukey = (oField.textContent).trim();
            }
        }
        // Get Pc4u User
        var oField = document.getElementById("postcodes4uuser");
        if(oField) {
            pc4uUser = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='postcodes4uuser']");
            if(oField) {
                pc4uUser = (oField.textContent).trim();
            }
        }

        // Get 'Show Warnings' Option
        var oField = document.getElementById("pc4ushow_warnings");
        if(oField) {
            pc4uShowWarnings = (oField.textContent).trim();
        } else {
            oField = document.querySelector("[id^='pc4ushow_warnings']");
            //oField = document.querySelector("[data-orig_id='pc4ushow_warnings']");
            if(oField) {
                pc4uShowWarnings = (oField.textContent).trim();
            }
        }
        pc4uShowLookupWarnings = pc4uShowWarnings;

        //Build the url
        //  Check if HTTPS
        if (window.location.protocol == "'https:" || window.location.protocol == "https:") {
            strUrl = "https://services.3xsoftware.co.uk";
        } else {
            strUrl = "http://services.3xsoftware.co.uk";
        }
        strUrl += "/search/byid/json?";
        strUrl += "username=" + encodeURI(pc4uUser);
        strUrl += "&key=" + encodeURI(pc4ukey);
        strUrl += "&id=" + encodeURI(AddrId);

        strUrl += "&callback=Pc4uSearchIdEnd";
        //Make the request
        if (scriptTag) {
            try {
                headTag.removeChild(scriptTag);
            }
            catch (e) {
                //Ignore
                if(pc4uShowWarnings === '1') {
                    alert("SearchIdBegin Error:" + e.message);
                }
            }
        }
        scriptTag = document.createElement("script");
        scriptTag.src = strUrl;
        scriptTag.type = "text/javascript";
        scriptTag.id = "postcodes4youelement";
        headTag.appendChild(scriptTag);
    } else {
        // NO ID - Assume Form Not Displayed - Ignore
    }
}

// ====================================================================
// Postcodes4u Lookup 'Single Address Select Return 'Callback' Function
//  Populate Relavant Address Form with Returned Details
// ====================================================================
function Pc4uSearchIdEnd(response) {

    var showGfAddressWhenComplete = document.getElementById(pc4uFormPostcodeFieldId+"_pc4uGfPostcodeLookupAddress");

    //Test for an error
    if (response != null && response['Error'] != null) {
        //Show the error message
        alert("Postcode Lookup Call Error: " + response['Error'].Cause);

        // If Gravity Form Ensure NOT Hidden
        if(showGfAddressWhenComplete) {
            //
            showGfAddressWhenComplete.style.display = "inline";
        }
    }
    else {
        //Check if there were any items found
        if (response.length == 0) {
            alert("Sorry, no matching postcode items found");

            // If Gravity Form Ensure NOT Hidden
            if(showGfAddressWhenComplete) {
                //
                showGfAddressWhenComplete.style.display = "inline";
            }
        }
        else {
            // Decode Address
            var address = response["Address"];

            // Get Alt Address Format Setting
            var altAddressFmt = "";
            var altAddressFmtElement = document.getElementById('pc4ualt_address_disp');
            if (altAddressFmtElement != null && altAddressFmtElement.value != '') {
                altAddressFmt = altAddressFmtElement.textContent;
            }
            // Get County Value Setting
            var countyDisp = "";
            var countyNameElement = document.getElementById('pc4ucounty_disp');
            if (countyNameElement != null && countyNameElement.value != '') {
                countyDisp = countyNameElement.textContent;
            }

            // Decode Calling Form Code - Dropdown List  Field
            if (pc4uLookupFormCode == "pc4uWooBilling") {
                // Woo Commerce Billing
                Pc4uProcessWooBillingAddress(address, altAddressFmt, countyDisp);

            } else {
                if (pc4uLookupFormCode == "pc4uWooShipping") {
                    // Woo Commerce Shipping
                    Pc4uProcessWooShippingAddress(address, altAddressFmt, countyDisp);

                } else {
                    if (pc4uLookupFormCode == "pc4uCF7Postcode") {
                        // Contact Form 7
                        Pc4uProcessCf7Address(address, altAddressFmt, countyDisp);

                    } else {
			if (pc4uLookupFormCode == "pc4uGravPostcode") {
                            // Gravity Form
	                    Pc4uProcessGravAddress(address, altAddressFmt, countyDisp);
                        } else {
                            if (pc4uLookupFormCode == "pc4uAdHocPostcode") {
                                // 'AdHoc' Postcode - Fields Specified
                                Pc4uProcessAdHocAddress(address, altAddressFmt, countyDisp);
                            } else {
                                // Default Form (3x)
                                Pc4uProcess3xAddress( address, altAddressFmt, countyDisp);
                            }
                        }
                    }
                }
            }
        }
    }
}

// ====================================================================
// Pc4uProcessWooBillingAddress - Populate #WooCommerce Billing Address
//  with Returned Address Details
// ====================================================================
function Pc4uProcessWooBillingAddress( respAddress, addressFmt, countyDisp) {
    // Woo Commerce Billing Form
    var companyFieldFound = false ;
    var townFieldFound = false ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    // WooCommerce Billing Form - Fields
    var addresssCompanyField ;
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;
    var addressCountryField ;

    // Get Company & Town Fields
    addresssCompanyField = document.getElementById("billing_company");
    if(addresssCompanyField) {
        companyFieldFound = true ;
    }
    addressTownField = document.getElementById("billing_city");
    if(addressTownField) {
        townFieldFound = true ;
    }

    // Get Remaining Address Fields
    addressLine1Field = document.getElementById("billing_address_1");
    addressLine2Field = document.getElementById("billing_address_2");
    addressLine3Field = document.getElementById("billing_address_3");
    addressCountyField = document.getElementById("billing_state");
    addressPostcodeField = document.getElementById("billing_postcode");
    addressCountryField = document.getElementById("billing_country");


    var fmtAddress ;

    if( addressFmt.trim() == '1') {
        // Alternative Address Format
        // Decode
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp);
    } else {
         // NORMAL ADDRESS - RAW RETURNED DATA
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp);
    }

    addressCompany = fmtAddress.AddCompany;
    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();

    //Populate Address Fields
    if(addresssCompanyField) {
        addresssCompanyField.value = addressCompany;
    }
    if(addressLine1Field){
        addressLine1Field.value = addressLine1;
    }

    //Pack Line 3 Details onto Line 2 If 3 Not Found
    if(addressLine2Field){
        addressLine2Field.value = addressLine2;

        if(!addressLine3Field) {
            if(addressLine3 !== "") {
                if(addressLine2Field.value !== "") {
                    addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                }
                addressLine2Field.value += addressLine3;
                addressLine3 = "";
            }
        }
    }
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
    }

    if(addressTownField){
        addressTownField.value = addressTown;
    }
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
    }
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
    }
    // Set Country to UK (GB)
    if(addressCountryField){
        addressCountryField.value = "GB";
    }

    // Clear/Reset Dropdown
    var lookupDropdown = document.getElementById("pc4uWooBillingDropdown");

    // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
    //  -- Clear Dropdown
    hidePc4uAddressDropdown(lookupDropdown, true);
}

// ======================================================================
// Pc4uProcessWooShippingAddress - Populate WooCommerce Shipping Address
//  with Returned Address Details
// ======================================================================
function Pc4uProcessWooShippingAddress( respAddress, addressFmt, countyDisp) {
    // Woo Commerce Shipping Form
    var companyFieldFound = false ;
    var townFieldFound = false ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    // WooCommerce Shipping Form - Fields
    var addresssCompanyField ;
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;
    var addressCountryField ;

     // Get Company & Town Fields
    addresssCompanyField= document.getElementById("shipping_company");
    if(addresssCompanyField){
        companyFieldFound = true ;
    }
    addressTownField = document.getElementById("shipping_city");
    if(addressTownField){
        townFieldFound = true ;
    }
    // Get Remaining Address Fields
    addressLine1Field = document.getElementById("shipping_address_1");
    addressLine2Field = document.getElementById("shipping_address_2");
    addressLine3Field = document.getElementById("shipping_address_3");
    addressCountyField = document.getElementById("shipping_state");
    addressPostcodeField = document.getElementById("shipping_postcode");
    addressCountryField = document.getElementById("shipping_country");

    var fmtAddress;

    if( addressFmt.trim() == '1') {
        // ALTERNATE 'PACKED' FORMAT
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp);
    } else {
        // NORMAL ADDRESS - RAW RETURNED DATA
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp);
    }

    addressCompany = fmtAddress.AddCompany;
    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();

    // Populate Company Details
    if(addresssCompanyField) {
        addresssCompanyField.value = addressCompany;
    }

    //Populate Address Line 1-3 Fields
    if(addressLine1Field){
        addressLine1Field.value = addressLine1;
    }

    //..Pack Line 3 Details onto Line 2 If 3 Not Found
    if(addressLine2Field){
        addressLine2Field.value = addressLine2;

        if(!addressLine3Field) {
            if(addressLine3 !== "") {
                if(addressLine2Field.value !== "") {
                    addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                }
                addressLine2Field.value += addressLine3;
                addressLine3 = "";
            }
        }
    }
    // Address 3 (If Present)
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
    }

    // Town Field
    if(addressTownField){
        addressTownField.value = addressTown;
    }

    // County Field
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
    }

    // Postcode Field
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
    }
    // Set Country to UK (GB)
    if(addressCountryField){
        addressCountryField.value = "GB";
    }

    // Clear/Reset Dropdown
    var lookupDropdown = document.getElementById("pc4uWooShippingDropdown");

    // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
    // -- Clear Pc4u Dropdown
    hidePc4uAddressDropdown(lookupDropdown, true);
}


// ====================================================================
// Pc4uProcessCf7Address - Populate Contact Form 7 Address
//  with Returned Address Details
//  - Modified in 1.4.2 for multiple CF7 Forms
// ====================================================================
function Pc4uProcessCf7Address( respAddress, addressFmt, countyDisp) {

    var oCf7Form = pc4uFieldObjectForPostcode;

    var fillEmptyFields = true ;

    // Contact Form7 Form - Fields
    var addresssCompanyField ;
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;
    var addressSelectField ;

    var addressSummaryLineField ;
    var addressSummaryBlockField ;

    var addressHiddenField ;

    // Needed for Address Decode
    var companyFieldFound = false ;
    var townFieldFound = false ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    var addressSummaryLine = "";
    var addressSummaryBlock = "";

    var addressSummaryBlock = "";

    // Decode Address Fields
    var cf7Fields = Pc4uExtractCF7FormFields( oCf7Form);

    addresssCompanyField = cf7Fields.FormCompany;
    addressLine1Field = cf7Fields.FormAddLine1;
    addressLine2Field = cf7Fields.FormAddLine2;
    addressLine3Field = cf7Fields.FormAddLine3;
    addressTownField = cf7Fields.FormTown;
    addressCountyField = cf7Fields.FormCounty;
    addressSelectField = cf7Fields.FormSelectAddress;

    addressSummaryLineField = cf7Fields.FormAddressSummaryLine;
    addressSummaryBlockField = cf7Fields.FormAddressSummaryBlock;

    addressHiddenField = cf7Fields.FormAddressHiddenArea;

    //Ensure Postcode Is Upper Case
    addressPostcodeField = cf7Fields.FormPostcode


    if( addresssCompanyField) {
        companyFieldFound = true ;
    }
    if(addressTownField) {
        townFieldFound = true ;
    }

    // Process Address Data IAW Format
    var fmtAddress ;
    if( addressFmt.trim() === '1') {
        // ALTERNATE 'PACKED' FORMAT

        // Decode
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp);
    } else {
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp);
    }
    addressCompany = fmtAddress.AddCompany;
    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();

    addressSummaryLine = fmtAddress.AddDetailsLine;
    addressSummaryBlock = fmtAddress.AddDetailsBlock;

    // Populate Address Fields
    // - Note - All Fields Are 'Optional'

    // Company
    if(addresssCompanyField) {
        addresssCompanyField.value = addressCompany;
        addresssCompanyField.focus();
    }
    // ADDRESS LINE1
    if(addressLine1Field) {
        addressLine1Field.value = addressLine1;
        addressLine1Field.focus();
    }
    // ADDRESS LINE2
    if(addressLine2Field) {
        addressLine2Field.value = addressLine2;
        addressLine2Field.focus();

        if(!addressLine3Field) {
            if(addressLine3 !== "") {
                if(addressLine2Field.value !== "") {
                    addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                }
                addressLine2Field.value += addressLine3;
                addressLine3 = "";

                addressLine2Field.focus();
            }
        }
    }
    // Address 3 (If Present)
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
        addressLine3Field.focus();
    }

    // Town/City
    if(addressTownField) {
        addressTownField.value = addressTown;
        addressTownField.focus();
    }
    // County
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
        addressCountyField.focus();
    }
    // Postcode
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
        addressPostcodeField.focus();
    }

    // Address Line (If Present)
    if(addressSummaryLineField) {
        addressSummaryLineField.value = addressSummaryLine;
        addressSummaryLineField.focus();
    }

    // Address Block (If Present)
    if(addressSummaryBlockField) {
        addressSummaryBlockField.value = addressSummaryBlock;
        addressSummaryBlockField.focus();
    }

    // Clear Address Select DropDown List
    if(addressSelectField) {
        // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
        // -- Clear Pc4u Dropdown
        hidePc4uAddressDropdown(addressSelectField, true );
    }

    // If Ensure 'Fields Populated' is required - do not allow empty Address Fie;ds
    if(fillEmptyFields) {

        if(addresssCompanyField && !addresssCompanyField.value){
            addresssCompanyField.value = " ";
        }

        if(addressLine1Field && !addressLine1Field.value){
            addressLine1Field.value = " ";
        }
        if(addressLine2Field && !addressLine2Field.value){
            addressLine2Field.value = " ";
        }

        if(addressLine3Field && !addressLine3Field.value){
            addressLine3Field.value = " ";
        }
        if(addressTownField && !addressTownField.value){
            addressTownField.value = " ";
        }
        if(addressCountyField && !addressCountyField.value){
            addressCountyField.value = " ";
        }
        if(addressPostcodeField && !addressPostcodeField.value){
            addressPostcodeField.value = " ";
        }
    }

    // If Display When Complete Div Available than Display It
    Pc4uShowHiddenCf7Address(addressHiddenField);
}




// ====================================================================
// Pc4uShowHiddenCf7Address - Unhide Contact Form 7 Address
// ====================================================================
function Pc4uShowHiddenCf7Address( cf7addressHiddenField) {
    if(cf7addressHiddenField) {        
        // If Display When Complete Div Available than Display It
        if(cf7addressHiddenField) {
            //
            cf7addressHiddenField.style.display = '';
        }
    }
}






// ====================================================================
// Pc4uProcessGravAddress - Populate Gravity Form Address
//  with Returned Address Details
// ====================================================================
function Pc4uProcessGravAddress( respAddress, addressFmt, countyDisp) {
    var companyFieldFound = false ;
    var townFieldFound = false ;
    var separateNameNumberFieldFound = false ;

    var fillEmptyFields = true ;

    // Ensure
    var populateEmpty = true ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";  // Complete Address Line 1
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    // Used when separate House Name/No and Street
    var addressLine1_NameNumberOnly = ""; // Optional
    var addressLine1_StreetOnly = ""; // Optional
    var addressLine2_StreetOnly = ""; // Optional

    // Gravity Form - Fields
    var addresssCompanyField ;
    var addressLine1_NameNumberField ; //Optional
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;

     // Get Company & Town Fields
     // // GF No Company Field
    //addresssCompanyField= document.getElementById("pc4uCompany");
    //if(addresssCompanyField){
    //    companyFieldFound = true ;
    //}
    addressTownField = document.getElementById(pc4uFormPostcodeFieldId + '_3');
    if(addressTownField){
        townFieldFound = true ;
    }
    // Get Remaining Address Fields

    // Look For Optional Address Line 1 Name/Number Field
    addressLine1_NameNumberField = document.getElementById(pc4uFormPostcodeFieldId + '_7');
    if(addressLine1_NameNumberField) {
        separateNameNumberFieldFound = true;
    }

    addressLine1Field = document.getElementById(pc4uFormPostcodeFieldId + '_1');
    addressLine2Field = document.getElementById(pc4uFormPostcodeFieldId + '_2');
    //addressLine3Field = document.getElementById("pc4uAddress3");

    addressCountyField = document.getElementById(pc4uFormPostcodeFieldId + '_4');
    addressPostcodeField = document.getElementById(pc4uFormPostcodeFieldId + '_5');

    var fmtAddress;
    if( addressFmt.trim() == '1') {
    	// Alternative Address Format
        // Decode
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp, populateEmpty);
    } else {
        // NORMAL ADDRESS - RAW RETURNED DATA
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp, populateEmpty);
    }

    addressCompany = fmtAddress.AddCompany;

    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();

    // Used when separate House Name/No and Street
    addressLine1_NameNumberOnly = fmtAddress.AddLine1_NameNumOnly;
    addressLine1_StreetOnly = fmtAddress.AddLine1_StreetOnly;
    addressLine2_StreetOnly = fmtAddress.AddLine2_StreetOnly;


    //Populate Address Fields
    if(addresssCompanyField && addressCompany) {
        addresssCompanyField.value = addressCompany;
    }

    // Check For Separate Name/Number and Street Name
    if(separateNameNumberFieldFound){
        if(addressLine1_NameNumberField) {
            addressLine1_NameNumberField.value = addressLine1_NameNumberOnly;
        }
        if(addressLine1Field) {
            addressLine1Field.value = "";
            if(addressCompany) {
                addressLine1Field.value += addressCompany ;

                if(addressLine2_StreetOnly) {
                    addressLine1Field.value += ", " +addressLine1_StreetOnly;
                    if(addressLine2Field){
                        addressLine2Field.value = addressLine2_StreetOnly;
                    }
                } else {
                    // No Street 2
                    addressLine2Field.value = addressLine1_StreetOnly;
                }
            } else {
                // No Company - Just Populate Street Address 1 & 2
                addressLine1Field.value = addressLine1_StreetOnly;
                addressLine2Field.value = addressLine2_StreetOnly;
            }
        } else {
            if(addressLine2Field){
                addressLine2Field.value = addressLine2_StreetOnly;
            }
        }
    } else {
        // No Separate Street Name - Normal Processing
        if(addressLine1Field){
            addressLine1Field.value = addressLine1;
        }

        if(addressLine2Field){
            addressLine2Field.value = addressLine2;

            if(!addressLine3Field) {
                if(addressLine3 !== "") {
                    if(addressLine2Field.value !== "") {
                        addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                    }
                    addressLine2Field.value += addressLine3;
                    addressLine3 = "";
                }
            }
        }
    }

    // Address 3 (If Present)
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
    }

    if(addressTownField){
        addressTownField.value = addressTown;
    }
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
    }
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
    }

    // If Ensure 'Fields Populated' is required - do not allow empty Address Fie;ds
    if(fillEmptyFields) {
        if(addresssCompanyField && !addresssCompanyField.value){
            addresssCompanyField.value = " ";
        }

        if(addressLine1Field && !addressLine1Field.value){
            addressLine1Field.value = " ";
        }
        if(addressLine2Field && !addressLine2Field.value){
            addressLine2Field.value = " ";
        }

        if(addressLine3Field && !addressLine3Field.value){
            addressLine3Field.value = " ";
        }
        if(addressTownField && !addressTownField.value){
            addressTownField.value = " ";
        }
        if(addressCountyField && !addressCountyField.value){
            addressCountyField.value = " ";
        }
        if(addressPostcodeField && !addressPostcodeField.value){
            addressPostcodeField.value = " ";
        }
    }

    // Clear DropDown List -
    var dropdown = document.getElementById(pc4uFormPostcodeFieldId +"_pc4uGfDropdown");

    // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
    // -- Clear Pc4u Dropdown
    hidePc4uAddressDropdown(dropdown, true);

    
    // If Display When Complete Div Available than Display It
    showPc4uGFHiddenAddress(pc4uFormPostcodeFieldId);
    //var showAddressWhenComplete = document.getElementById(pc4uFormPostcodeFieldId+"_pc4uGfPostcodeLookupAddress");
    //if(showAddressWhenComplete) {
      //  //
      //  showAddressWhenComplete.style.display = '';    
    //}
}


// ====================================================================
// Pc4uProcessAdHocAddress - Populate 'AdHoc' address with specified
//   address fields with Returned Address Details
//  KRF 23.08.14 - Add AddressLine and Block
// ====================================================================
function Pc4uProcessAdHocAddress ( respAddress, addressFmt, countyDisp) {
    // AdHoc Address - Specified Fields
    var companyFieldFound = false ;
    var townFieldFound = false ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";
    // Address Summary Line and Block Values
    var addressSummaryLine = "";
    var addressSummaryBlock = "";

    // 'AdHoc Form - Fields
    var addresssCompanyField ;
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;

    var addressCountryField ;
    // Address Summary Line and Block Field IDs
    var addressSummaryLineField ;
    var addressSummaryBlockField ;

    // Get Company & Town Fields
    addresssCompanyField= document.getElementById(pc4uFormAddressCompanyFieldId);
    if(addresssCompanyField){
        companyFieldFound = true ;
    }
    addressTownField = document.getElementById(pc4uFormAddressTownFieldId);
    if(addressTownField){
        townFieldFound = true ;
    }
    // Get Remaining Address Fields
    addressLine1Field = document.getElementById(pc4uFormAddressLine1FieldId);
    addressLine2Field = document.getElementById(pc4uFormAddressLine2FieldId);
    addressLine3Field = document.getElementById(pc4uFormAddressLine3FieldId);

    addressCountyField = document.getElementById(pc4uFormAddressCountyFieldId);
    addressPostcodeField = document.getElementById(pc4uFormPostcodeFieldId);

    addressCountryField = document.getElementById(pc4uFormAddressNationCountryFieldId);
    
    // Try For Summary Line and Block
    addressSummaryLineField = document.getElementById(pc4uFormAddressLineFieldId);
    addressSummaryBlockField = document.getElementById(pc4uFormAddressBlockFieldId);

    var fmtAddress ;
    if( addressFmt.trim() == '1') {
    	// Alternative Address Format
        // Decode
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp);
    } else {
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp);
    }

    addressCompany = fmtAddress.AddCompany;
    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();
    addressSummaryLine = fmtAddress.AddDetailsLine;
    addressSummaryBlock = fmtAddress.AddDetailsBlock;
        
    //Populate Address Fields
    if(addresssCompanyField) {
        addresssCompanyField.value = addressCompany;
    }
    if(addressLine1Field){
        addressLine1Field.value = addressLine1;
    }
    if(addressLine2Field){
        addressLine2Field.value = addressLine2;

        if(!addressLine3Field) {
            if(addressLine3 !== "") {
                if(addressLine2Field.value !== "") {
                    addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                }
                addressLine2Field.value += addressLine3;
                addressLine3 = "";
            }
        }
    }
    // Address 3 (If Present)
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
    }

    if(addressTownField){
        addressTownField.value = addressTown;
    }
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
    }
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
    }

    // Optionally Process Country - FORCE UK/GB
    if(addressCountryField) {
        var AddressType = "text";
        if(pc4uFormAddressNationCountryType) {
            if(pc4uFormAddressNationCountryType = "wp_dropdown") {
                // WordPress Country Field
                addressCountryField.value = "GB";
            } else {
                if(pc4uFormAddressNationCountryType = "dropdown") {
                    // General Dropdown Field
                    addressCountryField.value = "United Kingdom";
                    addressCountryField.selected = true;
                } else {
                    // Default Text Field
                    addressCountryField.value = "United Kingdom";
                }
            }
        }
    }
    
    // Summary (Single Line) Address
    if(addressSummaryLineField) {
        addressSummaryLineField.value = addressSummaryLine;
    }

    // Summary (Block) Address
    if(addressSummaryBlockField) {
        addressSummaryBlockField.value = addressSummaryBlock;
    }


    // Clear DropDown List -
    var dropdown = document.getElementById(pc4uFormAddressDropdownFieldId);

    // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
    // -- Clear Pc4u Dropdown
    hidePc4uAddressDropdown(dropdown, true);
}

// ====================================================================
// Pc4uProcess3xAddress - Populate 3X Basic Contact Form Address
//  with Returned Address Details
// ====================================================================
function Pc4uProcess3xAddress( respAddress, addressFmt, countyDisp) {
    // Default Form (3x)

    var companyFieldFound = false ;
    var townFieldFound = false ;

    // Address Components
    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    // 3X Contact Form - Fields
    var addresssCompanyField ;
    var addressLine1Field ;
    var addressLine2Field ;
    var addressLine3Field ;
    var addressTownField ;
    var addressCountyField ;
    var addressPostcodeField ;

     // Get Company & Town Fields
    addresssCompanyField= document.getElementById("pc4uCompany");
    if(addresssCompanyField){
        companyFieldFound = true ;
    }
    addressTownField = document.getElementById("pc4uTown");
    if(addressTownField){
        townFieldFound = true ;
    }
    // Get Remaining Address Fields
    addressLine1Field = document.getElementById("pc4uAddress1");
    addressLine2Field = document.getElementById("pc4uAddress2");
    addressLine3Field = document.getElementById("pc4uAddress3");

    addressCountyField = document.getElementById("pc4uCounty");
    addressPostcodeField = document.getElementById("pc4uPostcode");

    var fmtAddress ;
    if( addressFmt.trim() == '1') {
    	// Alternative Address Format
        // Decode
        fmtAddress = Pc4uDecodeAddressFormat1( respAddress, companyFieldFound, townFieldFound, countyDisp);
    } else {
        fmtAddress = Pc4uReturnRawAddress(respAddress, companyFieldFound, townFieldFound, countyDisp);
    }

    addressCompany = fmtAddress.AddCompany;
    addressLine1 = fmtAddress.AddLine1;
    addressLine2 = fmtAddress.AddLine2;
    addressLine3 = fmtAddress.AddLine3;
    addressTown = fmtAddress.AddTown;
    addressCounty = fmtAddress.AddCounty;
    addressPostcode = fmtAddress.AddPostcode.toUpperCase();

    //Populate Address Fields
    if(addresssCompanyField) {
        addresssCompanyField.value = addressCompany;
    }
    if(addressLine1Field){
        addressLine1Field.value = addressLine1;
    }
    if(addressLine2Field){
        addressLine2Field.value = addressLine2;

        if(!addressLine3Field) {
            if(addressLine3 !== "") {
                if(addressLine2Field.value !== "") {
                    addressLine2Field.value = addressLine2Field.value.trim() + ", ";
                }
                addressLine2Field.value += addressLine3;
                addressLine3 = "";
            }
        }
    }
    // Address 3 (If Present)
    if(addressLine3Field){
        addressLine3Field.value = addressLine3;
    }

    if(addressTownField){
        addressTownField.value = addressTown;
    }
    if(addressCountyField) {
        addressCountyField.value = addressCounty;
    }
    if(addressPostcodeField) {
        addressPostcodeField.value = addressPostcode;
    }

    // Clear DropDown List - ID HardCoded in Plugin
    var dropdown = document.getElementById("pc4uDropdown");

    // Use New 'Hide' Pc4u Address Dropdown Function KRF 2020.04.30
    // -- Clear Pc4u Dropdown
    hidePc4uAddressDropdown(dropdown, true);
}


// ====================================================================
// Pc4uDecodeAddressFormat1 - Return Address in an array using
//   Address Format 1 - 'Alternative' Or No Duplicate Address Format
// ====================================================================
function Pc4uDecodeAddressFormat1( procAddress, companyFieldPresent, townFieldPresent, countyDisp, populateEmpty) {

    var companyFieldFound = false ;
    var townFieldFound = false ;

    var addressCompany = "";

    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";

    // Address Details As Line or Text Block
    var addressDetailsLine = "";
    var addressDetailsBlock = "";

    // Separate Building No/Street
    var addressLine1_NameNum_Only = "";
    var addressLine1_Street_Only = "";
    var addressLine2_Street_Only = "";

    // Process Company and Town Fields
    addressCompany = procAddress.Company;
    if(companyFieldPresent && companyFieldPresent === true) {
        companyFieldFound = true ;
    }

    if(townFieldPresent && townFieldPresent === true) {
        townFieldFound = true ;
        addressTown = procAddress.PostTown;
    }

    // Process Address Lines 1, 2, 3 and 4
    // Check Address Line1
    if((companyFieldFound === true && procAddress.Line1 === procAddress.Company) ||
        (townFieldFound === true && procAddress.Line1 === procAddress.PostTown) ||
        (procAddress.Line1 === procAddress.County) ||
        (procAddress.Line1 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line1
    } else {
        addressLine1 = procAddress.Line1;
    }
    // Check Address Line2
    if((companyFieldFound === true && procAddress.Line2 === procAddress.Company) ||
       (townFieldFound === true && procAddress.Line2 === procAddress.PostTown) ||
       (procAddress.Line2 === procAddress.County) ||
       (procAddress.Line2 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line2
    } else {
        if(addressLine1 === "") {
            addressLine1 = procAddress.Line2;
        } else {
            addressLine2 = procAddress.Line2;
        }
    }
    // Check Address Line3
    if(addressLine2 === "" && (companyFieldFound === true && procAddress.Line3 === procAddress.Company) ||
        (townFieldFound === true && procAddress.Line3 === procAddress.PostTown) ||
        (procAddress.Line3 === procAddress.County) ||
        (procAddress.Line3 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line3
    } else {
        if(addressLine1 === "") {
            addressLine1 = procAddress.Line3;
        } else {
            if(addressLine2 === "") {
                addressLine2 = procAddress.Line3;
            } else {
                addressLine3 = procAddress.Line3;
            }
        }
    }

    // Process Separate Address Line1 Details
    if(procAddress.SubBuilding) {
        addressLine1_NameNum_Only = procAddress.SubBuilding + ", " ;
    }
    if(procAddress.BuildingName) {
        addressLine1_NameNum_Only += procAddress.BuildingName;
    } else {
        addressLine1_NameNum_Only += procAddress.BuildingNumber;
    }

    addressLine1_Street_Only = procAddress.PrimaryStreet;
    addressLine2_Street_Only = procAddress.SecondaryStreet;

    // Process County
    if(countyDisp === '1') {
        // Administrative County
        addressCounty = procAddress.AdministrativeCounty
    } else{
        if(countyDisp === '2') {
            // Postal County
            addressCounty = procAddress.PostalCounty;
        } else {
            if(countyDisp === '3') {
                // Traditional County
                addressCounty = procAddress.TraditionalCounty;
      		} else {
				if(countyDisp === '4') {
					addressCounty = ""
				} else {
					// Default County
					addressCounty = procAddress.County;
				}
            }
        }
    }

    // Process Postcode
    addressPostcode = procAddress.Postcode.toUpperCase();

    // ----------------------------------------
    // Populate Address Details Line & Block
    // Assume ALL Fields - Ensure No Duplication

    //   Company
    if(addressCompany) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ';
            addressDetailsBlock = addressDetailsBlock + '\n';
        }
        addressDetailsLine = addressDetailsLine + addressCompany
        addressDetailsBlock = addressDetailsBlock + addressCompany
    }

    // Check Address Line1
    if((procAddress.Line1 === procAddress.Company) ||
        (procAddress.Line1 === procAddress.PostTown) ||
        (procAddress.Line1 === procAddress.County) ||
        (procAddress.Line1 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line1
    } else {
        if(procAddress.Line1) {
            if(addressDetailsLine) {
                addressDetailsLine = addressDetailsLine + ', ';
                addressDetailsBlock = addressDetailsBlock + '\n';
            }
            addressDetailsLine = addressDetailsLine + procAddress.Line1
            addressDetailsBlock = addressDetailsBlock + procAddress.Line1
        }
    }

    // Check Address Line2
    if((procAddress.Line2 === procAddress.Company) ||
       (procAddress.Line2 === procAddress.PostTown) ||
       (procAddress.Line2 === procAddress.County) ||
       (procAddress.Line2 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line2
    } else {
        if(procAddress.Line2) {
            if(addressDetailsLine) {
                addressDetailsLine = addressDetailsLine + ', ';
                addressDetailsBlock = addressDetailsBlock + '\n';
            }
            addressDetailsLine = addressDetailsLine + procAddress.Line2
            addressDetailsBlock = addressDetailsBlock + procAddress.Line2
        }
    }
    // Check Address Line3
    if((procAddress.Line3 === procAddress.Company) ||
        (procAddress.Line3 === procAddress.PostTown) ||
        (procAddress.Line3 === procAddress.County) ||
        (procAddress.Line3 === procAddress.Postcode)) {
        // Company, Town, County or Postcode on address Line3
    } else {
         if(procAddress.Line3) {
            if(addressDetailsLine) {
                addressDetailsLine = addressDetailsLine + ', ';
                addressDetailsBlock = addressDetailsBlock + '\n';
            }
            addressDetailsLine = addressDetailsLine + procAddress.Line3
            addressDetailsBlock = addressDetailsBlock + procAddress.Line3
        }
    }

    if(procAddress.PostTown) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + procAddress.PostTown;
        addressDetailsBlock = addressDetailsBlock + procAddress.PostTown;
    }
    if(addressCounty) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressCounty;
        addressDetailsBlock = addressDetailsBlock + addressCounty;
    }

    if(procAddress.Postcode) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + procAddress.Postcode;
        addressDetailsBlock = addressDetailsBlock + procAddress.Postcode;
    }

    // Return Address Details as string array
    return  {
        AddCompany: addressCompany,
        AddLine1: addressLine1,
        AddLine2: addressLine2,
        AddLine3: addressLine3,
        AddTown: addressTown,
        AddCounty: addressCounty,
        AddPostcode: addressPostcode,

        AddLine1_NameNumOnly: addressLine1_NameNum_Only,
        AddLine1_StreetOnly: addressLine1_Street_Only,
        AddLine2_StreetOnly: addressLine2_Street_Only,

        AddDetailsLine: addressDetailsLine,
        AddDetailsBlock: addressDetailsBlock

    };
}


// ====================================================================
// Pc4uReturnRawAddress - Return Address in an array using
//   Address Format 0 - 'RAW' Format
// ====================================================================
function Pc4uReturnRawAddress( procAddress, companyFieldPresent, townFieldPresent, countyDisp, populateEmpty) {

    var addressCompany = "";
    var addressLine1 = "";
    var addressLine2 = "";
    var addressLine3 = "";
    var addressTown = "";
    var addressCounty = "";
    var addressPostcode = "";
    var addressDetailsLine = "";
    var addressDetailsBlock = "";

    // Separate Building No/Street
    var addressLine1_NameNum_Only = "";
    var addressLine1_Street_Only = "";
    var addressLine2_Street_Only = "";


    // Set Company - PostTown and Postcode
    addressCompany = procAddress.Company;
    addressTown = procAddress.PostTown;
    addressPostcode = procAddress.Postcode.toUpperCase();

    //Process Address Lines 1 and 2
    addressLine1 = procAddress.Line1;
    addressLine2 = procAddress.Line2;
    addressLine3 = procAddress.Line3;

    // Process Separate Address Line Details
    if(procAddress.SubBuilding) {
        addressLine1_NameNum_Only = procAddress.SubBuilding + ", " ;
    }
    if(procAddress.BuildingName) {
        addressLine1_NameNum_Only += procAddress.BuildingName;
    } else {
        addressLine1_NameNum_Only += procAddress.BuildingNumber;
    }
    addressLine1_Street_Only = procAddress.PrimaryStreet;
    addressLine2_Street_Only = procAddress.SecondaryStreet;

    // Process County
    if(countyDisp === '1') {
        // Administrative County
        addressCounty = procAddress.AdministrativeCounty;
    } else{
        if(countyDisp === '2') {
            // Postal County
            addressCounty = procAddress.PostalCounty;
        } else {
            if(countyDisp === '3') {
                // Traditional County
                addressCounty = procAddress.TraditionalCounty;
            	if(countyDisp === '4') {
					addressCounty = ""
				} else {
					// Default County
					addressCounty = procAddress.County;
                }
			}
        }
    }

    // ---------------------------------------
    // Populate Address Details Line & Block
    // No Duplicate Check
    if(addressCompany) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ';
            addressDetailsBlock = addressDetailsBlock + '\n';
        }
        addressDetailsLine = addressDetailsLine + addressCompany
        addressDetailsBlock = addressDetailsBlock + addressCompany
    }

    if(addressLine1) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressLine1;
        addressDetailsBlock = addressDetailsBlock + addressLine1;
    }

    if(addressLine2) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressLine2;
        addressDetailsBlock = addressDetailsBlock + addressLine2;
    }
    if(addressLine3) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressLine3;
        addressDetailsBlock = addressDetailsBlock + addressLine3;
    }
    if(addressTown) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressTown;
        addressDetailsBlock = addressDetailsBlock + addressTown;
    }
    if(addressCounty) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressCounty;
        addressDetailsBlock = addressDetailsBlock + addressCounty;
    }

    if(addressPostcode) {
        if(addressDetailsLine) {
            addressDetailsLine = addressDetailsLine + ', ' ;
            addressDetailsBlock = addressDetailsBlock + '\n' ;
        }
        addressDetailsLine = addressDetailsLine + addressPostcode;
        addressDetailsBlock = addressDetailsBlock + addressPostcode;
    }

    // Return Address Details as string array
    return  {
        AddCompany: addressCompany,
        AddLine1: addressLine1,
        AddLine2: addressLine2,
        AddLine3: addressLine3,
        AddTown: addressTown,
        AddCounty: addressCounty,
        AddPostcode: addressPostcode,

        AddLine1_NameNumOnly: addressLine1_NameNum_Only,
        AddLine1_StreetOnly: addressLine1_Street_Only,
        AddLine2_StreetOnly: addressLine2_Street_Only,

        AddDetailsLine: addressDetailsLine,
        AddDetailsBlock: addressDetailsBlock
    };
}

// ====================================================================
// Pc4uExtractCF7FormFields - Return Address in an array
//
// ====================================================================
function Pc4uExtractCF7FormFields( oformObject) {

    var formObjectValid = false ;
    var oFormElements ;

    var oFormLegacyDropDownElements ;
    var oNonFormAddressElements;
    var testAddressFields ;

    var addressCompany = null;
    var addressLine1 = null;
    var addressLine2 = null;
    var addressLine3 = null;
    var addressTown = null;
    var addressCounty = null;
    var addressPostcode = null;
    var addressSummaryLine = null;
    var addressSummaryBlock = null;

    var addressSelectDropdown = null;

    var addressHiddenDiv = null;

    if(oformObject) {
        // Legacy Browser (IE) Get All Input and Select Objects
        oFormElements = oformObject.querySelectorAll("*");

        if(oFormElements && oFormElements.length > 0) {
            formObjectValid = true;
        }

        if(formObjectValid){
            for (var i = 0; i < oFormElements.length; i++) {
                // Note Not Using 'includes' of Legacy Browser Compatability
                if(oFormElements[i].id.indexOf("pc4uCf7Company") !== -1 ||
                   oFormElements[i].classList.contains("pc4uCf7Company")) {
                    addressCompany = oFormElements[i];
                } else {
                    if(oFormElements[i].id.indexOf("pc4uCF7Address1") !== -1 || 
                       oFormElements[i].classList.contains("pc4uCF7Address1") ) {
                        addressLine1 = oFormElements[i];
                    } else {
                        if(oFormElements[i].id.indexOf("pc4uCF7Address2") !== -1 ||
                           oFormElements[i].classList.contains("pc4uCF7Address2") ) {
                            addressLine2 = oFormElements[i];
                        } else {
                            if(oFormElements[i].id.indexOf("pc4uCF7Address3") !== -1 ||
                               oFormElements[i].classList.contains("pc4uCF7Address3") ) {                            
                                addressLine3 = oFormElements[i];
                            } else {
                                if(oFormElements[i].id.indexOf("pc4uCF7City") !== -1 ||
                                    oFormElements[i].classList.contains("pc4uCF7City")) {
                                    addressTown = oFormElements[i];
                                } else {
                                    if(oFormElements[i].id.indexOf("pc4uCF7County") !== -1 ||
                                       oFormElements[i].classList.contains("pc4uCF7County") ) {
                                        addressCounty = oFormElements[i];
                                    } else {
                                        if(oFormElements[i].id.indexOf("pc4upostcode") !== -1 ||
                                            oFormElements[i].classList.contains("pc4upostcode")) {
                                            addressPostcode = oFormElements[i];
                                        }else {
                                            if(oFormElements[i].id.indexOf("pc4uCF7Dropdown") !== -1 ||
                                               oFormElements[i].classList.contains("pc4uCF7Dropdown") ) {
                                                addressSelectDropdown = oFormElements[i];
                                            } else {
                                                if(oFormElements[i].id.indexOf("pc4uCF7AddressLine") !== -1 ||
                                                   oFormElements[i].classList.contains("pc4uCF7AddressLine")) {
                                                    addressSummaryLine = oFormElements[i];
                                                } else {
                                                    if(oFormElements[i].id.indexOf("pc4uCF7AddressBlock") !== -1 ||
                                                        oFormElements[i].classList.contains("pc4uCF7AddressBlock")) {
                                                        addressSummaryBlock = oFormElements[i];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Get Any 'Hidden Address Fields' Form Div
            if(addressHiddenDiv == null) {
                oNonFormAddressElements = oformObject.querySelectorAll("[id*='pc4uCF7AddressHidden']");
                if(oFormLegacyDropDownElements && oFormLegacyDropDownElements.length > 0) {
                    addressHiddenDiv = oFormLegacyDropDownElements[0];
                }
            }

            // Legacy Browser (IE) Postcode Dropdown Test
            if(addressSelectDropdown == null) {
                oFormLegacyDropDownElements = oformObject.querySelectorAll("[id*='pc4uCF7Dropdown']");
                if(oFormLegacyDropDownElements && oFormLegacyDropDownElements.length > 0) {
                    addressSelectDropdown = oFormLegacyDropDownElements[0];
                }
            }
        }
    }

    // If Form Valid and Has Elements - Try to Use It
    if(formObjectValid) {
        // If No 'ID' Matched - then try for field names
        if(addressCompany == null) {
            testAddressFields = oFormElements["Company"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressCompany = testAddressFields[0];
            }
        }
        if(addressLine1 == null) {
            testAddressFields = oFormElements["Street"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressLine1 = testAddressFields[0];
            }
            if(addressLine1 == null) {
                testAddressFields = oFormElements["Address1"];
                if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressLine1 = testAddressFields[0];
                }
            }
        }
        if(addressLine2 == null) {
            testAddressFields = oFormElements["Address2"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressLine2 = testAddressFields[0];
            }
        }
        if(addressLine3 == null) {
            testAddressFields = oFormElements["Address3"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressLine3 = testAddressFields[0];
            }
        }
        if(addressTown == null) {
            testAddressFields = oFormElements["City"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressTown = testAddressFields[0];
            }
            if(addressTown == null) {
                testAddressFields = oFormElements["Town"];
                if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressTown = testAddressFields[0];
                }
            }
        }

        if(addressCounty == null) {
            testAddressFields = oFormElements["County"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressCounty = testAddressFields[0];
            }
        }
        if(addressPostcode == null) {
            testAddressFields = oFormElements["Postcode"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressPostcode = testAddressFields[0];
            }
        }
        if(addressSelectDropdown == null) {
            testAddressFields = oFormElements["pc4uCF7Dropdown"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressSelectDropdown = testAddressFields[0];
            }
        }
         if(addressSummaryLine == null) {
            testAddressFields = oFormElements["pc4uCF7AddressLine"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressSummaryLine = testAddressFields[0];
            }
        }
        if(addressSummaryBlock == null) {
            testAddressFields = oFormElements["pc4uCF7AddressBlock"];
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addressSummaryBlock = testAddressFields[0];
            }
        }

        // Single 'Hidden Address' Area
        oNonFormAddressElements = oformObject.querySelectorAll("[id*='pc4uCF7AddressHidden']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressHiddenDiv = oNonFormAddressElements[0];
        }
    }

    // Check If No Form or No Address Found
    if(!formObjectValid ||
        (!(addressCompany)&& !(addressPostcode) &&!(addressLine1)&&!(addressLine2)&&!(addressLine3)&&!(addressTown)&&!(addressCounty))){

        // DECODE FIELDS FROM ENTIRE DOCUMENT
        // --------------------------------------------

        // - Use 'Legacy' Methods to ensure all browser compatability

        // Postcode Field
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCf7Postcode']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressPostcodeField = oFormLegacyDropDownElements[0];
        }

        // Company Field
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCf7Company']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addresssCompanyField = oNonFormAddressElements[0];
        }
        if(addresssCompanyField) {
            companyFieldFound = true ;
        } else {
            // ID Not Found - Try for Fields named 'Company
            testAddressFields = document.getElementsByName("Company");
            if(testAddressFields && testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                // Single Entry Found
                addresssCompanyField = testAddressFields[0];
                companyFieldFound = true ;
            }
        }

        // 'Town' / 'City' Field
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7City']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressTownField = oNonFormAddressElements[0];
        }
        if(addressTownField) {
            townFieldFound = true ;
        } else {
             // ID Not Found - Try for Fields named 'City' or 'Town'
            testAddressFields = document.getElementsByName("City");
            if(testAddressFields){
                if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressTownField = testAddressFields[0];
                    townFieldFound = true ;
                }
            }
            if(!townFieldFound) {
                testAddressFields = document.getElementsByName("Town");
                if(testAddressFields){
                    if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                        // Single Entry Found
                        addressTownField = testAddressFields[0];
                        townFieldFound = true ;
                    }
                }
            }
            if(testField.length == 1 && testField[0].type == "text") {
                // Single Entry Found
                addresssCompanyField = testField[0];
                townFieldFound = true ;
            }
        }

        // Address Line 1  - 'Street' or 'Address1'
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7Address1']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressLine1Field = oNonFormAddressElements[0];
        }
        if(addressLine1Field) {
            addressFieldFound = true ;
        } else {
             // ID Not Found - Try for Fields named 'Street' or 'Address1'
            testAddressFields = document.getElementsByName("Street");
            if(testAddressFields){
                if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressLine1Field = testAddressFields[0];
                    addressFieldFound = true ;
                }
            }
            if(!addressFieldFound) {
                testAddressFields = document.getElementsByName("Address1");
                if(testAddressFields){
                    if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                        // Single Entry Found
                        addressLine1Field = testAddressFields[0];
                        addressFieldFound = true ;
                    }
                }
            }
        }

        // Address Line 2  - 'Address2'
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7Address2']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressLine2Field = oNonFormAddressElements[0];
        }
        if(addressLine2Field) {
            addressFieldFound = true ;
        } else {
             // ID Not Found - Try for Fields named 'Address2'
            testAddressFields = document.getElementsByName("Address2");
            if(testAddressFields){
                if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressLine2Field = testAddressFields[0];
                    addressFieldFound = true ;
                }
            }
        }

        // Address Line 3  - 'Address3'
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7Address3']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressLine3Field = oNonFormAddressElements[0];
        }
        if(addressLine3Field) {
            addressFieldFound = true ;
        } else {
             // ID Not Found - Try for Fields named 'Address3'
            testAddressFields = document.getElementsByName("Address3");
            if(testAddressFields){
                if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressLine3Field = testAddressFields[0];
                    addressFieldFound = true ;
                }
            }
        }

        // Address 'County' Line  - 'County'
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7County']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressCountyField = oNonFormAddressElements[0];
        }
        if(addressCountyField) {
            addressFieldFound = true ;
        } else {
            // ID Not Found - Try for Fields named 'County'
            testAddressFields = document.getElementsByName("County");
            if(testAddressFields){
                if(testAddressFields.length === 1 && testAddressFields[0].type === "text") {
                    // Single Entry Found
                    addressCountyField = testAddressFields[0];
                    addressFieldFound = true ;
                }
            }
        }

        // Single 'Summary' Address Line
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7AddressLine']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressSummaryLine = oNonFormAddressElements[0];
        }
        // Single 'Summary' Address Block
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7AddressBlock']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressSummaryBlock = oNonFormAddressElements[0];
        }

        // Postcode AddressSelect Dropdown
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7Dropdown']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressSelectDropdown = oFormLegacyDropDownElements[0];
        }

         // Single 'Hidden Address' Area
        addressFieldFound = false ;
        oNonFormAddressElements = document.querySelectorAll("[id*='pc4uCF7AddressHidden']");
        if(oNonFormAddressElements && oNonFormAddressElements.length > 0) {
            addressHiddenDiv = oNonFormAddressElements[0];
        }
    }


    // Return Address Details as Control array
    return  {
        FormCompany: addressCompany,
        FormAddLine1: addressLine1,
        FormAddLine2: addressLine2,
        FormAddLine3: addressLine3,
        FormTown: addressTown,
        FormCounty: addressCounty,
        FormPostcode: addressPostcode,
        FormSelectAddress: addressSelectDropdown,

        FormAddressSummaryLine: addressSummaryLine,
        FormAddressSummaryBlock: addressSummaryBlock,
        FormAddressHiddenArea: addressHiddenDiv

    };
}


function legacyHtmlObjectSearch(searchObj, searchID){
    //Early return
    if( searchObj.id === searchID ){
      return searchObj;
    }
    var result, p;
    for (p in searchObj) {
        if( searchObj.hasOwnProperty(p) && typeof searchObj[p] === 'object' ) {
            result = legacyHtmlObjectSearch(searchObj[p], searchID);
            if(result){
                return result;
            }
        }
    }
    return result;

}
// --------------------------------------------------------------------------
// Function to SHOW PC4u Address Selection Dropdown
//  Added KRF 2020.04.30
function showPc4uAddressDropdown(dropdownObj){
    if(dropdownObj) {
        // Use JQuery Show if available - 2021.04.25
         try {
             jQuery(dropdownObj).show();

             // Check If Show Worked -
             if (jQuery(dropdownObj).css('display') === 'none') {
                 dropdownObj.style.display = '';
             } else {
                 dropdownObj.style.display = '';
             }
         }
         catch(err){
             dropdownObj.style.display = '';
         }

        // Check for Select2 Container - Needs Showing Too - KRF 2020.04.29
        try {
            var ddPrev = jQuery(dropdownObj).prev();
            if(ddPrev && ddPrev.hasClass('select2-container')){

                try {
                    ddPrev.show();
                }
                catch(err){
                    ddPrev.style.display = '';
                }
                ddPrev.css('display','block');
            }
        }
        catch(error) {
            // Do Nothing
        }
        // Fix for IOS - For CF7 Mostly
        setTimeout(function(){
            dropdownObj.focus();
            // lookupDropdown.options[0].selected = true;
        },500);
        //lookupDropdown.focus();
    }
}

// ------------------------------------------------------------------------
// Function to HIDE PC4u Address Selection Dropdown
//  Added KRF 2020.04.30
function hidePc4uAddressDropdown(dropdownObj, bClearDropdown){
     if(dropdownObj) {
         // Use JQuery Hide if available - 2021.04.25
         try {
            jQuery(dropdownObj).hide();
            if (jQuery(dropdownObj).css('display') !== 'none') {
                 dropdownObj.style.display = 'none';
            } else {
                dropdownObj.style.display = 'none';
            }
         }
         catch(err){
             dropdownObj.style.display = 'none';
         }

        // Check for Select2 Container - Needs Hiding Too - KRF 2020.04.29
        try {
            var ddPrev = jQuery(dropdownObj).prev();
            if(ddPrev && ddPrev.hasClass('select2-container')){
                try {
                    ddPrev.hide();
                }
                catch(err){
                     ddPrev.style.display = 'none';
                }
            }
        }
        catch(error) {
            // Do Nothing
        }

        // Clear Dropdown list if required
        if(bClearDropdown && bClearDropdown === true){
            dropdownObj.options.length = 0;
            try {
                jQuery(dropdownObj).empty();

                if(ddPrev && ddPrev.hasClass('select2-container')){
                    ddPrev.select2('val', '0');
                    ddPrev.select2({placeholder: "Select an address:",
                            allowClear: true });
                }
            }
            catch(error) {
                // Do Nothing
            }
        }
     }
}

// ------------------------------------------------------------------------
// Function to Show GravityForms 'Hidden Address Fields
//  Used to display Address when selected - or when Manual Address is required for a Hidden GravityForms Address
//  Added KRF 2021.07.29
function showPc4uGFHiddenAddress(gfPc4uAddressFieldID){
    
    var showHiddenGfAddress = document.getElementById(gfPc4uAddressFieldID+"_pc4uGfPostcodeLookupAddress");
    if(showHiddenGfAddress) {
        //
        showHiddenGfAddress.style.display = '';    
    }
    var showHiddenGfManualAddressButton = document.getElementById(gfPc4uAddressFieldID+"_pc4uGFManualAddress");
    if(showHiddenGfManualAddressButton) {        //
        showHiddenGfManualAddressButton.style.display = 'none';    
    }    
}