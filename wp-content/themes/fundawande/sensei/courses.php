<?php
/**
 * Template Name: My Courses Page Template
 *
 * @author Pango

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

    $course_args = array(
        'post_type' => 'course',
    );

    $courses = Timber::get_posts($course_args);

    $context['courses'] = $courses;


    Timber::render(array('lms/template-courses.twig', 'page.twig'), $context);
}
