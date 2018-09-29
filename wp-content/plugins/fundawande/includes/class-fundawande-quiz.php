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

} // end FundaWande_Quiz
