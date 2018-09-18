<?php
/**
*Template Name: Home Page Template
 *
 * @package Pango
 */

// if user is not logged in direct to the custom FW login page
if (!is_user_logged_in()) {
    wp_redirect(get_site_url(null, '/login'));
    exit();
}

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    Timber::render(array('template-home.twig', 'page.twig'), $context);

}