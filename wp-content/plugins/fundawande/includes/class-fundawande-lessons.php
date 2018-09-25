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

        // Setup Taxonomies
        add_action( 'init', array( $this, 'setup_sub_unit_icon_taxonomy' ), 100 );


    }

    /**
	 * Returns all lessons for the given module ID, and also adds a term array to each lesson
	 *
	 * @since 1.0.00
     * 
     * @author jtame
	 *
	 * @param integer $course_id
     * 
	 * @param integer $term_id. The unit or module id to retrieve lessons for.
     * 
	 * @return array $lessons
	 */
	public function get_lessons( $course_id , $term_id ){

		$lesson_query = Sensei()->modules->get_lessons_query( $course_id, $term_id );
        $user_id = get_current_user_id();
        if ($user_id) {
            $current_lesson_key = get_user_meta($user_id, 'fw_current_sub_unit', true);
        } else {
            $current_lesson_key = null;
        }

		if( isset( $lesson_query->posts ) ){
            //If any tags are added to the lesson, add to the lesson object
            foreach ($lesson_query->posts as $key => $lesson ) {
                $lesson->term = array();
                $lesson->term = wp_get_post_terms($lesson->ID, 'sub-unit-icon');
                if ($lesson->term) {
                    $lesson->icon = $lesson->term[0]->slug;
                } else {
                    $lesson->icon = 'unit_quiz';
                }
                $lesson->key = get_post_meta($lesson->ID, 'fw_unique_key',true);
                if ($current_lesson_key && $current_lesson_key == $lesson->key) {
                    $lesson->current = true;
                }
                $lesson->complete = FundaWande()->lms->fw_get_sub_unit_status($lesson->key);
                $lesson->quiz = get_post_meta($lesson->ID, '_quiz_has_questions', true);

            }
			return $lesson_query->posts;

        }
        else {
		    return array();
		}
    } // end get_lessons()
    
    /**
	 * Returns the current unit info, including all lessons within that unit
	 *
	 * @since 1.0.00
     * 
     * @author jtame
	 *
	 * @param integer $course_id
     * 
	 * @param integer $post_id. The ID of the lesson
     * 
	 * @return array $unit
	 */
    public function get_unit_info($course_id, $post_id) {
        $unit = Sensei()->modules->get_lesson_module( $post_id );
        //Add the lessons array to the unit info to make that info available on the single lesson template
        $unit->lessons = $this->get_lessons($course_id, $unit->term_id);

        //$unit->title = get_post_meta($post_id, 'module_title',

        return $unit;
    } //end get_unit_info()

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

     /**
      * Setup the "sub unit icon" taxonomy, linked to the "course" post type.
      * @since  1.1.0
      * @return void
      */
     public function setup_sub_unit_icon_taxonomy () {
         register_taxonomy(
             'sub-unit-icon',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
             'lesson',        //post type name
             array(
                 'hierarchical' => true,
                 'label' => 'Sub Unit Icons',  //Display name
                 'query_var' => true,
                 'rewrite' => array(
                     'slug' => 'sub-unit-icon', // This controls the base slug that will display before each term
                     'with_front' => false // Don't display the category base before
                 )
             )
         );

     } // End setup_sub_unit_icon_taxonomy()


 } // end FundaWande_Lessons