<?php
/**
 * Pango Boilerplate functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package boilerplate
 */

/**
 * Enqueue scripts and styles.
 */
function pango_scripts() {

    // Include all vender assets
	wp_enqueue_style( 'vendors-style',  get_template_directory_uri().'/css/vendors-styles.min.css' ,array(),'1' );
	wp_enqueue_script( 'vendors-script', get_template_directory_uri().'/js/vendors-js.min.js' ,array('jquery'),'1',true);

	// Include global assets
    wp_enqueue_style( 'theme-style',  get_template_directory_uri().'/css/theme-styles.min.css' ,array(),'1' );
    wp_enqueue_script( 'theme-script', get_template_directory_uri().'/js/theme-js.min.js' ,array('jquery'),'1',true);

}
add_action( 'wp_enqueue_scripts', 'pango_scripts' );


/**
 * Load Timber compatibility file.
 */
require get_template_directory() . '/includes/functions-timber.php';


/*
 * Set up the function to display the login form, so it can be added to the login page template.
 */
function get_login_form() {
    FundaWande()->login->setup_login_form();
}
add_action('show_login_form', 'get_login_form');