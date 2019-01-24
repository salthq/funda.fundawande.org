<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Coaching Utils Class
 *
 * All functionality pertaining to the coaching utils functionality of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Coaching_Utils {


    /**
     * Constructor
     */
    public function __construct() {

    }


    /**
     * Get the course users and coaches.
     *
     * @param integer $course_id the ID of the course.
     *
     * @return array array of users in the course
     */
    public function get_course_users($course_id) {

        // Set up array of course users
        $user_args = array(
            'meta_query' => array(
                array(
                    'key' => 'fw_current_course',
                    'value' => $course_id,
                    'compare' => '=='
                ),
            )
        );

        // Get array of ueser
        $users = get_users( $user_args );
      
        // loop through users and assign their coach to object
        foreach ($users as $key => $user) {
            $user->coach = get_user_meta($user->ID,'fw_coach',true);
        }

        // return the users
        return $users;
        

    } // end get_course_users()

    /**
     * Get the coaches.
     *
     * @return array array of users in the course
     */
    public function get_coaches() {
        // set up coach args
        $coach_args = array(
            'meta_query' => array(
                array(
                    'key' => 'is_coach',
                    'value' => true,
                    'compare' => '=='
                ),
            )
        );
        // get array of coaches
        $coaches = get_users( $coach_args );
        
        // return array of coaches
        return $coaches;
        

    } // end get_course_users()
    
    /**
     * Set the bulk coach to users
     *
     * @param $data Data passed through the form
     */
    public function set_bulk_coach($data) {

        // Loop through selected users
        foreach ($data['user_select'] as $key => $user_id) {

            // Update the user's coach
            update_user_meta($user_id,'fw_coach',$data['bulk_coach']);
        }
        
        return true;
        
    } // end set_bulk_coach()

    /**
     * Set the coach to a user
     *
     * @return array array of users in the course
     */
    public function set_coaches($data) {

        // Loop through all users
        foreach ($data['coaches'] as $key => $coach_id) {
            // Update the user's coach
            update_user_meta($key,'fw_coach',$coach_id);
        }

        return true;
        

    } // end set_coaches()


} // end FundaWande_Coaching_Utils
