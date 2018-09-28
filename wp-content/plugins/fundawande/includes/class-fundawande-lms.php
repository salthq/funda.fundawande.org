<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Lms Class
 *
 * All functionality pertaining to the custom LMS functionality of Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Lms {

    /**
     * Constructor
     */
    public function __construct() {

        add_action('FUNDAWANDE_AJAX_HANDLER_fw_lesson_complete', array( $this, 'fw_lesson_complete'));
        add_action('FUNDAWANDE_HANDLER_nopriv_fw_lesson_complete', array( $this, 'fw_lesson_complete'));

        //  This hook is fired after a lesson quiz has been graded and the lesson status is 'passed' OR 'graded'
        add_action( 'sensei_user_lesson_end', array( $this, 'fw_quiz_complete'),10,2);
        // do_action( 'sensei_user_lesson_end', $user_id, $lesson_id );

        // Fires the end of the submit_answers_for_grading function. It will fire irrespective of the submission results.
        add_action( 'sensei_user_quiz_submitted', array( $this, 'fw_quiz_submitted'),10,5);
        // do_action( 'sensei_user_quiz_submitted', $user_id, $quiz_id, $grade, $quiz_pass_percentage, $quiz_grade_type );

        add_filter('sensei_user_started_course', array( $this, 'fw_user_started_course'), 10,1);
        //  return apply_filters( 'sensei_user_started_course', $user_started_course, $course_id, $user_id );

        add_action( 'sensei_user_lesson_reset', array( $this, 'fw_user_reset_quiz'),10,2);


        remove_action( 'sensei_single_quiz_content_inside_before', array( 'Sensei_Quiz', 'the_title' ), 20 ); //output single quiz
        remove_action( 'sensei_single_quiz_content_inside_before', array( 'Sensei_Quiz', 'the_user_status_message' ), 40 );
        remove_action( 'sensei_single_quiz_questions_before', array( 'Sensei_Messages', 'send_message_link' ), 10 );


    }
    /**
     * Complete lesson functionality to track a lesson as complete
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_lesson_complete() {
        //log the information
        if (isset($_POST)) {
            $user_id = $_POST['userid'];
            $post_id = $_POST['postid'];
            $lesson_key = get_post_meta($post_id, 'fw_unique_key',true);

            // Run normal Sensei update logic
            $activity_logged = WooThemes_Sensei_Utils::update_lesson_status($user_id, $post_id, 'complete');

            // prevent fundawande progress tracking if unique lesson key isn't assigned
            if (!empty($lesson_key)) {
                // Determine if an existing status exists and assign
                $current_status_args = array(
                    'number' => 1,
                    'type' => 'fw_sub_unit_progress',
                    'user_id' => $user_id,
                    'status' => $lesson_key,
                );

                // possibly returns array, we just want one object
                $user_lesson_status = get_comments($current_status_args);
                if (is_array($user_lesson_status) && 1 == count($user_lesson_status)) {
                    $user_lesson_status = array_shift($user_lesson_status);

                }

                // If no current sub unit status then skip progress
                if (empty($user_lesson_status)) {
                    $time = current_time('mysql');
                    $user = $user = get_userdata($user_id);
                    $data = array(
                        'comment_type' => 'fw_sub_unit_progress',
                        'user_id' => $user_id,
                        'comment_date' => $time,
                        'comment_approved' => $lesson_key,
                        'comment_karma' => 1,
                        'comment_author' => $user->display_name,
                        'comment_author_email' => $user->user_email

                    );

                    $comment_id = wp_insert_comment($data);

                    // If this is a new completion we need to update the course, module and unit progress
                    // get course id of the lesson
                    $course_id = Sensei()->lesson->get_course_id($post_id);



                } else {

                    $comment_id = $user_lesson_status->comment_ID;
                }
                // Update user course progress
                $this->fw_update_course_progress_overall($user_id, $course_id);

                // Update the module and subunit progress and set the users current lesson to the next
                $this->fw_modules_status_of_sub_unit($user_id, $post_id);


                return $comment_id;
            }
            return false;
        }


    } // end fw_lesson_complete

    /**
     * Quiz submitted functionality to track a quiz as submitted but not graded
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_quiz_submitted($user_id, $quiz_id, $grade, $quiz_pass_percentage, $quiz_grade_type) {

        // Set default karma for non-auto graded quizzes and failed auto quizzes
        $karma = 0;

        // Check if the pass mark was achieved if it's a auto graded quiz and set karma to 1
        if (is_int($grade)) {
            if (($grade >= $quiz_pass_percentage) && ($quiz_grade_type == 'auto')) {
                $karma = 1;
            }
        }

        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $post_id = absint(get_post_meta($quiz_id, '_quiz_lesson', true));
        $lesson_key = get_post_meta($post_id, 'fw_unique_key',true);


        // Determine if an existing review exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'status' => $lesson_key,
        );

        // possibly returns array, we just want one object
        $user_lesson_status = get_comments($current_status_args);
        if (is_array($user_lesson_status) && 1 == count($user_lesson_status)) {
            $user_lesson_status = array_shift($user_lesson_status);

        }

        // If no current review then return the review form object with user and post details
        if (empty($user_lesson_status)) {
            $time = current_time('mysql');
            $user = $user = get_userdata($user_id);
            $data = array(
                'comment_type' => 'fw_sub_unit_progress',
                'user_id' => $user_id,
                'comment_date' => $time,
                'comment_approved' => $lesson_key,
                'comment_karma' => $karma,
                'comment_author' => $user->display_name,
                'comment_author_email' => $user->user_email

            );

            $comment_id = wp_insert_comment($data);
            update_comment_meta( $comment_id, 'quiz_grade',  $grade );

            // If this is a new completion we need to update the course, module and unit progress
            // get course id of the lesson
            $course_id = Sensei()->lesson->get_course_id($post_id);

            // Update user course progress
            $this->fw_update_course_progress_overall($user_id, $course_id);

            // Update the module and subunit progress and set the users current lesson to the next
            $this->fw_modules_status_of_sub_unit($user_id, $post_id);


        } else {
            $comment = array();
            $comment['comment_ID'] = $user_lesson_status->comment_ID;
            $comment['comment_approved'] = $lesson_key;
            $comment['comment_karma'] = $karma;
            wp_update_comment( $comment );

            $comment_id = $user_lesson_status->comment_ID;
            update_comment_meta( $comment_id, 'quiz_grade',  $grade );

        }

        return $comment_id;

    } // end fw_quiz_submitted

    /**
     * Quiz graded functionality to track a quiz as graded
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_quiz_complete($user_id, $lesson_id) {
        //log the information

        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $post_id = $lesson_id;
        $lesson_key = get_post_meta($post_id, 'fw_unique_key',true);


        // Determine if an existing review exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'status' => $lesson_key,
        );

        // possibly returns array, we just want one object
        $user_lesson_status = get_comments($current_status_args);
        if (is_array($user_lesson_status) && 1 == count($user_lesson_status)) {
            $user_lesson_status = array_shift($user_lesson_status);

        }

        // If no current review then return the review form object with user and post details
        if (empty($user_lesson_status)) {
            $time = current_time('mysql');
            $user = $user = get_userdata($user_id);
            $data = array(
                'comment_type' => 'fw_sub_unit_progress',
                'user_id' => $user_id,
                'comment_date' => $time,
                'comment_approved' => $lesson_key,
                'comment_karma' => 1,
                'comment_author' => $user->display_name,
                'comment_author_email' => $user->user_email

            );

            $comment_id = wp_insert_comment($data);

            // If this is a new completion we need to update the course, module and unit progress
            // get course id of the lesson
            $course_id = Sensei()->lesson->get_course_id($post_id);

            // Update user course progress
            $this->fw_update_course_progress_overall($user_id, $course_id);

            // Update the module and subunit progress and set the users current lesson to the next
            $this->fw_modules_status_of_sub_unit($user_id, $post_id);


        } else {
            $comment = array();
            $comment['comment_ID'] = $user_lesson_status->comment_ID;
            $comment['comment_approved'] = $lesson_key;
            $comment['comment_karma'] = 1;
            wp_update_comment( $comment );

            $comment_id = $user_lesson_status->comment_ID;

        }

        return $comment_id;

    } // end fw_quiz_submitted

    /**
     * Update a users course progress
     *
     * @return $course_progress return the course progress of the user
     */
    public function fw_update_course_progress_overall($user_id = null,$course_id) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Get course lessons
        $course_lessons = Sensei()->course->course_lessons($course_id);

        // Get lessons the user has completed
        global $wpdb;
        $query = "SELECT * FROM $wpdb->comments 
        WHERE comment_type = 'fw_sub_unit_progress' 
        AND user_id IN ($user_id) 
        AND comment_karma = 1";
        $user_lesson_status = $wpdb->get_results( $query );

        if ($user_lesson_status) {
            $course_progress = (count($user_lesson_status)/count($course_lessons)) * 100;
        } else {
            $course_progress = 0;
        }

        update_user_meta($user_id,'fw_course_progress',$course_progress);

        return $course_progress;

    }

    /**
     * Check lesson modules and their stats
     *
     * @return $changed return true if a module or unit was updated
     */
    public function fw_modules_status_of_sub_unit($user_id = null, $lesson_id) {

        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $changed = false;

        $lesson_nav = $this->fw_get_prev_next_lessons($lesson_id);
        if (!empty($lesson_nav['next'])) {
            $next_lesson_id = $lesson_nav['next'];
            $next_lesson_key = get_post_meta($next_lesson_id, 'fw_unique_key',true);
            // Set current sub unit to the next lesson
            update_user_meta($user_id,'fw_current_sub_unit',$next_lesson_key);
            $changed = true;

        }

        $current_unit = FundaWande()->units->fw_get_set_current_unit($user_id);
        $current_unit_progress = FundaWande()->units->fw_unit_progress($current_unit->term_id);
        update_user_meta($user_id,'fw_current_unit_progress',$current_unit_progress);
        $current_module = FundaWande()->units->fw_get_set_current_unit($user_id,$current_unit);
        $current_module_progress = FundaWande()->modules->fw_module_progress($current_module->term_id);
        update_user_meta($user_id,'fw_current_module_progress',$current_module_progress);


        return $changed;


    }



    /**
     * Course progress functionality off of a given module ID
     *
     * @return $course_progress return the course progress percent at a module level
     *
     */
    public function fw_course_progress_at_module($module_id) {

        $module_number = get_term_meta($module_id, 'module_number',true );

        $course_progress = ($module_number/12) * 100;

        return $course_progress;


    } // end fw_course_progress_at_module

    /**
     * Return a lesson link based on it's key and parent course
     * @param string $sub_unit_key. The key for the user's current lesson
     * @param int $course_id. The ID for the currently active course
     * 
     * @return string $sub_unit_link. The URL for the current lesson.  
     */
    public function fw_get_current_lesson_link($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $current_sub_unit = FundaWande()->lessons->fw_get_user_current_lesson($user_id);

        //The meta query returns an array, but we just want the lesson object
        if($current_sub_unit) {

            $sub_unit_link = get_permalink($current_sub_unit->ID);
        }
        else {
            // If no post is found which matches the query, add home_url as the link,
            // which will take them to their course page. This should only happen if the key
            // has been incorrectly set on the lesson editor screen. 
            $sub_unit_link = home_url('/'); 
        }        

        return $sub_unit_link;

    } // end fw_get_current_lesson_link()


    /**
     * Returns next and previous lesson IDs.
     *
     * @since  1.0.0
     * @param  integer $lesson_id Lesson ID.
     * @return array Multi-dimensional array of previous and next lesson IDs.
     */
    public function fw_get_prev_next_lessons( $lesson_id ) {
        // For modules, $lesson_id is the first lesson in the module.
        $links               = array();
        $course_id           = Sensei()->lesson->get_course_id( $lesson_id );
        $args = array(
            'number' => -1,
            'post_type' => 'lesson',
            'meta_query' => array(
                array(
                    'key' => '_lesson_course',
                    'value' => $course_id
                )
            ),
            'meta_key' => 'fw_unique_key',
            'orderby' => 'meta_value',
            'order' => 'ASC'

        );

        // Using just 'get_posts' returns an empty array for some reason
        $sub_unit_list = get_posts($args);

        if ( is_array( $sub_unit_list ) && count( $sub_unit_list ) > 0 ) {
            $found = false;

            foreach ( $sub_unit_list as $item ) {
                if ( isset( $item->ID ) && $found ) {
                    $links['next'] = $item->ID;
                    break;
                } else {
                    $links['next'] = '';
                }
                if ( isset( $item->ID ) && (absint( $item->ID ) === absint( $lesson_id )) ) {
                    $found = true;
                } else {
                    $links['previous'] = $item->ID;

                }
            }
        }

        return $links;
    } // End fw_get_prev_next_lessons()

    /**
     * Sets the first sub unit of the course to current sub unit
     *
     * @since  1.0.0
     * @param  integer $course_id Course ID.
     * @param  integer $user_id User ID.
     * @return string $sub_unit_key
     */
    public function fw_set_first_sub_unit($course_id , $user_id = null ) {
        //
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $course_lessons = Sensei()->course->course_lessons($course_id);
        $sub_unit_key = '';
        if ($course_lessons[0] ) {
            update_user_meta($user_id, 'fw_current_sub_unit','r4m_m00_u01_s01' );
            update_user_meta($user_id, 'fw_current_unit','r4m_m00_u01');
            update_user_meta($user_id, 'fw_current_module','r4m_m00' );

        }
        return $sub_unit_key;


    } // End fw_get_prev_next_lessons()



    /**
     * Get the sub unit status from a lesson key
     *
     * @return boolean $status return true if lesson is complete by user, false otherwise
     *
     */
    public function fw_is_sub_unit_current($lesson_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $current_lesson_key = get_user_meta($user_id, 'fw_current_sub_unit',true);
        $lesson_key = get_post_meta($lesson_id, 'fw_unique_key',true);

        if ($current_lesson_key == $lesson_key) {
            return true;
        }

        return false;


    } // end fw_is_sub_unit_current

    /**
     * Get the sub unit status from a lesson key
     *
     * @return boolean $status return true if lesson is complete by user, false otherwise
     *
     */
    public function fw_user_started_course($user_started_course) {

        // Overide alles and make user always started course

        $user_started_course = true;

        return $user_started_course;


    } // end fw_user_started_course

    /**
     * Returns the global media URL variable
     * 
     * @return string $media_url The URL for where media assets are served from
     */

     public function fw_get_media_url() {
         $media_url = "https://storage.googleapis.com/fwpilot_media/";

         return $media_url;
     }


    /**
     * Fires after an activity is reset to update the progress
     *
     **/
    public function fw_user_reset_quiz($user_id, $lesson_id) {
        $lesson_key = get_post_meta($lesson_id, 'fw_unique_key',true);
        // Determine if an existing review exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'status' => $lesson_key,
        );

        $status = false;
        $user_lesson_status = get_comments($current_status_args);
        if ($user_lesson_status) {
            // possibly returns array, we just want one object
            if (is_array($user_lesson_status) && 1 == count($user_lesson_status)) {
                $user_lesson_status = array_shift($user_lesson_status);

            }
            error_log(print_r($user_lesson_status,true));
            $status = wp_delete_comment($user_lesson_status->comment_ID);

        }
        return $status;

    }


} // end FundaWande_Lms
