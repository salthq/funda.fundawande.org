<?php
/**
 *Template Name: Contacts Page Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    //Check if a user has a coach assigned
    $coach_id = get_user_meta($user->ID, 'fw_coach', true);

    //If a coach has been assigned, add the info to Timber context so the coach card can be populated
    if($coach_id) {
        $coach_info = get_userdata($coach_id);
        $context['coach_name'] = $coach_info->display_name;
        $context['coach_email'] = $coach_info->user_email;
    }


    Timber::render(array('template-contacts.twig', 'page.twig'), $context);
}