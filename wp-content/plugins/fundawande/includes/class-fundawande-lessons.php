<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}

/**
 * FundaWande Lessons Class
 *
 * All functionality pertaining to the custom lesson functionality of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */

 class FundaWande_Lessons {

    /**
     * Constructor
     */
    public function __construct() {

        // Add the construct elements

    }

    /**
     * Get Lesson nav links to enable bi-directional navigation between lessons
     * 
     * @author jtame
     *
     * @param integer $post_id. The ID of the lesson
     *
     * @return object $nav_links
     */

    public function get_lesson_nav_links($post_id) {
        $prev_next_lessons = sensei_get_prev_next_lessons ($post_id);

        $nav_links = (object) [
            'unit_completed' => false,
            'prev_link' => '',
            'next_link' => '',
            'next_unit_link' => '',
            'next_unit_title' => ''
        ];

        if ( isset( $prev_next_lessons['previous']) || isset( $prev_next_lessons['next']) ) {
            if( isset( $prev_next_lessons['previous']) ) {
                //The value for the prev_link property must only be set if the previous link is a lesson. 
                if( strpos($prev_next_lessons['previous']['url'], '/lesson/') ) {
                    $nav_links->prev_link = $prev_next_lessons['previous']['url'];
                }
            }
            if( isset( $prev_next_lessons['next']) ) {
                //The value for the next_link property must only be set if the next link is a lesson. 
                if( strpos($prev_next_lessons['next']['url'], '/lesson/') ) {
                    $nav_links->next_link = $prev_next_lessons['next']['url'];
                }
                //If the next link is a module instead, then the lesson is the last one within a unit. 
                elseif( strpos($prev_next_lessons['next']['url'], '/modules/') ) {
                    $nav_links->unit_completed = true;
                    $nav_links->next_unit_link = $prev_next_lessons['next']['url'];
                    $nav_links->next_unit_title = $prev_next_lessons['next']['name'];
                }
            }
            
            return $nav_links;
        }    
    } // end get_lesson_nav_links()

 } // end FundaWande_Lessons