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
//    $context['lessons'] = Sensei()->course->course_lessons(31);



    //Get the unit info for the current lesson
    $unit = Sensei()->modules->get_lesson_module( $post->ID );

    //Add unit object to Timber context
    $context['current_unit'] = $unit;

    //Get the module number for the parent module, to enable module-specific styling
    $context['module_number'] = get_term_meta($unit->parent, 'module_number', true);

    //Get the name of the current unit
    $context['current_unit_name'] = $unit->name;

    //TODO: Move the code below to a new FW plugin class called class-fundawande-lessons.php 

    // Get previous and next lesson URLs
    $nav_links = sensei_get_prev_next_lessons( $post->ID );
    $context['nav_links'] = $nav_links;
    if ( isset( $nav_links['previous']) || isset( $nav_links['previous'])) {
        //If a previous lesson exists, add the previous lesson URL to context
        if(isset( $nav_links['previous'])) {
            //Only add the previous lesson URL to Timber context if it's a lesson URL
            if(strpos($nav_links['previous']['url'], '/lesson/')) {
                $context['prev_url'] = $nav_links['previous']['url'];
            }
        }
        //If a next lesson exists, add the next URL to context
        if(isset( $nav_links['next'])) {
            //Add the next URL to context if it's a lesson URL
            if(strpos($nav_links['next']['url'], '/lesson/')) {
                $context['next_url'] = $nav_links['next']['url'];
            } 
            /*
             * If the next URL is not a lesson URL, then its a unit URL. In that case, add 'unit_completed'
             * to Timber context so a complete unit button can be added to the template 
             */
            else {
                $context['unit_completed'] = true;
                $context['next_unit_link'] = $nav_links['next']['url'];
                $context['next_unit_title'] = $nav_links['next']['name'];
            }
        }
    }

    Timber::render(array('lms/single-lesson.twig', 'page.twig'), $context);

}
