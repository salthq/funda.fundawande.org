<?php
/**
 * Template Name: Legal Page Template
 *
 * @package Pango
 * 
 * This template is used for any legal documentation such as the terms and conditions and privacy policy.
 */

if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    Timber::render(array('template-legal.twig', 'page.twig'), $context);

}