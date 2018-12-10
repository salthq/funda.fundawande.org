<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Quiz Class
 *
 * All functionality pertaining to the custom quiz functionality of Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Quiz {

    /**
     * Constructor
     */
    public function __construct() {

        // Load single quiz content template
        add_action('fundawande_single_quiz_content', array($this, 'load_single_quiz_template'), 8,1);

        // Load quiz question template for single line and file upload with feedback
        add_action('fundawande_question_feedback_template', array($this, 'load_question_feedback_template'), 8);

    }

     /**
     * Load assessment question template for single line and upload with feedback
     */
    public function load_question_feedback_template(){
        // load collapsible Sensei template name if it exists in the users theme
        require ( ABSPATH . 'wp-content/plugins/fundawande/templates/question_type-needs-feedback.php');

    }

    /**
     *  Add a notice to the array of pastpapers for display at a later stage.
     *
     * @param array $language. This is the language variable as set by the url on each page via url parameters
     *
     * @return object $language_obj
     */
    public function load_single_quiz_template() {

        require(ABSPATH . 'wp-content/plugins/fundawande/templates/single-quiz.php');


    } // end load_single_quiz_template()

    /**
         * Check whether the user has completed the given lesson.
         *
         * @param integer $activity_id the ID of the activity (lesson) to check.
         * @param integer $user_id the ID of the user to check.
         *
         * @return boolean $lesson_completed True if lesson graded, false if not.
         */
        public function user_completed_lesson($activity_id, $user_id) {

            $lesson_completed = false;

            $lesson_status = Sensei_Utils::user_lesson_status($activity_id,$user_id);
            if ($lesson_status) {
                $lesson_completed = ($lesson_status->comment_approved == 'graded') ? true : false;
            }
            return $lesson_completed;

    }
    /**
     * Get the sub unit status from a lesson key
     *
     * @return boolean $status return true if lesson is complete by user, false otherwise
     *
     */
    public function fw_is_quiz_retry($lesson_id_or_key, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $lesson_id = $lesson_id_or_key;
        if (is_numeric($lesson_id_or_key)) {

            $lesson_key = get_post_meta($lesson_id_or_key,'fw_unique_key',true);
        } else {
            $lesson_key = $lesson_id_or_key;
        }


        // Determine if an existing review exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'status' => $lesson_key,
        );

        $status = false;
        $user_lesson_status = get_comments($current_status_args);
        if(is_array($user_lesson_status ) && 1 == count($user_lesson_status )) {
            $user_lesson_status  = array_shift($user_lesson_status );
        }

        if ($user_lesson_status) {
            switch ($user_lesson_status->comment_karma) {
                case 0:
                    If (WooThemes_Sensei_Utils::user_completed_lesson( $lesson_id, get_current_user_id())) {
                        $status = true;
                    }
                    break;
                case 1:
                    $status = false;
                    break;
                // Add default just as a catch all
                default:
                    $status = true;
            }

        }

        return $status;


    } // end fw_get_sub_unit_status


    /**
     * Check whether activity feedback has been released to the user.
     *
     * @return boolean true or false.
     */
    public function user_can_view_feedback($lesson_id,$user_id) {
        $has_feedback = false;
        $comment_args = array(
            'number' => 1,
            'status' => array('graded','in-progress','ungraded'),
            'type' => 'sensei_lesson_status',
            'user_id' => $user_id,
            'post_id' => $lesson_id
        );

        $activity_comment = get_comments( $comment_args );

        if ($activity_comment) {
            if (is_array($activity_comment) && 1 == count($activity_comment)) {
                $activity_comment = array_shift($activity_comment);
            }

            $has_feedback = get_comment_meta($activity_comment->comment_ID, 'quiz_has_feedback', true);
        }

        return $has_feedback;

    }

      /**
     * Check whether activity feedback has been released to the user.
     *
     * @return boolean true or false.
     */
    public function show_assessment_notifications($lesson_id,$user_id) {
        $show_notifications = false;
        $comment_args = array(
            'number' => 1,
            'status' => array('graded','in-progress','ungraded'),
            'type' => 'sensei_lesson_status',
            'user_id' => $user_id,
            'post_id' => $lesson_id
        );

        $activity_comment = get_comments( $comment_args );

        if ($activity_comment) {
            if (is_array($activity_comment) && 1 == count($activity_comment)) {
                $activity_comment = array_shift($activity_comment);
            }

            if(strtotime($activity_comment->comment_date) > strtotime("-20 seconds")) {
                $show_notifications = true;

            }
        }

        return $show_notifications;

    }

    /**
     * Check whether user has submitted a file/video.
     *
     * @return boolean true or false.
     */
    public function user_has_submitted($lesson_id,$user_id) {

        $has_submitted = true;
        $quiz_id = get_post_meta($lesson_id, '_lesson_quiz', true);

        $questions = Sensei_Utils::sensei_get_quiz_questions($quiz_id);

        foreach ($questions as $key => $question) {
            $user_answer_content = Sensei()->quiz->get_user_question_answer( $lesson_id,  $question->ID , $user_id );

            if (!$user_answer_content) {
                $has_submitted = false;
            }
        }


        return $has_submitted;

    }
    

    

      /**
     * Check whether the activity needs feedback, or is graded automatically.
     *
     * Activities with boolean, gap fill and MCQ questions do not need feedback as they are graded automatically. Activities with file-upload and single line submission need feedback
     *
     * @return boolean true or false.
     */
    public function assessment_needs_feedback($lesson_id) {
        $needs_feedback = true;
        $quiz_id = get_post_meta($lesson_id, '_lesson_quiz', true);

        $questions = Sensei_Utils::sensei_get_quiz_questions($quiz_id);

        foreach ($questions as $key => $question) {

            $type = Sensei()->question->get_question_type( $question->ID );
            
            switch ($type){
                case "boolean":
                    $needs_feedback = false;
                    break;
                case 'multiple-choice':
                    $needs_feedback = false;
                    break;
                case 'gap-fill':
                    $needs_feedback = false;
                    break;
                case 'multiple-choice-with-images':
                    $needs_feedback = false;
                    break;
                case 'drag-and-drop-non-sequential':
                    $needs_feedback = false;
                    break;
                case 'drag-and-drop-sequential':
                    $needs_feedback = false;
                    break;
                default:
                    $needs_feedback = true;
            }
            break;
        }
        return $needs_feedback;
    

    }

} // end FundaWande_Quiz
