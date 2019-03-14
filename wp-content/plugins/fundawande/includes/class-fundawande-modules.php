<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Modules Class
 *
 * All functionality pertaining to the custom modules functionality of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Modules {

    /**
     * Constructor
     */
    public function __construct() {

        // Add the construct elements
        add_action( 'edit_term', array($this,'handle_module_unit_edit'), 10, 3 );
        add_action( 'create_module_key_after_ordering', array($this,'handle_module_unit_ordering'), 10, 1 );

    }

    /**
     * Get course parent modules for display on the course page.
     *
     * @param integer $course_id. The course ID to fetch the modules for
     *
     * @return object $course_modules
     */
    public function get_course_modules($course_id) {

        $course_modules = Sensei()->modules->get_course_modules($course_id);
        
        //Module numbering starts at 0 in the FW courses
        $course_module_number = 0;

        foreach($course_modules  as $key => $module) {

            // if there is a module parent then it's a child module (unit)
            if ($module->parent) {

                // Unset the child module from array
                unset($course_modules[$key]);

            } else {
                // Get the hide module variable to determine whether to show module in course
                $hide_module = get_term_meta($module->term_id, 'hide_module', true);
                // if the hide_module is true, then unset it from course
                if ($hide_module) {
                    unset($course_modules[$key]);
                    continue;
                }
                // Get the term data in case there are custom fields
                $course_modules[$key]->meta = get_term_meta($module->term_id);
                $module_units = get_term_children( $module->term_id,'module');

                // Get custom module order for course
                $order = Sensei()->modules->get_course_module_order($course_id);

                if ( $order) {
                    
                    // Sort by custom order
                    $ordered_units = array();
                    $unordered_units = array();
                    foreach ( $module_units as $unit ) {
                        $order_key = array_search($unit, $order);
                        if ($order_key !== false) {
                            $ordered_units[$order_key] = $unit;
                        } else {
                            $unordered_units[] = $unit;
                        }
                    }

                    // Order modules correctly
                    ksort( $ordered_units );

                    $module_units = $ordered_units;
                }


                // If the module number meta does not exist or is different to
                //  $course_module_number, change the module number meta to be the same as course_module_number 
                $module_number_meta = get_term_meta($module->term_id, 'module_number', true);
                
                // get the module unique key
                $module_key = get_term_meta($module->term_id, 'fw_unique_key', true);

                // check if user exists
                $user_id = get_current_user_id();

                // get user current module
                $user_current_module = get_user_meta($user_id,'fw_current_module',true);

                // check if this unit key is equal to current unit key and if so, assign current property
                if ($module_key == $user_current_module) {

                    $course_modules[$key]->current = true;
                    // $course_modules[$key]->current = false;
                }


                if (!isset($module_number_meta) || $module_number_meta !== $course_module_number) {
                    update_term_meta($module->term_id,'module_number',$course_module_number);
                    $course_modules[$key]->module_number = $course_module_number;
                }
                $course_modules[$key]->link = get_term_link($module->term_id);
                $course_modules[$key]->complete = $this->fw_is_module_complete($module->term_id,$user_id);
                $course_modules[$key]->units = array();
                foreach($module_units  as $key2 => $unit) {
                    // Get the hide unit variable to determine whether to show module in course
                    $hide_unit = get_term_meta($unit, 'hide_module', true);
                    // if the hide_unit is true, then skip unit
                    if ($hide_unit) {
                        continue;
                    }

                    $unit_data = new stdClass();
                    $unit_data->ID = $unit;
                    $unit_data->complete = FundaWande()->units->fw_is_unit_complete($unit,$user_id);
                    $course_modules[$key]->units[] = $unit_data;
                }
                //Get the custom module title
                $course_modules[$key]->module_title = get_term_meta($module->term_id, 'module_title', true);
                //Get the custom module description
                $course_modules[$key]->module_description = get_term_meta($module->term_id, 'module_description', true);

                $course_module_number++;
            }
        }

        return $course_modules;

    } // end get_course_modules

    /**
     * Set module unique key after course module edit
     *
     * @param integer $term_id. The term ID being edited
     * @param integer $tt_id. The taxonomy id?
     * @param integer $taxonomy. The taxonomy being edited
     *
     */
    public function handle_module_unit_edit($term_id, $tt_id, $taxonomy) {
        // if the taxonomy being edited is not module, return.
        if ($taxonomy != 'module') {
            return __return_false();
        }

        // Get course id from module id
        $course_id = self::get_module_course($term_id);

        // If course ID exists then set the unique key
        if ($course_id) {
            self::set_module_unit_unique_key($course_id );
        }

        return true;

    } // end set_module_unit_unique_key

    /**
     * Set module unique key after course module edit
     *
     * @param integer $course_id. The course ID being ordered
     *
     */
    public function set_module_unit_unique_key($course_id) {
         // Get all modules in the course
         $course_modules = Sensei()->modules->get_course_modules($course_id);
         // Get the unique key from the course
         $course_unique_key = get_post_meta($course_id,'fw_unique_key',true);
         $course_language = get_post_meta($course_id,'course_language',true);
 
         
         // Module numbering starts at 0 in the FW courses so we need to start at -1 to allow first increment 
         $course_module_number = -1;
         // unit numbering starts at 1
         $course_unit_number = 1;

         // Set up empty array for ordered module IDs to set course order
         $ordered_module_ids = array();
         // loop through the course modules
         foreach($course_modules  as $key => $module) {

            $ordered_module_ids[] = $module->term_id;
 
             // if there is a module parent then it's a child module (unit)
             if ($module->parent) {
                 
                 // Set the unique key programmatically
                 $module->key = $unique_key = sprintf("%s_m%02d_u%02d",$course_unique_key,$course_module_number, $course_unit_number);
                 update_term_meta($module->term_id,'fw_unique_key',$unique_key);
                 $module_title = get_term_meta($module->term_id,'module_title',true);
                 $name = sprintf("%s_m%02d_u%02d_%s | %s",$course_unique_key,$course_module_number, $course_unit_number,$course_language,$module_title);
                 global $wpdb;
                 $wpdb->query( $wpdb->prepare( "
                     UPDATE `wp_terms` SET name = %s
                     WHERE term_id = %d;
                 ", [
                    $name,
                     $module->term_id
                 ] ) );
                 // increment the unit number
                 $course_unit_number++;
 
             } else {
                // Set the unique key programmatically
                // reset unit numbering to 1 as we are in a new module
                $course_module_number++;
                $course_unit_number = 1;
                $module->key = $unique_key = sprintf("%s_m%02d",$course_unique_key,$course_module_number);
                update_term_meta($module->term_id,'fw_unique_key', $unique_key);
                $module_title = get_term_meta($module->term_id,'module_title',true);
                 $name = sprintf("%s_m%02d_%s | %s",$course_unique_key,$course_module_number,$course_language,$module_title);
                 global $wpdb;
                 $wpdb->query( $wpdb->prepare( "
                     UPDATE `wp_terms` SET name = %s
                     WHERE term_id = %d;
                 ", [
                    $name,
                     $module->term_id
                 ] ) );
                // increment the module number
             }
            

        }
        $order = update_post_meta(intval($course_id), '_module_order', $ordered_module_ids);

    }

    /**
     * Set module unique key after course module ordering
     *
     * @param integer $course_id. The course ID being ordered
     *
     */
    public function handle_module_unit_ordering($course_id) {
        // if the course Id doesn't exist, return.
        if (!$course_id) {
            return __return_false();
        }

        self::set_module_unit_unique_key($course_id );

    } // end set_module_unit_unique_key_after_order

    /**
     * Get module units and their lessons based on the parent module ID
     *
     * @param integer $module_id. The module ID to fetch the units for
     *
     * @return object $module_units
     *
     */
    public function get_module_units($module_id,$course_id) {


        // Get the module children units
        $module_units = get_term_children( $module_id, 'module' );


        // Get custom module order for course
        $order = Sensei()->modules->get_course_module_order($course_id);

        if ( $order) {
            
            // Sort by custom order
            $ordered_units = array();
            $unordered_units = array();
            foreach ( $module_units as $unit ) {
                $order_key = array_search($unit, $order);
                if ($order_key !== false) {
                    $ordered_units[$order_key] = $unit;
                } else {
                    $unordered_units[] = $unit;
                }
            }

            // Order modules correctly
            ksort( $ordered_units );
           
            $module_units = $ordered_units;
        }

        foreach($module_units  as $key => $unit) {

            // Get the hide unit variable to determine whether to show module in course
            $hide_unit = get_term_meta($unit, 'hide_module', true);
            
            // if the hide_unit is true, then skip unit
            if ($hide_unit) {
                unset($module_units[$key]);
                continue;
            }
    
            $module_units[$key] = new TimberTerm($unit);

            // Get the term data in case there are custom fields
            $module_units[$key]->meta = get_term_meta($unit);
            
            // Get the unit lessons for display within the module
            $module_units[$key]->lessons = FundaWande()->lessons->get_lessons($course_id, $unit);
            
            $module_units[$key]->lesson_count = count($module_units[$key]->lessons);
           
            // get the unit unique key  
            $unit_key = get_term_meta($module_units[$key]->term_id, 'fw_unique_key', true);

            // check if user exists
            $user_id = get_current_user_id();

            // check if unit is complete
            $module_units[$key]->complete = FundaWande()->units->fw_is_unit_complete($module_units[$key]->term_id,$user_id);


            // get user current unit
            $user_current_unit = get_user_meta($user_id,'fw_current_unit',true);

            // check if this unit key is equal to current unit key and if so, aassign current proporty
            if ($unit_key == $user_current_unit) {
                // TODO change after
//                $module_units[$key]->current = true;
                $module_units[$key]->current = false;
            }


        }
        


        return $module_units;

    } // end get_module_units


    /**
     * Module progress functionality off of a given unit ID
     *
     * @return $module_progress return the module progress percent
     *
     */
    public function fw_module_progress($module_id) {
        $module_units = get_term_children($module_id, 'module' );

        $completed = 0;
        $total = 0;
        foreach ($module_units as $module_unit) {
            $total++;
            if (FundaWande()->units->fw_is_unit_complete($module_unit)) {
                $completed = $total;
            }
        }

        if ($total > 0) {
            $module_progress = ($completed/$total) * 100;
        }


        if ($module_progress == 100) {
            $user_id = get_current_user_id();
            $this->fw_complete_module($module_id,$user_id);
        }
        return $module_progress;


    } // end fw_module_progress_at_unit



    /**
     * Module progress functionality off of a given module ID
     *
     * @return $module_progress return the module progress percent
     *
     */
    public function fw_is_module_complete($module_id,$user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Determine if an existing unit status exists
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_module_progress',
            'user_id' => $user_id,
            'status' => $module_id,
        );

        // possibly returns array, we just want one object
        $user_module_status = get_comments($current_status_args);
        if ($user_module_status) {
            return true;

        }
        return false;
    } // end fw_is_module_complete

    /**
     * Complete unit functionality to track a unit as complete
     *
     * @return $comment_id return the comment ID of the completed progress indicator
     */
    public function fw_complete_module($module_id, $user_id) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Determine if an existing unit status exists
        $current_status_args = array(
            'number' => 1,
            'type' => 'fw_module_progress',
            'user_id' => $user_id,
            'status' => $module_id,
        );

        // possibly returns array, we just want one object
        $user_module_status = get_comments($current_status_args);
        if (is_array($user_module_status) && 1 == count($user_module_status)) {
            $user_module_status = array_shift($user_module_status);

        }

        // If no current review then return the review form object with user and post details
        if (empty($user_module_status)) {
            $time = current_time('mysql');
            $user = $user = get_userdata($user_id);
            $data = array(
                'comment_type' => 'fw_module_progress',
                'user_id' => $user_id,
                'comment_date' => $time,
                'comment_approved' => $module_id,
                'comment_karma' => 1,
                'comment_author' => $user->display_name,
                'comment_author_email' => $user->user_email

            );

            $comment_id = wp_insert_comment($data);

        } else {

            $comment_id = $user_module_status->comment_ID;
        }

        return $comment_id;


    } // end fw_complete_module



    /**
     * Get and set the current Module  based off a user
     *
     * @param int $user_id. The ID for the current user
     * @param object $current_unit. The object of the current unit
     *
     * @return string $unit return the unit object of the current Unit
     */
    public function fw_get_set_current_module($user_id = null, $current_unit = null) {

        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // If current unit isn't set then get it
        if (!$current_unit) {
            // Get the current unit object from from the user
            $current_unit = $this->fw_get_set_current_unit($user_id);
        }

        // Get the current module ID
        $current_module_id = $current_unit->parent;

        // et the current module object
        $current_module = new TimberTerm($current_module_id);

        // add the unit key to object
        $current_module->key = get_term_meta($current_module->term_id, 'fw_unique_key',true);

        // update the current unit off of the key in case they have become misaligned
        update_user_meta($user_id,'fw_current_module',$current_module->key);

        return $current_module;

    }


    /**
     * Get the course ID for a given module
     *
     * @param int $module_id. The ID for the module
     *
     * @return int $course_id return the course ID that contains the module
     */
    public function get_module_course($module_id) {

        // get all the courses in the system
        $courses = Sensei()->course->get_all_courses();

        // loop through to courses to find the first (only) one with the module in it
		foreach ($courses as $course) {
			if (has_term($module_id, 'module', $course->ID)) {
                return $course->ID;
			}
        }
        return false;
    }

} // end FundaWande_Modules
