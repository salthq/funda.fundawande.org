<?php
/**
*Template Name: Coach Dashboard Page Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    if (!(is_super_admin() || current_user_can( 'coach' ))) {
        wp_redirect('/');
        exit();
    }
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    if (!empty($_GET['module'])) {
        $module_id = $_GET['module'];
    } else {
        $module_id = '';
    }

    if (!empty($_GET['fw_course'])) {
        $course = $_GET['fw_course'];
        $context['selected_course'] = $_GET['fw_course'];
        $context['modules'] = Sensei()->modules->get_course_modules($course);
        // Get all courses in the term via path slug
        $course_args = array(
            'post_type' => 'course',
        );
        $context['courses'] = Timber::get_posts($course_args);
        if (!empty($_GET['module'])) {
            $module_id = '';
            foreach($context['modules'] as $course_module) {
                if ($module_id = $course_module->term_id) {
                    $module_id = $_GET['module'];
                }
            }
            
        }

    } else {
        $course ='';
         // Get all courses in the term via path slug
         $course_args = array(
            'post_type' => 'course',
        );
        $context['courses'] = Timber::get_posts($course_args);
        $context['modules'] = get_terms( array(
            'taxonomy' => 'module'
        ));
    }

    if (!empty($_GET['coach'])) {
        $coach = $_GET['coach'];
        $context['coach'] = $_GET['coach'];
    } else {
        $coach = null;
    }

    
    $context['module_id'] = $module_id;

    if (!empty($_GET['user'])) {
        $user_id = $_GET['user'];
        $context['user_id'] = $_GET['user'];
        $context['week_number'] = $week_number = 'all';
    } else {
        $user_id = null;
    }
   


    $context['assessments_data'] = FundaWande()->coaching->get_teacher_assessments($course,$coach,$module_id,$user_id);

    Timber::render(array('lms/template-coach-dash.twig', 'page.twig'), $context);

}