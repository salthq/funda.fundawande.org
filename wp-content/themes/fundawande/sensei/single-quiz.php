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

    // run this is make sure the quiz progress is updated before it loads
    do_action( 'sensei_single_quiz_content_inside_before', get_the_ID() );
    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    // Get the quiz lesson ID
    $lesson_id = $post->_quiz_lesson;
    $lesson = new TimberPost($lesson_id);
    $context['quiz_lesson'] =  $lesson;



    FundaWande()->language->fw_correct_lesson_lang($context['user']->fw_current_course, $lesson->ID);

    // check if is retry quiz
    $context['quiz_retry'] = FundaWande()->quiz->fw_is_quiz_retry($lesson_id);


    //Get the unit info for the current lesson
    $sub_unit_meta = FundaWande()->lessons->fw_get_sub_unit_meta($context['user']->fw_current_course, $lesson_id );

    $context['sub_unit_meta'] = $sub_unit_meta;
    $context['module_number'] = $sub_unit_meta->module_number;


    Timber::render(array('lms/single-quiz.twig', 'page.twig'), $context);

}
