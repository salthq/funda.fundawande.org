<?php
/**
 * The Template for displaying all single courses.
 *
 * Override this template by copying it to yourtheme/sensei/single-course.php
 *
 * @author 		Automattic
 * @package 	Sensei
 * @category    Templates
 * @version     1.9.0
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

    // Get the course modules to visualise on the course page
    $context['modules'] = FundaWande()->modules->get_course_modules($post->ID);


    Timber::render(array('lms/single-course.twig', 'page.twig'), $context);
}
