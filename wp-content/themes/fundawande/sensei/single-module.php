<?php
/**
 * The Template for displaying all units within a module
 *
 *
 * @author 		Pango
 * @package 	FundaWande
 * @version     1.0.0
 */

// if user is not logged in direct to login page
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit();
}

if (class_exists('Timber')) {

    $context = Timber::get_context();
    $post = new TimberPost();

    $context['post'] = $post;

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    // Get the units to visualise on the module page
    $context['units'] = FundaWande()->modules->get_module_units($post->$id, wp_get_post_parent_id($post->$id) );


    Timber::render(array('lms/single-module.twig', 'page.twig'), $context);
}
