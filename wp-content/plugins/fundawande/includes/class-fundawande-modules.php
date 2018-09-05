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

            // if there is a module parent then it's a child module (unit(_
            if ($module->parent) {

                // Unset the child module from array
                unset($course_modules[$key]);

            } else {
                // Get the term data in case there are custom fields
                $course_modules[$key]->meta = get_term_meta($module->term_id);
                // If the module number meta does not exist or is different to  
                //  $course_module_number, change the module number meta to be the same as course_module_number 
                $module_number_meta = get_term_meta($module->term_id, 'module_number');

                if (!isset($module_number_meta) || $module_number_meta !== $course_module_number) {
                    update_term_meta($module->term_id,'module_number',$course_module_number);
                    $course_modules[$key]->module_number = $course_module_number;
                }
                $course_modules[$key]->link = get_term_link($module->term_id);

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

            // Get the module number for the parent module so that module-specfic styling works
            $module_units[$key]->module_number = implode("", get_term_meta($module_id, 'module_number'));

            // Get the term data in case there are custom fields
            $module_units[$key]->meta = get_term_meta($unit);

            // Get the unit lessons for display within the module
            $module_units[$key]->lessons = Sensei()->modules->get_lessons($course_id, $unit);


        }

        return $module_units;

    } // end get_course_modules

} // end FundaWande_Modules
