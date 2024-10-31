<?php
/*
Plugin Name: Postcodes4U
Plugin URI: http://plugins.3xsoftware.co.uk
Description: Postcodes4u UK Address Postcode Lookup
Version: 1.5.34
Requires at least: 3.0
Requires PHP: 5.6.4
Author: 3X Software Ltd
Author URI: http://3xsoftware.co.uk
License:    GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/***************************
* GLOBAL VARIABLES
***************************/

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'postcodes4u_install');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'postcodes4u_remove' );


/***************************
* includes

***************************/

include ('includes/data-processing.php');
include ('includes/scripts.php');
include ('includes/display-functions.php');
include ('includes/admin-page.php');
include ('includes/cf7_integration.php');
include ('includes/gf_integration.php');
include ('includes/woo_integration.php');




// Activate Postcode Plugin Form
add_shortcode( 'pc4u_contact_form', 'pc4u_shortcode' );

$pc4u_plugin_name = 'Postcodes4U';

// retrieves the plugin settings from the options table
$pc4u_options = get_option ('pc4u_settings');


/*****************************************
* INSTALLATION PROCESSES
*****************************************/
function postcodes4u_install() {

    global $wpdb;

    $the_page_title = 'Postcodes4U';
    $the_page_name = 'Postcodes4U';

    // the menu entry...
    delete_option("postcodes4u_page_title");
    add_option("postcodes4u_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("postcodes4u_page_name");
    add_option("postcodes4u_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("postcodes4u_page_id");
    add_option("postcodes4u_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "Postcodes4u Lookup.";
        $_p['post_status'] = 'private';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncategorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'private';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'postcodes4u_page_id' );
    add_option( 'postcodes4u_page_id', $the_page_id );
}

function postcodes4u_remove() {

    global $wpdb;

    $the_page_title = get_option( "postcodes4u_page_title" );
    $the_page_name = get_option( "postcodes4u_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'postcodes4u_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("postcodes4u_page_title");
    delete_option("postcodes4u_page_name");
    delete_option("postcodes4u_page_id");

    // Remove Any Settings Value
    unregister_setting('pc4u_settings_group', 'pc4u_settings');

    // Restore Gravity Forms File ?

}
/*************************
*end extra
*************************/




// Add Local Plugin Templates to Woocommerce Search Path
// =====================================================


function pc4uplugin_plugin_path() {

  // gets the absolute path to this plugin directory

  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}


?>