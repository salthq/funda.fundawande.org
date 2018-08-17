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
 * Remember user language choice
 */
add_action('init', 'set_user_language_preference');
function set_user_language_preference() {
    if ( is_user_logged_in() && isset($_GET['lang'])) {
        $user_id = get_current_user_id();
        $lang = $_GET['lang'];
        update_user_meta($user_id, 'language_preference', $lang );
    }
}
