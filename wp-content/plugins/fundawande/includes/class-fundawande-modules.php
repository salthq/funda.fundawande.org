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

        foreach($course_modules  as $key => $module) {

            // if there is a module parent then it's a child module (unit(_
            if ($module->parent) {

                // Unset the child module from array
                unset($course_modules[$key]);

            } else {
                // Get the term data in case there are custom fields
                $course_modules[$key]->meta = get_term_meta($module->ID);
            }
        }

        return $course_modules;

    } // end get_course_modules

} // end FundaWande_Modules
