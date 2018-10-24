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
    $user = new TimberUser();


    // If the Ts and Cs are signed and if the user is assigned to the course, redirect to the course page.
    if(get_user_meta($user->id, 'legal', true) == 'agreed') {
        if(get_user_meta($user->id, 'fw_current_course', true) != "") {
            $current_course_id = FundaWande()->lms->fw_get_current_course_id($user->ID);
            error_log('id:'.$current_course_id );
            wp_redirect(get_permalink($current_course_id));
        }
    }
    


    Timber::render(array('template-home.twig', 'page.twig'), $context);

}