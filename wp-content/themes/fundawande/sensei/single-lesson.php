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
    $sub_unit_meta = FundaWande()->lessons->fw_get_sub_unit_meta($context['user']->fw_current_course, $post->ID );

    $context['sub_unit_meta'] = $sub_unit_meta;
    $context['module_number'] = $sub_unit_meta->module_number;



    Timber::render(array('lms/single-lesson.twig', 'page.twig'), $context);

}
