<?php
/**
 * Template Name: My Courses Page Template
 *
 * @author Pango

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

    // Assign current user to context
    $user = new TimberUser();
    $context['user'] = $user;

    $course_args = array(
        'post_type' => 'course',
    );

    $courses = Timber::get_posts($course_args);

    foreach ($courses as $course) {

        // Get number of completed lessons per course
        if (
            Sensei_Utils::user_started_course($course->ID,  get_current_user_id())
            || Sensei_Utils::user_completed_course($course->ID,  get_current_user_id())
        ) {
            $course->completed_lessons = count(Sensei()->course->get_completed_lesson_ids($course->ID, get_current_user_id()));
            $course->lesson_count = count(Sensei()->course->course_lessons($course->ID));
        }
    }

    $context['courses'] = $courses;


    Timber::render(array('lms/template-courses.twig', 'page.twig'), $context);
}
