<?php
/**
 * The Template for displaying all single courses pages to show the modules
 *
 *
 * @author 		Pango
 * @package 	FundaWande
 * @version     1.0.0
 */

// if user is not logged in direct to the custom FW login page
if (!is_user_logged_in()) {
    wp_redirect(get_site_url(null, '/login'));
    exit();
} elseif (isset($_GET['course_id'])) {
    $current_course_id = $_GET['course_id'];
    $user_id = get_current_user_id(); 
    update_user_meta($user_id, 'fw_cohort', $current_course_id );
    update_user_meta($user_id, 'fw_current_course', $current_course_id );

} 

if (class_exists('Timber')) {

    $context = Timber::get_context();
    $post = new TimberPost();

    $context['post'] = $post;

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    $current_course_id =  FundaWande()->lms->fw_get_current_course_id($user->ID);

    if ($post->ID != $current_course_id) {
        wp_redirect('/change-course?current='.$current_course_id.'&new='.$lesson_course_ID);
    }

    // Get the course modules to visualise on the course page
    $context['modules'] = FundaWande()->modules->get_course_modules($post->ID);


    Timber::render(array('lms/single-course.twig', 'page.twig'), $context);
}
