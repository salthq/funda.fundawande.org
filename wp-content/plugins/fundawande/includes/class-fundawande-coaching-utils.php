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

        $user_args = array(
            'meta_query' => array(
                array(
                    'key' => 'fw_current_course',
                    'value' => $course_id,
                    'compare' => '=='
                ),
            )
        );

        $users = get_users( $user_args );
        
        foreach ($users as $key => $user) {
            $user->coach = get_user_meta($user->ID,'fw_coach',true);
        }

        return $users;
        

    } // end get_course_users()

    /**
     * Get the coaches.
     *
     *
     * @return array array of users in the course
     */
    public function get_coaches() {

        $user_args = array(
            
        );

        $users = get_users( $user_args );
        

        return $users;
        

    } // end get_course_users()


} // end FundaWande_Coaching_Utils
