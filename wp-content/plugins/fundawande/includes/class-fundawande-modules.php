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
                // Get the term data in case there are custom fields
                $course_modules[$key]->meta = get_term_meta($module->term_id);
                $module_units = get_term_children( $module->term_id,'module');
                error_log(print_r($course_modules[$key]->units,true));
                // If the module number meta does not exist or is different to
                //  $course_module_number, change the module number meta to be the same as course_module_number 
                $module_number_meta = get_term_meta($module->term_id, 'module_number', true);


                if (!isset($module_number_meta) || $module_number_meta !== $course_module_number) {
                    update_term_meta($module->term_id,'module_number',$course_module_number);
                    $course_modules[$key]->module_number = $course_module_number;
                }
                $course_modules[$key]->link = get_term_link($module->term_id);

                foreach($module_units  as $key2 => $unit) {
                    $course_modules[$key]->units = array();
                    $unit_data = new stdClass();
                    $unit_data->ID = $unit;
                    $unit_data->complete = FundaWande()->lms->fw_is_unit_complete($unit);
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

        foreach($module_units  as $key => $unit) {

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

            // get user current unit
            $user_current_unit = get_user_meta($user_id,'fw_current_unit',true);

            // check if this unit key is equal to current unit key and if so, aassign current proporty
            if ($unit_key == $user_current_unit) {
                $module_units[$key]->current = true;
            }


        }

        return $module_units;

    } // end get_course_modules


} // end FundaWande_Modules
