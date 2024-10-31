=== Postcodes4U Address Finder ===
Contributors: 3XSoftware
Tags: postcode, address, woocommerce, contactform7, gravityforms
Requires at least: 3.0.1
Tested up to: 6.6.1
Requires PHP: 5.6.4
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Requires WooCommerce at least: 2.2.3
Tested WooCommerce up to: 9.1.4
Tested ContactForm7 4.9.2 - 5.9.8
Tested Gravity Forms 2.4.15 - 2.8.15


**Postcodes4U Address Finder** lets you look up an address using a UK Postcode. 
Includes WooCommerce, Contact Form 7 and Gravity Forms Integration,   
plus our own custom Postcode Lookup Form.


== Description ==

**Postcodes4U Address Finder** Lets you look up an address using a UK Postcode. 

It allows Postcode Address Lookup to be added to your WooCommerce Checkout pages as well as Contact Form 7 and Gravity Form pages.

Easy integration into any other WordPress Page.

Also includes our own customisable Contact Form with Postcode lookup that can be added to any of your pages. 


= Please Note =

THIS POSTCODE LOOKUP IS A UK ONLY SERVICE SO WILL NOT WORK FOR ANY OTHER COUNTRY.

THIS PLUGIN REQUIRES YOU TO REGISTER AT: <http://www.postcodes4u.co.uk> 
WHERE YOU WILL RECEIVE 30 FREE CREDITS ON REGISTRATION

Additional Lookup Credits Can Be Purchased From as little as 1.4p per Postcode Lookup - going up to 5p per Postcode Lookup Depending on the volume of Lookups Purchased.

Credits are purchased using your account at <http://www.postcodes4u.co.uk>.

If you wish to see how the Postcodes4u Postcode Lookup works before creating a Postcodes4u account, then use our 'Test' Postcodes
AA901XX, AA902XX, AA903XX and AA901XX.    


= Technical features: =

* 100% user friendly, easy to install & remove
* Lightweight, clean code
* Works with WooCommerce 2.2.3 - 9.1.4
*            ContactForm 7 v4.0 - v5.9.8
*            GravityForms v2.3.1.12 - 2.8.15
  
   
== Installation ==

= To Install The Postcodes4u Plugin =

1. Install the plugin from your wordpress admin panel.

OR

1. Upload the Postcodes4U plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Once the plugin has been activated a new page 'Postcodes4u' is created. This page, which is visible to admin only, can be used to do local address lookups.

= Configure Postcodes4u =
The configuration settings for the Postcodes4u plugin can be accessed from the **Postcodes4U** page in the **Settings** menu.

Once Installed you will need to enter your Postcodes4U key and username which can be obtained from <http://www.postcodes4u.co.uk>.

When the key and user name has been entered, and your have turned 'on' Postcodes4u lookups (Postcodes4u Active) you can use the various integration options to add postcode lookups to your WordPress website. 

If you wish to see how the Postcodes4u Postcode Lookup works before creating a Postcodes4u account, then use our 'Test' Postcodes
AA901XX, AA902XX, AA903XX and AA901XX.   


= WooCoommerce Integration =

If you have WooCommerce installed and have have enabled **WooCommerce Integration** on the **Postcodes4U** page in the **Settings** menu your customers can use post code lookups in the checkout billing and shipping addresses to ensure accurate entry details.

There are additional options that allow you to control where the Postcodes4u postcode lookup will be displayed so it can better match you Theme. 

= Contact Form 7 Integration =
If you have Contact Form 7 installed and have enabled **Contact Form 7 Integration** on the **Postcodes4U** page in the **Settings** menu you can add postcode lookup to a Contact Form.

This is displayed in detail in the following document.
<http://www.postcodes4u.co.uk/support/Pc4u_CF7_SetupNotes.pdf>.

This is summarised below:

First add the Postcodes4u address to the form where the Postcode field and Address selction dropdown is to be displayed using the new 'field type/short code' 'pc4upostcode'.

**[pc4upostcode Postcode]**


