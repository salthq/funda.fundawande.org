<?php
/**
 * The Template for displaying all Quizzes.
 *
 * Override this template by copying it to yourtheme/sensei/single-quiz.php
 *
 * @author 		Automattic
 * @package 	Sensei
 * @category    Templates
 * @version     1.9.0
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

    Timber::render(array('lms/single-quiz.twig', 'page.twig'), $context);

}
