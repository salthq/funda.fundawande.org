<?php
/**
 *Template Name: Insights Page Template
 *
 * @package Pango
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit();
}

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    Timber::render(array('template-insights.twig', 'page.twig'), $context);
}