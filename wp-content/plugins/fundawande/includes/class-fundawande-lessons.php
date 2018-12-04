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
                    // default current to false for now
                    // TODO turn back on
//                    $lesson->current = true;
                    $lesson->current = false;
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
     * @author cwbmuller
	 *
	 * @param integer $course_id
     * 
	 * @param integer $post_id. The ID of the lesson
     * 
	 * @return object $unit
	 */
    public function fw_get_sub_unit_meta($course_id, $post_id) {
        $meta_obj = new stdClass();
        // get the lesson nav object
        $meta_obj->nav = FundaWande()->lms->fw_get_prev_next_lessons( $post_id );

        // get the sub unit unit
        $meta_obj->unit = FundaWande()->units->fw_get_sub_unit_unit( $post_id );

        //Get the unit title
        $meta_obj->unit_title = get_term_meta($meta_obj->unit->term_id, 'module_title', true);

        //Add the lessons array to the unit info to make that info available on the single lesson template
        $meta_obj->unit_lessons = $this->get_lessons($course_id, $meta_obj->unit->term_id);
        // get the last sub unit from array
        $last_sub_unit = array_values(array_slice($meta_obj->unit_lessons, -1))[0];
        $meta_obj->is_last_in_unit = false;
        // if sub unit is last mark as last
        if ($post_id == $last_sub_unit->ID) {
            $meta_obj->is_last_in_unit = true;
            $meta_obj->next_unit = FundaWande()->units->fw_get_sub_unit_unit($meta_obj->nav->next);

        }

        // get sub unit module
        $meta_obj->module_id = $meta_obj->unit->parent;
        //Get the module number for the parent module, to enable module-specific styling
        $meta_obj->module_number = get_term_meta($meta_obj->module_id, 'module_number', true);

        // get the sub unit module title
        $meta_obj->module_title = get_term_meta($meta_obj->module_id, 'module_title', true );

        // get the module unit array
        $module_units = get_term_children($meta_obj->module_id, 'module' );
        // retrieve the last unit from array
        $last_unit = array_values(array_slice($module_units, -1))[0];
        $meta_obj->is_last_in_module = false;
        // if sub unit is last in module, mark as such
        if ($meta_obj->unit->term_id == $last_unit) {
            $meta_obj->is_last_in_module = true;
            $meta_obj->next_unit = FundaWande()->units->fw_get_sub_unit_unit($meta_obj->nav->next);
            if ($meta_obj->next_unit) {
                $meta_obj->next_module = get_term($meta_obj->next_unit->parent);
            }


        }

        return $meta_obj;

    } //end get_unit_info()


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
         $current_course_id =  FundaWande()->lms->fw_get_current_course_id($user_id);
         $current_sub_unit_key = get_user_meta($user_id,'fw_current_sub_unit',true);

         // '_lesson_course' is a user meta field for the current active course, which could be in English or Xhosa.
         // The lesson's key is the same in both courses, so this meta query matches the key to the current active course.
         $args = array(
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
                 case 1:
                     $status = true;
                     break;
                 case 0:
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
             $course_id =  FundaWande()->lms->fw_get_current_course_id($user_id);

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