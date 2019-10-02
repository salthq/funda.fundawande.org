<?php
/**
*Template Name: Profile Page Template
 *
 * @package Pango
 */

if (isset($_POST['task'])) {

    error_log(print_r($_POST,true));
    if ($_POST['task'] == 'profile_submit') {
        $user_id = $_POST['user_id'];

        foreach ($_POST as $key => $item) {
            if (($key != 'task') && ($key != 'user_email') && ($key != 'user_id')) {
                update_user_meta($user_id, $key, $item);
            } elseif ($key == 'user_email') {
                $user_id = wp_update_user(array( 'ID' => $user_id, $key => $item));
            }
        }
    }
}

if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;
    $user = new TimberUser();
    $context['user'] = $user;
    $current_course_id =  FundaWande()->lms->fw_get_current_course_id($user->ID);
    $context['course'] = new TimberPost($current_course_id);
    $context['course_progress'] = Sensei()->course->get_completion_percentage($current_course_id, $user->ID);

    Timber::render(array('template-profile.twig', 'page.twig'), $context);

}