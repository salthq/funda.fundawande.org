<?php
/**
 *  Template Name: Change Course Page
 *
 * @package Pango
 */

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;
    $current_course_id = $_GET['current'];
    if ($current_course_id) {
        $context['current_course'] = new TimberPost($current_course_id);
    }
    $new_course_id = $_GET['new'];
    if ($new_course_id) {
        $context['new_course'] = new TimberPost($new_course_id);
    }
    
    Timber::render(array('change-course.twig', 'page.twig'), $context);

}