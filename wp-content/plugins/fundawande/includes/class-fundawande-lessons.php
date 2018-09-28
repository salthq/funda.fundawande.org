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
                $lesson->term = wp_get_post_terms($lesson->ID, 'lesson-tag');
                $lesson->icon = get_post_meta($lesson->ID, 'sub_unit_icon',true);
                $lesson->key = get_post_meta($lesson->ID, 'fw_unique_key',true);
                $lesson->title = get_post_meta($lesson->ID, 'lesson_title', true);
                if ($current_lesson_key && $current_lesson_key == $lesson->key) {
                    $lesson->current = true;
                }
                $lesson->complete = $this->fw_is_sub_unit_complete($lesson->key);
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
                    //TODO: Change this from the actual title of the lesson, to the custom title
                    $nav_links->next_unit_title = $prev_next_lessons['next']['name'];
                }
            }
            
            return $nav_links;
        }    
    } // end get_lesson_nav_links()

     /**
      * Return a lesson link based on it's key and parent course
      * @param string $sub_unit_key. The key for the user's current lesson
      * @param int $course_id. The ID for the currently active course
      *
      * @return string $sub_unit_link. The URL for the current lesson.
      */
     public function fw_get_user_current_lesson($user_id = null) {
         if (!$user_id) {
             $user_id = get_current_user_id();
         }

         $current_course_id = get_user_meta($user_id,'fw_current_course',true);
         $current_sub_unit_key = get_user_meta($user_id,'fw_current_sub_unit',true);

         // '_lesson_course' is a user meta field for the current active course, which could be in English or Xhosa.
         // The lesson's key is the same in both courses, so this meta query matches the key to the current active course.
         $args = array(
             'number' => 1,
             'post_type' => 'lesson',
             'meta_query' => array(
                 array(
                     'key' => 'fw_unique_key',
                     'value' => $current_sub_unit_key
                 ),
                 array(
                     'key' => '_lesson_course',
                     'value' => $current_course_id
                 )
             )
         );

         // Using just 'get_posts' returns an empty array for some reason
         $sub_unit_list = Timber::get_posts($args);

         //The meta query returns an array, but we just want the lesson object
         if(is_array($sub_unit_list) && 1 == count($sub_unit_list)) {
             $sub_unit = array_shift($sub_unit_list);
             return $sub_unit;
         }
         return false;

     } // end fw_get_user_current_lesson()


     /**
      * Get the sub unit status from a lesson key
      *
      * @return boolean $status return true if lesson is complete by user, false otherwise
      *
      */
     public function fw_is_sub_unit_complete($lesson_id_or_key, $user_id = null) {
         if (!$user_id) {
             $user_id = get_current_user_id();
         }

         if (is_int($lesson_id_or_key)) {
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
         if ($user_lesson_status) {

             $status = true;

         }

         return $status;


     } // end fw_get_sub_unit_status

     /**
      * Get the sub units in a course
      *
      * @param int $course_id Course ID of the course to get sub units from
      *
      * @return array $sub_unit_list return array of sub units
      *
      */
     public function fw_get_course_sub_units($course_id = null) {

         if (!$course_id) {
             $user_id = get_current_user_id();
             $course_id = get_user_meta($user_id,'fw_current_course',true);

         }

         $args = array(
             'numberposts' => -1,
             'post_type' => 'lesson',
             'meta_query' => array(
                 array(
                     'key' => '_lesson_course',
                     'value' => $course_id
                 ),
                 // Only get those with a year
                 'unique_key' => array(
                     'key' => 'fw_unique_key',
                     'compare' => 'EXISTS',
                 ),
             ),
             'orderby'    => array(

                 'unique_key' => 'ASC'
             ),


         );

         // Using just 'get_posts' returns an empty array for some reason
         $sub_unit_list = get_posts($args);



         return $sub_unit_list;


     } // end fw_get_course_sub_units


 } // end FundaWande_Lessons