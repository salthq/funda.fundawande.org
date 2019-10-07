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
    $context['lesson_id'] = $lesson_id;

    // Add check to see that quiz is correctly set for the user
    $context['quiz_correctly'] = FundaWande()->quiz->check_correctly_submitted_quiz($lesson_id,$user->ID);

    $current_course_id =  FundaWande()->lms->fw_get_current_course_id($user->ID);
    $lesson_course_ID = Sensei()->lesson->get_course_id($lesson_id);

    if ($lesson_course_ID !== $current_course_id) {
        wp_redirect('/change-course?current='.$current_course_id.'&new='.$lesson_course_ID);
    }

    // check if is retry quiz
    $context['quiz_retry'] = FundaWande()->quiz->fw_is_quiz_retry($lesson_id);
    $context['quiz_resubmit'] = FundaWande()->quiz->user_has_submitted($lesson_id,$user->ID);
    $context['quiz_attempts'] = FundaWande()->quiz->get_quiz_attempts($lesson_id,$user->ID);
    $context['show_notifications'] = FundaWande()->quiz->show_assessment_notifications($lesson_id,$user->ID);


    //Get the unit info for the current lesson
    $sub_unit_meta = FundaWande()->lessons->fw_get_sub_unit_meta($current_course_id, $lesson_id );

    $context['sub_unit_meta'] = $sub_unit_meta;
    $context['module_number'] = $sub_unit_meta->module_number;
    $context['unit_number'] = $sub_unit_meta->unit_number;


    // Quiz Timer setup
    $quiz_timer = get_post_meta($lesson_id, 'pango-qt_limit', true);
    $context['quiz_timer'] = $quiz_timer;

    $quiz_time_expired = get_post_meta($lesson_id, 'quiz_time_expired', true);
    $context['quiz_time_expired'] = $quiz_time_expired;

    $user_quiz_grade = '';
    $quiz_id = (int) get_post_meta($post->ID, '_quiz_lesson', true);
    $context['quiz_lesson_id'] = $quiz_id;
    $user_quiz_status = WooThemes_Sensei_Utils::user_lesson_status($quiz_id, $user->ID);

    if (!empty($user_quiz_status)) {
        $user_quiz_grade = get_comment_meta($user_quiz_status->comment_ID, 'grade', true);
        $context['grade'] = $user_quiz_grade;
    }

    if (($quiz_timer != '') && ('' == $user_quiz_grade)) {
        unset($_SESSION['quizLimit_' . $lesson_id]);
        $_SESSION['quizLimit_' . $lesson_id] = $quiz_timer;

        if(isset($_SESSION['quizStart_' . $lesson_id])) {
            $context['quiz_start'] = $_SESSION['quizStart_' . $lesson_id];
        }
    }

    Timber::render(array('lms/single-quiz.twig', 'page.twig'), $context);

}
