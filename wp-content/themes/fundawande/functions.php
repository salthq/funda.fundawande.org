<?php
/**
 * Pango Boilerplate functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package boilerplate
 */


// Set up constant variable to control JS & CSS versioning
// This version number must be changed whenever pushing to stable branch
const FW_VER = '1.2.3'; // Released on 19/06/2019 by Jason Tame


/**
 * Enqueue scripts and styles.
 */
function pango_scripts() {

    // Include all vender assets
	wp_enqueue_style( 'vendors-style',  get_template_directory_uri().'/css/vendors-styles.min.css' ,array(),FW_VER );
	wp_enqueue_script( 'vendors-script', get_template_directory_uri().'/js/vendors-js.min.js' ,array('jquery'),FW_VER,true);

	// Include global assets
    wp_enqueue_style( 'theme-style',  get_template_directory_uri().'/css/theme-styles.min.css' ,array(),FW_VER );
    wp_enqueue_script( 'theme-script', get_template_directory_uri().'/js/theme-js.min.js' ,array('jquery'),FW_VER,true);

    if (( is_page_template('login.php')  ) || ( is_page_template('register.php')  ) ) {
        wp_enqueue_script('login-script', get_template_directory_uri(). '/js/login.min.js', array('jquery'), FW_VER, true);
    }
}
add_action( 'wp_enqueue_scripts', 'pango_scripts' );

/*
* Add function to only allow Gutenberg on Blog posts and Course post types
*/
add_filter('use_block_editor_for_post', 'global_disable_gutenberg', 5, 2);

function global_disable_gutenberg($current_status, $post)
{
    // Disable gutenberg for every post type except posts and courses
    if (($post->post_type !== 'post')) {
        return false;
    }
    return $current_status;
}


/**
 * Enqueue admin scripts and styles.
 */
function pango_admin_scripts() {
    wp_enqueue_style( 'theme-admin-style',  get_template_directory_uri().'/css/admin-styles.min.css' ,array(),FW_VER );

}
add_action( 'admin_enqueue_scripts', 'pango_admin_scripts' );

/**
 * Enqueue login styles
 */
function fw_login_stylesheet() {
    wp_enqueue_style( 'custom-login-css', get_stylesheet_directory_uri() . '/css/custom-login.min.css' ,'', FW_VER);

}
add_action( 'login_enqueue_scripts', 'fw_login_stylesheet' );


/**
 * Load Timber compatibility file.
 */
require get_template_directory() . '/includes/functions-timber.php';


// Ideally this function should be in class-fundawande-admin.php, but it doesn't trigger when added there. 
// TODO: Find a way to add this function to the FW plugin
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_super_admin() && !current_user_can('editor') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php') {
        add_filter('show_admin_bar', '__return_false');
    }
}

add_action('init', 'remove_admin_bar');

