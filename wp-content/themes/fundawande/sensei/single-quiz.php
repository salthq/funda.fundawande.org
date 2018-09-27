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
    do_action( 'sensei_single_quiz_content_inside_before', get_the_ID() );
    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    // Get the quiz lesson ID
    $lesson_id = $post->_quiz_lesson;
    $lesson = new TimberPost($lesson_id);
    $context['quiz_lesson'] =  $lesson;

    FundaWande()->language->fw_correct_lesson_lang($context['user']->fw_current_course, $lesson->ID);


    //Get the unit info for the current lesson
    $unit = FundaWande()->lessons->get_unit_info($context['user']->fw_current_course,$lesson->ID);

    $context['unit'] = $unit;

    $context['num_lessons'] = count($unit->lessons);

    //Get the module number for the parent module, to enable module-specific styling
    $context['module_number'] = get_term_meta($unit->parent, 'module_number', true);

    //Get the parent module title
    $context['module_title'] = get_term_meta($unit->parent, 'module_title', true );

    //Get the unit title
    $context['unit_title'] = get_term_meta($unit->term_id, 'module_title', true);

    //Get the nav links object and add to Timber context
    $context['nav_links'] = FundaWande()->lessons->get_lesson_nav_links($lesson->ID);


    Timber::render(array('lms/single-quiz.twig', 'page.twig'), $context);

}
