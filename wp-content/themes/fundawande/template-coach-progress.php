<?php
/**
*Template Name: Coach Progress Dashboard Page Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()));
        exit();
    }
    $is_coach =  FundaWande()->coaching_utils->is_user_coach();
    if (!(is_super_admin() || $is_coach)) {
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
        $context['courses'] =  FundaWande()->lms->get_courses();
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
        $context['courses'] =  FundaWande()->lms->get_courses();
        $context['modules'] = get_terms( array(
            'taxonomy' => 'module'
        ));
    }

    if (!empty($_GET['coach'])) {
        $coach = $_GET['coach'];
        $context['selected_coach'] = $_GET['coach'];
    } else {
        $coach = null;
    }

    $context['coaches'] = FundaWande()->coaching_utils->get_coaches();

    
    $context['module_id'] = $module_id;

    if (!empty($_GET['user']) && $_GET['user'] != '') {
        $user_id = $_GET['user'];
        $context['user_id'] = $_GET['user'];
        $context['week_number'] = $week_number = 'all';
        $context['module_progress_data'] = FundaWande()->coaching->get_teacher_module_progress($course,$user_id);

    } else {
        $user_id = null;
    }
   


    $context['course_progress_data'] = FundaWande()->coaching->get_teacher_course_progress($course,$coach,$user_id);
    

    Timber::render(array('lms/template-coach-progress-dash.twig', 'page.twig'), $context);

}