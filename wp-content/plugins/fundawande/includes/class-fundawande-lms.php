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
        add_action( 'sensei_user_lesson_end', array( $this, 'fw_quiz_complete'));
        // do_action( 'sensei_user_lesson_end', $user_id, $lesson_id );

        // Fires the end of the submit_answers_for_grading function. It will fire irrespective of the submission results.
        add_action( 'sensei_user_quiz_submitted', array( $this, 'fw_quiz_submitted'));
        // do_action( 'sensei_user_quiz_submitted', $user_id, $quiz_id, $grade, $quiz_pass_percentage, $quiz_grade_type );

    }
    /**
     * Module summary.
     *
     * @param module parameters
     *
     * @return module return
     */
    public function fw_lesson_complete() {
        //log the information
        if (isset($_POST)) {
            $userID = $_POST['userid'];
            $postID = $_POST['lessonid'];

            $activity_logged = WooThemes_Sensei_Utils::update_lesson_status($userID, $postID, 'complete');
            $message = 'Lesson complete';

            return $activity_logged;
        }


    } // end module_name

} // end FundaWande_Lms
