<?php
/**
 * The Template for displaying all single courses pages to show the modules
 *
 *
 * @author 		Pango
 * @package 	FundaWande
 * @version     1.0.0
 */

// if user is not logged in direct to the custom FW login page
if (!is_user_logged_in()) {
    wp_redirect(get_site_url(null, '/login'));
    exit();
}

if (class_exists('Timber')) {

    $context = Timber::get_context();
    $post = new TimberPost();

    $context['post'] = $post;

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    // Check correct course language
    FundaWande()->language->fw_correct_course_lang($context['user']->fw_current_course,$post->ID);

    // Get the course modules to visualise on the course page
    $context['modules'] = FundaWande()->modules->get_course_modules($post->ID);

    //TODO: replace this dummy unit array with a function that pulls in all the child modules of the current module
    $total_units = array('Unit 1', 'Unit 2', 'Unit 3', 'Unit 4');
    //count child modules (units) and add to context
    $context['total_units'] = count($total_units);

    //TODO: replace this dummy unit array with a function that pulls in the completed child modules of the current module
    $completed_units = array('Unit 1', 'Unit 2', 'Unit 3');
    //count completed child modules (units) and add to context
    $context['completed_units'] = count($completed_units);


    Timber::render(array('lms/single-course.twig', 'page.twig'), $context);
}
