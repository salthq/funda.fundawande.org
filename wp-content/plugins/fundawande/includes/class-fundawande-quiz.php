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

        
        add_action( 'admin_notices', array($this,'show_post_order_info') );

        add_action('admin_enqueue_scripts', array( $this,'al_lessons_admin_enqueue'));

        add_action('FUNDAWANDE_AJAX_HANDLER_fw_reset_quiz', array($this,'fw_reset_quiz'));

    }

     /**
     * Complete lesson functionality to track a lesson as complete
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_reset_quiz() {
        //log the information
        if (isset($_POST)) {
            error_log(print_r($_POST,true));
            $user_id = $_POST['user_id'];
            $post_id = $_POST['post_id'];
            Sensei_Utils::sensei_remove_user_from_lesson($post_id,$user_id);
            echo 'success reset!';
        }
        die;
    }
    
    
    /**
     * Display notice when user deletes the post
     *
     * When user deletes the post, show the notice for the user
     * to go and refresh the post order.
     *
     * @since 1.0.0
     */
    function show_post_order_info() {
        global $pagenow, $post;

        $user_id = get_current_user_id();

        if ( $pagenow == 'post.php' && $post->post_type == 'lesson') {
            ?>
            <div id="fw-updater-notice" class="updated notice">
                <p><?php _e( 'Reset this lesson (NB: This will completely reset the current lesson & assessment and clear your progress)', 'my_plugin_textdomain' ); ?></p>
                <p  class="submit"><a id="fw-reset-quiz" href="#" class="al-update-now button-primary" data-user-id="<?php echo $user_id; ?>" data-post-id="<?php echo $post->ID; ?>">Reset my lesson & assessment progress</a></p>
            </div>
            <?php
        }
    }

     /**
     * Enqueue lessons admin JS
     *
     * @return void
     */
    public function al_lessons_admin_enqueue( ) {
        global $pagenow, $post;

        if ( $pagenow == 'post.php' && $post->post_type == 'lesson') {
            // Check if it is a renewal order, if not, then handle the change
            wp_enqueue_script('lessons-admin-script', FundaWande()->plugin_url.'assets/js/lessons-admin.min.js', array(), FundaWande()->version, true);
            wp_localize_script( 'lessons-admin-script', 'fundawande_ajax_object', array( 'ajaxurl' => FundaWande()->plugin_url . 'fundawande_ajax.php') );
        
        }
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
    public function fw_is_quiz_retry($lesson_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }


        // Determine if an existing comment exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'post_id' => $lesson_id,
            'status' => array('complete','in-progress','ungraded'),
        );

        $status = false;
        $user_lesson_status = get_comments($current_status_args);
        if(is_array($user_lesson_status ) && 1 == count($user_lesson_status )) {
            $user_lesson_status  = array_shift($user_lesson_status );
        }

        if ($user_lesson_status) {
            switch ($user_lesson_status->comment_approved) {
                case 'complete':
                    $status = false;
                    break;
                case 'in-progress':
                    $status = true;
                    break;
                // Add default just as a catch all
                default:
                    $status = false;
            }

        }

        return $status;


    } // end fw_get_sub_unit_status

    /**
     * Get the number of attempts that a user tries a quiz.
     *
     * @param integer $lesson_id the ID of the activity (lesson) to check.
     * @param integer $user_id the ID of the user to check.
     * 
     * @return boolean true or false.
     */
    public function get_quiz_attempts($lesson_id,$user_id) {
       
        // Determine if an existing comment exists and assign
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_sub_unit_progress',
            'user_id' => $user_id,
            'post_id' => $lesson_id,
            'status' => array('complete','in-progress','ungraded'),
        );

        $user_lesson_status = get_comments($current_status_args);
        if(is_array($user_lesson_status ) && 1 == count($user_lesson_status )) {
            $user_lesson_status  = array_shift($user_lesson_status );
        }

        if ($user_lesson_status) {
            $quiz_attempts = get_comment_meta($user_lesson_status->comment_ID, 'quiz_attempts', true);
            return $quiz_attempts;
        }

        return false;
       

    }

    /**
     * Check whether a quiz has been graded correctly.
     *
     * @param integer $lesson_id the ID of the activity (lesson) to check.
     * @param integer $user_id the ID of the user to check.
     * 
     * @return boolean true or false.
     */
    public function check_correctly_submitted_quiz($lesson_id,$user_id) {
       
        $lesson_completed = false;

        $lesson_status = Sensei_Utils::user_lesson_status($lesson_id,$user_id);
        if (isset($lesson_status) && $lesson_status->comment_approved == 'complete')  {
            $lesson_completed = true;
        }

        return false;
       
    }

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

        $has_submitted = false;
        $quiz_id = get_post_meta($lesson_id, '_lesson_quiz', true);

        $questions = Sensei_Utils::sensei_get_quiz_questions($quiz_id);

        foreach ($questions as $key => $question) {
            $user_answer_content = Sensei()->quiz->get_user_question_answer( $lesson_id,  $question->ID , $user_id );

            if (!empty($user_answer_content)) {
                $has_submitted = true;
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
        $needs_feedback = false;
        $quiz_id = get_post_meta($lesson_id, '_lesson_quiz', true);

        $questions = Sensei_Utils::sensei_get_quiz_questions($quiz_id);

        foreach ($questions as $key => $question) {

            if ( $needs_feedback) {
                break;
            }

            $type = Sensei()->question->get_question_type( $question->ID );
            // error_log($type);
            
            switch ($type){
                case "boolean":
                    $needs_feedback = false;
                    continue;
                case 'multiple-choice':
                    $needs_feedback = false;
                    continue;
                case 'gap-fill':
                    $needs_feedback = false;
                    continue;
                case 'multiple-choice-with-images':
                    $needs_feedback = false;
                    continue;
                case 'drag-and-drop-non-sequential':
                    $needs_feedback = false;
                    continue;
                case 'drag-and-drop-sequential':
                    $needs_feedback = false;
                    continue;
                default:
                $needs_feedback = true;
            }
        }
        return $needs_feedback;
    

    }

} // end FundaWande_Quiz
