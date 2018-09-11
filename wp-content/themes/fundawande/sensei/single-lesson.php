<?php
ob_start();
/**
 * The Template for displaying all single lessons.
 *
 * Override this template by copying it to yourtheme/sensei/single-lesson.php
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

    // Check if lesson has quiz questions
    $lesson_quiz = get_post_meta($post->ID, '_quiz_has_questions', true);

    // If there are questions in lesson redirect to the quiz page
    if (0 < $lesson_quiz) {
        $lesson_quiz_ID = get_post_meta($post->ID, '_lesson_quiz', true);
        header("Location: ".get_post_permalink($lesson_quiz_ID));
        die();
    }

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    //Get the unit info for the current lesson
    $unit = Sensei()->modules->get_lesson_module( $post->ID );

    //Get the module number for the parent module, to enable module-specific styling
    $context['module_number'] = get_term_meta($unit->parent, 'module_number', true);

    //Get the name of the current unit
    $context['unit_name'] = $unit->name;

    // Get previous and next lesson URLs
    $nav_links = sensei_get_prev_next_lessons( $post->ID );
    if ( isset( $nav_links['previous']) || isset( $nav_links['previous'])) {
        //If a previous lesson exists, add the previous lesson URL to context
        if(isset( $nav_links['previous'])) {
            $context['prev_url'] = $nav_links['previous']['url'];
        }
        //If a next lesson exists, add the next lesson URL to context
        if(isset( $nav_links['next'])) {
            $context['next_url'] = $nav_links['next']['url'];
        }
    }

    Timber::render(array('lms/single-lesson.twig', 'page.twig'), $context);

}