The form fields fields that will contain the address details for the address lookup need to be set up with ID values 

For the Company Name the field ID is **pc4uCf7Company**

... for Address Line 1 the ID is **pc4uCF7Address1**

... for Address Line 2 the ID is **pc4uCF7Address2**

... for Address Line 3 the ID is **pc4uCF7Address3**

... for Town/City the ID is **pc4uCF7City**

... for County the ID is **pc4uCF7County**

**Please Note** You only need to include the fields you need for the form, eg for a short address form you could just include **Address Line1**, **Address Line2**, **Town/City** and **Postcode**

..You just need to ensure that the required address fields are also in the email template 

If you want to make the Postcode field 'required' simply place a **'*'** after the postcode field eg  **[pc4upostcode* Postcode]**

= GravityForms Integration =
If you have Gravity Forms installed and have enabled **Gravity Forms Integration** on the **Postcodes4U** page in the **Settings** menu you can add a Address Field with postcode lookup to a Gravity Form.

The plugin has replaced '**Address**' advanced field with '**Pc4u Address**'. All existing Address fields will work as before, but now have Postcodes4u Lookup options available, together with a UK address option.
The Postcodes4u lookup options are on the 'Appearance' tab of the Address field in the form. 
This includes controls to locally enable/disable the Postcode lookup on an individual address field, together with advanced positioning option, 

This is displayed in detail in the following document, and it is recommended that you read these notes.
<http://www.postcodes4u.co.uk/support/Pc4u_GravityForms_SetupNotes.pdf>.


= Postcodes4u Contact Form =

We have provided a Customisable Postcode Lookup Contact form that can be added by simply placing a short code '[pc4u_contact_form]' on the required page. 
Full details on the customisation settings for the contact form are described in 'Other Notes'.

To Add the Postcode Lookup Contact form use the following shortcode:

**[pc4u_contact_form]**

By default the short code will display the telephone and postal address fields but does not require them be be entered when the contact form is submitted.

To add a Contact Form that requires the telephone number and postal address to be present use the short code with attributes shown below:

