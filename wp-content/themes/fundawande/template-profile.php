<?php
/**
*Template Name: Profile Page Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    if (!(is_super_admin() || current_user_can( 'coach' ))) {
        wp_redirect('/');
        exit();
    }
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    Timber::render(array('template-profile.twig', 'page.twig'), $context);

}