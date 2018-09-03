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
 * Enqueue admin scripts and styles.
 */
function pango_admin_scripts() {
    wp_enqueue_style( 'theme-admin-style',  get_template_directory_uri().'/css/admin-styles.min.css' ,array(),'1' );

}
add_action( 'admin_enqueue_scripts', 'pango_admin_scripts' );


/**
 * Load Timber compatibility file.
 */
require get_template_directory() . '/includes/functions-timber.php';
