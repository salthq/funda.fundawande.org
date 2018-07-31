<?php
/*
 * Plugin Name: Funda Wande
 * Version: 1.0.00
 * Plugin URI: https://fundawande.rog
 * Description: Funda Wande plugin by Pango
 * Author: Pango
 * Author URI: http://pango.studio
 * Requires at least: 3.5
 * Tested up to: 4.4
 * @package WordPress
 * @author Pango
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Returns the global Funda Wande Instance.
 *
 * @since 1.8.0
 */
function FundaWande() {
    return FundaWande_Main::instance( array( 'version' => '3.2.00' ) );
}

// Get active theme
$theme = wp_get_theme();

// If the active theme is Funda Wande then initalise the plugin
if ($theme->slug = 'fundawande') {

    function init_autoloader() {
        require_once( 'includes/class-fundawande-autoloader.php' );
        new FundaWande_Autoloader();
    }

    // Load auto loader to add include all includes class files
    init_autoloader();

    // Run global Funda Wande instance
    FundaWande();
}