**[pc4u_contact_form musthavetelephone="YES" musthaveaddress="YES]**
   

= Postcodes4u Contact Form Customisation =

A full list of the contact form attributes 

**contacttitle**  - Name of Contact Form - defaults to "Contact Us"

**subjecttitle**  - Contact Form Subject Title - defaults to "Subject"

**messagetitle**  - Contact Form Message Title - defaults to "Your Message"

**showtelephone** - Set to "TRUE" or "YES" to display telephone number input area, "FALSE" or "NO" to not display - Default is "YES"

**musthavetelephone** - Set to "TRUE" or "YES" if a telephone number must be included in the message,"FALSE" or "NO" to not display  -  Default is "NO"

**showaddress**   - Set to "TRUE" or "YES" to display postal address input area, "FALSE" or "NO" to not display - Default is "YES"

**musthaveaddress** - Set to "TRUE" or "YES" if a postal address must be included in the message,"FALSE" or "NO" to not display  -  Default is "NO"

	
A Contact Form Shortcode example that sets all of the parameters follows:

**[pc4u_contact_form contacttitle="Send Us A Message" subjecttitle="Message Subject" messagetitle="Message" showtelephone="YES" musthavetelephone="YES" showaddress="YES" musthaveaddress="YES"]**



== Frequently Asked Questions ==

= Do I need a Postcodes4U account to use this plugin? =
Yes, you will need a Postcodes4U account in order to obtain your key and username. This is entered in the Postcodes 'Settings' Page.

Once you have registered you will then receive 30 free credits. 

= How do I get free credits =
Register for an account at <http://www.postcodes4u.co.uk> for 30 free credits. 

= Purchasing Further Postcodes4u Credits =
Using your account on <http://www.postcodes4u.co.uk> you can purchase additional Postcodes4u credits.

= Do you offer address look-ups for other countries? =
Currently we only offer address look ups to the UK.

= Using Our 'Test' Postcodes =
If you wish to review, test or develop for our Postcodes4u system without using up your credits - we have created 4 'Test' postcodes, each with 5 addresses covering with a wide variety of address types which you can use free of charge.
AA901XX, AA902XX, AA903XX and AA901XX.    


= Enabling Postcodes4u in the Woo Commerce Checkout =
Ensure WooCommerce is installed and have set up the Postcodes4u key, Username, and Postcodes4u lookups are enabled (Postcodes4u Active) on the Postcodes4u settings page.

Then just select WooCommerce Integration 'Show the Postcodes4u Lookup in WooCommerce Checkout'.


= Adding Postcodes Lookups to Contact Form 7 =
Ensure WooCommerce is installed and have set up the Postcodes4u key, Username, and Postcodes4u lookups are enabled (Postcodes4u Active) on the Postcodes4u settings page.

To enable Contact Form 7 Integration select the **Show the Postcodes4u Lookup in Contact Form 7** option.

Details on adding Postcodes4u lookups to a Contact 7 Form can be found in the **Installation** tab or you can download a simple setup guide here <http://postcodes4u.co.uk/support/Pc4u_CF7_SetupNotes.pdf>

= Adding Postcodes Lookups to a GravityForms Form  =
Ensure GravityForms is installed and have set up the Postcodes4u key, Username, and Postcodes4u lookups are enabled (Postcodes4u Active) on the Postcodes4u settings page.

To enable GravityForms Integration select the **Enable Gravity Forms Integration** option.

Details on adding Postcodes4u lookups to a GravityForms Form can be found in the **Installation** tab or you can download a setup guide here <http://postcodes4u.co.uk/support/Pc4u_GravityForms_SetupNotes.pdf>

= Woo Commerce Compatibility =
Works,and tested with, WooCommerce 2.2.3 - 9.1.4 (The Current Version)

= Contact Form 7 Compatibility =
Works,and tested with, Contact Form 7 4.0 - 5.9.8 (The Current Version)

= Gravity Form Compatibility =
Works,and tested with, Gravity Forms 2.4.15 - 2.8.15 (The Current Version)

= SUPPORT ISSUES =

If you have questions or issues with the WordPress Postcodes4u Plugin then visit the support forum
 <https://wordpress.org/support/plugin/postcodes4u-address-finder> and enter your query.
Or you can email us at <support@postcodes4u.co.uk>



== Screenshots ==

1. Simple interface to add your Postcodes4U details

2. Works with ContactForm7 and GravityForms

3. Works with WooCommerce Checkout and Account Pages

4. Easy setup for postcode Lookup in ContactForm7

5. Easy setup for postcode Lookup in GravityForms

6. Compatible with GravityForms 2.5.x - 2.8.15

7. Includes Our own Contact form with Postcodes4U Postcode Lookup

8. In all cases, like WooCommerce, use our Simple address look up for for Postcodes4U 

9. With Simple address selection


== Changelog ==
= Version 1.5.34 =
Checked Compatibility with Wordpress 6.6.1  WooCommerce 9.1.4
Contact Form7 5.9.8   and GravityForms 2.8.15

= Version 1.5.33 =
Checked Compatibility with Wordpress 6.5.3  WooCommerce 8.9.2
Contact Form7 5.9.5   and GravityForms 2.8.12

= Version 1.5.32 =
Checked Compatibility with Wordpress 6.4.2  WooCommerce 8.4.0
Contact Form7 5.8.4   and GravityForms 2.8.0

* Note For ContactForm7 * forms with multiple addresses. Version 5.8 has enforced the requirement of unique form field IDs
which may cause an existing forms 2nd or subsequent address lookup to fail.
Updated notes about this can be found in the updated Postcodes4u ContactForm7 Setup guide:
   <http://www.postcodes4u.co.uk/support/Pc4u_CF7_SetupNotes.pdf>.

= Version 1.5.31 =
Checked Compatibility with Wordpress 6.3 WooCommerce 8.0.1
Contact Form7 5.7.8   and GravityForms 2.7.12

= Version 1.5.30 =
Checked Compatibility with Wordpress 6.2.2 WooCommerce 7.8.0
Contact Form7 5.7.7   and GravityForms 2.7.8

= Version 1.5.29 =
Checked Compatibility with Wordpress 6.2 WooCommerce 7.5.1
Contact Form7 5.7.5.1 and GravityForms 2.7.3

= Version 1.5.28 =
GravityForms 'Aria' processing has been finally fixed - the issue was related to Pre 2.5 versions of Gravity forms.
   I have tested back to v2.4.15.  If you need a version that works with an earlier version of Gravity Forms. please contact us.
   Postcodes4u Lookup now operates in the form editor in the newer versions of GravityForms.
	
Checked Compatibility with Wordpress 6.1.1, WooCommerce 7.2.2
Contact Form7 5.7.2 and GravityForms 2.6.8.3

= Version 1.5.27 =
GravityForms 'Aria' Code Restored - Removal messed up a large number of users.
.. Instead a flag has been added to supress ARIA calls for the very few users that are affected by this issue. 
A minor display positioning fix has also been added to the Postcodes4u GravityForms addressform to ensure all the fields line up.
	
Checked Compatibility with Wordpress 6.0.0, WooCommerce 6.6.1
Contact Form7 5.6 and GravityForms 2.6.3.4

= Version 1.5.26 =
Gravity 'Aria' Code Removed - Generates errors for some users

Checked Compatibility with Wordpress 5.9.0, WooCommerce 6.2.0
Contact Form7 5.5.5 and GravityForms 2.5.16.3

= Version 1.5.25 =
Gravity Forms Updates 
  Fixed Not displayed Country Stopping Address Field Validation
  Fixed Postcode Lookup Label Field Stopping Lookup Button working on some themes 
  Updates for very rare Aria Attribute Issue
CF7 Update
  Fixed validation issue with Contact Form 7 'default:get' on Postcode field

Checked Compatibility with Wordpress 5.8.1, WooCommerce 5.7.1,
Contact Form7 5.4.2 and GravityForms 2.5.11

= Version 1.5.24 =
Gravity Forms Updates
    Updated to increase compatibility with GravityForms 2.5.x
        inc formatting changed for hidden/pop up GF Address Form
    Enhancements for GravityForms & Elementor on Mobile devices.
    Enabled Postcode Lookup on Gravity Form Editor
    Postcodes4u Control Styling added to styles file
        (/inclues/css/pc4u_styles_v1-0.css)
    
Added new option to allow Lookup Warning messages (eg. No Postcode Specified etc)
Added Ability to Customise Lookup Button Text (Default 'Lookup')

Checked Compatibility with Wordpress 5.8, WooCommerce 5.5.2,
Contact Form7 5.4.2 and GravityForms 2.5.8

= Version 1.5.23 =
Fix GravityForms 2.5 Forms editor Incompatibility Issues
  Checked Compatibility with Wordpress 5.7.2, WooCommerce 5.3.0,
	Contact Form7 5.4.1 and GravityForms 2.5.1.1

= Version 1.5.22 =
Fix CF7 Styling for Disabled Submit Buttons - Esp when using 'Acceptance' Checkbox.
Further jQuery 'Show' and 'Hide' issues for rare cases in Javascript.
  Checked Compatibility with Wordpress 5.7.1 and WooCommerce 5.2.2

= Version 1.5.21 =
Improved WooCommerce Postcodes4u Form Layout, added support for WooCommerce 'My Account' Address Form editing.
  Checked Compatibility with Wordpress 5.7, WooCommerce 5.1, ContactForm7 5.4 and GravityForms 2.4.23

= Version 1.5.20a =
Stop Contact Form7 Submit Wait Windows

= Version 1.5.20 =
Added support for Contact Form 7 'Conditional Fields' - Repeating Fields 

= Version 1.5.19 =
Added support for Contact Form 7 'default:get' on Postcode field
 Checked Compatibility with Wordpress 5.6, WooCommerce 4.8.0, ContactForm7 5.3.1 and GravityForms 2.4.20

= Version 1.5.18 =
Fixed issue with WooCommerce Default Billing Address Issue
 Checked Compatibility with Wordpress 5.5.3, WooCommerce 4.7.0, ContactForm7 5.3 and GravityForms 2.4.18

= Version 1.5.17 =
Fixed issue with Address DropDown appearing on certain Gravity Form/Theme combinations
using the JQuery 'Select2' Selection controls. Added 'Blank' County to GravityForms.
 Checked Compatibility with Wordpress 5.4.1, WooCommerce 4.2.0, ContactForm7 6.1.9 and GravityForms 2.4.18

= Version 1.5.16 =
Fixed issue with YITH WooCommerce Checkout Manager
  Checked Compatibility with WooCommerce 3.9.1

= Version 1.5.15 =
Fixed issue with CF7 and Material Design - Especially Concerning CF7 Multiple Address Forms
  Checked Compatibility with WordPress 5.3.2, WooCommerce 3.9.0, Gravity Forms 2.4.16, Contact Form7 5.1.6

= Version 1.5.14 =
New 'No County' address option
Fixed issue with CF7 Single line and Block Address - Missing Address Fields
Fixed issue with spaces after Username and Key (Values Now 'Trimmed')
  Checked Compatibility with WordPress 5.2.3, WooCommerce 3.7.0, Gravity Forms 2.4.10, Contact Form7 5.1.4

= Version 1.5.13 =
Fixed issue with Pc4u Contact Form
  Checked Compatibility with WooCommerce 3.5.6.

= Version 1.5.12 =
Fixed issue with WooCommerce - Postcode Field Placement on Checkout Billing Address.
  Now also working on Checkout Shipping address on WooCommerce 3.5.4 or greater.

= Version 1.5.11 =
Fixed issue with WooCommerce - Postcode Field Placement on Checkout Billing Address.
.. Currently not working on Shipping address on WooCommerce 3.5.4 or greater.
* Checked Compatibility with Wordpress 5.1 and WooCommerce 3.5.5

= Version 1.5.10 =
Fixed issue with Gravity Forms esp when using Conditional Logic
* Checked Compatibility with Gravity Forms 2.4.6

= Version 1.5.9 =
* Introduced Test Postcodes 'AA901XX, AA902XX, AA903XX and AA901XX - The are for testing, they are free, and do not need a Postcodes4u username or key.
* Checked Compatibility WordPress 5.0.3, WooCommerce 3.5.4, Contact Form7 5.1.1 and Gravity Forms 2.4.5 
*
=   Contact Form 7 Enhancements =
*   New Contact Form fields added to allow an address to be displayed in a single field line or address block. See ContactForm7 setup guide for further details
*      <http://www.postcodes4u.co.uk/support/Pc4u_CF7_SetupNotes.pdf>.
*   When an Addresses is selected, any empty form address fields are set to empty to ensure old or invalid information and the placeholder is cleared. 

=   Gravity Form Enhancements =
*   When an Addresses is selected, any empty form address fields are set 'empty' to ensure that the Address 'Required' test is passed.
*   If a Gravity Form address lookup fails - and the Form is set to Hide the address - the address is displayed to allow manual entry.
 
= Version 1.5.8 =
* Fixed missing 'Placeholder' option in ContactForm7 Postcode field.
* Checked Compatibility WordPress 5.0.3, WooCommerce 3.5.2, Contact Form7 5.1.1 and Gravity Forms 2.4.5
* (Note Internal Development Version)

= Version 1.5.7 =
* Checked Compatibility WordPress 5.0.1 and Gravity Forms 2.4.3

= Version 1.5.6 =
* Fixed 'Lookup' Button Display issue on Gravity Forms
* Checked Compatibility with Contact Form7 5.1.0

= Version 1.5.5 =
* Fixed issue with WooCommerce Shipping Selection by Postcode Issue
* Fixed 'Lookup' Button Display on a number of themes
* Fixed 'Raw' Address display top ensure the 'County Display' setting is used if required.
* Checked Compatibility with WordPress 5.0, WooCommerce 3.5.2, Contact Form7 5.0.5 & Gravity Forms 2.4.1

= Version 1.5.4 =
* Checked Compatibility with WordPress 4.9.8, WooCommerce 3.4.5, Contact Form7 5.0.4 & Gravity Forms 2.3.3.6
* Fix Multi Page and County Validation Issue with Gravity Forms.
* Improved Compatibility with Older Browsers
* General Stability Improvements

= Version 1.5.3 = 
* Fixed a few issues with the AdHoc Form processing
* (Note Internal Development Version)

= Version 1.5.2 =
* Fixed Multiple Contact Form 7 Issue with Older Browsers
* Added new 'AdHoc' integration code to make it easier to add Postcode lookup to any wordpress form.

= Version 1.5.1 =
* Fixed GravityForms Lookup issue - Do not hide address if data present

= Version 1.5.0 =
* Add Support Gravity Forms
* Checked Compatibility with WordPress 4.9.6, WooCommerce 3.4.2 & Contact Form7 5.0.2


= Version 1.4.2 =
* Add Support for Multiple Contact Form 7 Postcode Lookup Forms on a page
* Checked Compatibility with WordPress 4.9.5, WooCommerce 3.3.4 & Contact Form7 5.0.1

= Version 1.4.1 =
* Fix Contact Form 7 Postcode 'required field' issue
* Tidy up postcode lookup error messages.
* Checked Compatibility with WordPress 4.9.4 & WooCommerce 3.3.1

= Version 1.4.0 =
* Contact Form 7 Integration Added
* Additional WooCommerce Address Positioning options added.
* More control of Address County Display
* Ability to display Addresses with more address lines
* Tidied up Admin page.

= Version 1.3.2 =
* Updated for WooCommerce 3.2.6
* Checked Compatibility with WordPress 4.9.1

= Version 1.3.1a =
* Fixed an issue with custom Postcode Lookup Forms

= Version 1.3.1 =
* Fixed an issue with WooCommerce Checkout Display

= Version 1.3.0 =
* Improvements to WooCommerce Address Display - Can Specify Alternate Positions for Postcode Lookup form fields.
* Option to override WooCommerce Checkout Templates to ensure Postcodes4u Lookup is enabled if required. (But may loose any custom Theme Styling!).
* Restored general Postcode Lookup Enable/Disable control.
* Tidied up Admin page.

= Version 1.2.5 =
* Improvements to Alternative Address Display Option - Stops Company, Town, County or Postcode appearing in Address lines 1 & 2 if present elsewhere.
 
= Version 1.2.4 =
* Added an Alternative Address Display Option - Stops Company and Town appearing in Address lines 1 & 2. 
* Fixed an error with Woocommerce Shopping Address form - (Removed 2nd Postcode Field). 
* Checked Compatibility with WordPress 4.6.1 and WooCommerce 2.6.8

= Version 1.2.3 =
* Styling Enhancements and Theme integration improvements. 
* Checked Compatibility with WordPress 4.6 and WooCommerce 2.6.4

= Version 1.2.2 =
* Fix Issues with HTTPS Postcode Lookups. 
* Checked Compatibility with WordPress4.4 and WooCommerce 2.4

= Version 1.2.1 =
* Admin Page Update - Allow HTTPS in WooCommerce.

= Version 1.2 =
* Added Shortcode customisable Contact Form with Postcode Lookup.

= Version 1.1 =
* Added WooCommerce Checkout Billing and Shipping Postcode Lookup Integration.

= Version 1.0 =
* Original 'Blog' form version.



== Upgrade Notice ==
= Version 1.4.0 =
* Major Changes to Plugin JavaScript to file renamed.
*  If you have any issues upgrading the plugin then it is recommended that the old plugin is deactivated and deleted and the new plugin version installed.


== Support ==

If you have questions or issues with the WordPress Postcodes4u Plugin then visit the support forum
 <https://wordpress.org/support/plugin/postcodes4u-address-finder> and enter your query.
Or you can email us at <support@postcodes4u.co.uk>






