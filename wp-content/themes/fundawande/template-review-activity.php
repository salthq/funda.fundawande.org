<?php
/**
*Template Name: Assessment review Page Template
 *
 * @package Pango
 */

if (isset($_POST['task'])) {
    FundaWande()->coaching->update_assessment($_POST);
    // error_log('post');
    // error_log(print_r($_POST,true));
}

if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    if (!(is_super_admin() || current_user_can( 'coach' ))) {
        wp_redirect('/my-dashboard');
        exit();
    }
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    if (isset($_GET['assessment_id']) && isset($_GET['user_id'])) {
        $activity_id = $_GET['assessment_id'];
        $user_id = $_GET['user_id'];
    } else {
        wp_redirect('/coach-dashboard');
    }

    $activity_needs_feedback = FundaWande()->activities->activity_needs_feedback($activity_id);

    if ($activity_needs_feedback) {


        $context['activity_data'] = StartupSchool()->coaching->get_activity_review($activity_id, $user_id);

        Timber::render(array('lms/template-review-activity.twig', 'page.twig'), $context);
    } else {
        $quiz_id = get_post_meta($activity_id, '_lesson_quiz', true);
        wp_redirect('/wp-admin/admin.php?page=sensei_grading&user='.$user_id.'&quiz_id='.$quiz_id);
    }

}