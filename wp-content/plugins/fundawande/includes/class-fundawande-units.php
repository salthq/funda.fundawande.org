<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Units Class
 *
 * All functionality pertaining to the custom units functionality of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Units {

    /**
     * Constructor
     */
    public function __construct() {

        // Add the construct elements

    }

    /**
     * Unit progress functionality off of a given unit ID
     *
     * @return boolean module_progress return the unit progress percent
     *
     */
    public function fw_is_unit_complete($unit_id,$user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $unit_key = get_term_meta($unit_id, 'fw_unique_key',true);

        // Determine if an existing unit status exists
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_unit_progress',
            'user_id' => $user_id,
            'status' => $unit_key,
        );

        // possibly returns array, we just want one object
        $user_unit_status = get_comments($current_status_args);
        if ($user_unit_status) {
            return true;

        }
        return false;
    } // end fw_is_unit_complete

    /**
     * Check the unit progress and return
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     *
     */
    public function fw_unit_progress($unit_id, $course_id = null) {

        if (!$course_id) {
            $user_id = get_current_user_id();
            $course_id =  FundaWande()->lms->fw_get_current_course_id($user_id);
        }


        $unit_lessons = Sensei()->modules->get_lessons( $course_id , $unit_id);

        $completed = 0;
        $total = 0;

        foreach ($unit_lessons as $unit_lesson) {
            $total++;

            if (FundaWande()->lessons->fw_is_sub_unit_complete($unit_lesson->ID)) {
                $completed = $total;
            }
        }

        $unit_progress = ($completed/$total) * 100;

        if ($unit_progress == 100) {
            $user_id = get_current_user_id();
            $this->fw_complete_unit($unit_id,$user_id);
        }

        return $unit_progress;


    } // end fw_unit_progress

    /**
     * Complete unit functionality to track a unit as complete
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_complete_unit($unit_id, $user_id) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $unit_key = get_term_meta($unit_id, 'fw_unique_key',true);

        // Determine if an existing unit status exists
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_unit_progress',
            'user_id' => $user_id,
            'status' => $unit_key,
        );

        // possibly returns array, we just want one object
        $user_unit_status = get_comments($current_status_args);
        if (is_array($user_unit_status) && 1 == count($user_unit_status)) {
            $user_unit_status = array_shift($user_unit_status);

        }

        // If no current review then return the review form object with user and post details
        if (empty($user_unit_status)) {
            $time = current_time('mysql');
            $user = $user = get_userdata($user_id);
            $data = array(
                'comment_type' => 'fw_unit_progress',
                'user_id' => $user_id,
                'comment_date' => $time,
                'comment_approved' => $unit_key,
                'comment_karma' => 1,
                'comment_author' => $user->display_name,
                'comment_author_email' => $user->user_email

            );

            $comment_id = wp_insert_comment($data);

        } else {

            $comment_id = $user_unit_status->comment_ID;
        }

        return $comment_id;


    } // end fw_unit_complete


    /**
     * Get and set the current Unit based off a user
     *
     * @param int $user_id. The ID for the current user
     *
     * @return string $unit return the unit object of the current Unit
     */
    public function fw_get_set_current_unit($user_id = null) {

        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Get the current sub unit object from from the user
        $current_sub_unit = FundaWande()->lessons->fw_get_user_current_lesson($user_id);
        $current_unit = $this->fw_get_sub_unit_unit($current_sub_unit->ID);

        $current_unit = new TimberTerm($current_unit->term_id);

        // add the unit key to object
        $current_unit->key = get_term_meta($current_unit->term_id, 'fw_unique_key',true);

        // update the current unit off of the key in case they have become misaligned
        update_user_meta($user_id,'fw_current_unit',$current_unit->key);

        return $current_unit;

    }


    /**
     * Get the Unit of a sub unit
     *
     * @param int $sub_unit_id. The ID sub unit
     *
     * @return object $unit return the unit object of the current Unit
     */
    public function fw_get_sub_unit_unit($sub_unit_id) {

        // get taxonomy terms on this lesson
        $units = wp_get_post_terms($sub_unit_id, 'module', array("fields" => "all"));
        $unit = false;
        foreach ($units as $item) {
            if ($item->parent) {
                $unit = $item;
                break;
            }
        }

        $unit = new TimberTerm($unit->term_id);
        return $unit;

    }







} // end FundaWande_Units
