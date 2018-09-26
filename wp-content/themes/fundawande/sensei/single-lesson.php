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
        wp_redirect(get_post_permalink($lesson_quiz_ID));
    }
    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    FundaWande()->language->fw_correct_lesson_lang($context['user']->fw_current_course,$post->ID);




    //Get the unit info for the current lesson
    $unit = FundaWande()->lessons->get_unit_info($context['user']->fw_current_course, $post->ID );

    $context['unit'] = $unit;

    $context['num_lessons'] = count($unit->lessons);  

    //Get the module number for the parent module, to enable module-specific styling
    $context['module_number'] = get_term_meta($unit->parent, 'module_number', true);

    //Get the parent module title
    $context['module_title'] = get_term_meta($unit->parent, 'module_title', true );

    //Get the unit title
    $context['unit_title'] = get_term_meta($unit->term_id, 'module_title', true);

    //Get the nav links object and add to Timber context 
    $context['nav_links'] = FundaWande()->lessons->get_lesson_nav_links($post->ID);

    Timber::render(array('lms/single-lesson.twig', 'page.twig'), $context);

}
