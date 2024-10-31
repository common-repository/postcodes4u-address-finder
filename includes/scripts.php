<?php

/***************************************************************************
* Postcodes4u script control
* ========================== 
* Updated KRF 2021.07.30 
*             - Include Pc4u Script & Styles in Gravity Forms Page Editor
*  
* 
* Author: Kevin Fisher - 3X Software
* Plugin URI: plugins.3xsoftware.co.uk/wordpress-uk-address-finder
*
****************************************************************************/


// Register All Postcodes4u Scripts and CSS Styles
add_action( 'wp_loaded', 'register_all_pc4u_scripts' );
function register_all_pc4u_scripts() 
{
    wp_register_script('pc4u-script', plugin_dir_url( __FILE__ ) . 'js/pc4u_wp_v1_5_19' . '.js');
    wp_register_style('pc4u-style', plugin_dir_url( __FILE__ ) . 'css/pc4u_styles_v1-1' . '.css');
}

// Ensure All Postcodes4u Scripts and CSS Styles loaded to normal (Not Admin) Wordpress pages
add_action('wp_enqueue_scripts', 'pc4u_load_scripts');
function pc4u_load_scripts ()
{
    wp_enqueue_script('pc4u-script');  // .JS
    wp_enqueue_style('pc4u-style');    // .css	
}

// Ensure All Postcodes4u Scripts and CSS Styles loaded to Gravity Forms Admin/Editor pages
add_action('admin_enqueue_scripts', 'pc4u_load_admin_scripts');
function pc4u_load_admin_scripts ()
{
    if ( class_exists( 'RGForms' ) ) {
        
        if ( RGForms::is_gravity_page() ) {
            wp_enqueue_script( 'pc4u-script');
            wp_enqueue_style('pc4u-style');    // .css	
        }
    }
}

//  AddPostcodes4u Scripts to Gravity Forms Admin 'Safe Scripts'
add_filter('gform_noconflict_scripts', 'register_safe_script' );
function register_safe_script( $scripts ){
    //registering my script with Gravity Forms so that it gets enqueued when running on no-conflict mode
    $scripts[] = "pc4u-script";
    return $scripts;
}