<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande language Class
 *
 * All functionality pertaining to the language functionality of Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Language {

    /**
     * Constructor
     */
    public function __construct() {
        //Add action to remember user language choice
        add_action('init', array($this,'set_user_language_preference'));
    }
    /**
     *  Add a notice to the array of pastpapers for display at a later stage.
     *
     * @param array $language. This is the language variable as set by the url on each page via url parameters
     *
     * @return object $language_obj
     */
    public function get_language( $language = null ) {

        // Determine if $language was assigned, if not then assign to the default language
        if ($language === null ) {
            // Assign default language, currently Xhosa
            $language = 'xho';
        }

        // Create empty $language_obj
        $language_obj = new stdClass();


        // Default to eng for pilot
        // TODO remove this default for the language changing pilot
        $language = 'eng';


        // Create switch case depending on the $language
        // Add language prefix to $language_obj
        // Add language url suffix to $language_obj
        // Add language identifier to $language_obj
        switch ($language) {
            case 'xho':
                $language_obj->prefix = 'xho_' ;
                $language_obj->url_param = 'lang=xho';
                $language_obj->id = 'xho';
                break;
            case 'eng':
                $language_obj->prefix = 'eng_' ;
                $language_obj->url_param = 'lang=eng';
                $language_obj->id = 'eng';
                break;
            // Add default just as a catch all
            default:
                $language_obj->prefix = 'xho_' ;
                $language_obj->url_param = 'lang=xho';
                $language_obj->id = 'xho';
        }

        // return $language_obj
        return $language_obj;


    } // end get_language()

    /**
     * If the user is logged in and a get variable for the language is set, add to the user object
     */
    public function set_user_language_preference() {
        if ( is_user_logged_in()) {
            $user_id = get_current_user_id();
            $current_course_id = FundaWande()->lms->fw_get_current_course_id($user_id);

            // Default to eng for pilot
            // TODO remove this default for the language changing pilot
            $_GET['lang'] = 'eng';

            if (isset($_GET['lang'])) {
                $lang = $_GET['lang'];
                update_user_meta($user_id, 'language_preference', $lang);
                // Set the user's current course off the lang
                $current_course_id = get_field('fw_'.$lang.'_course','options',true);
                update_user_meta($user_id, 'fw_current_course', $current_course_id );
            } elseif (empty($current_course_id)) {
                $current_course_id = get_field('fw_xho_course','options',true);
                update_user_meta($user_id, 'fw_current_course', $current_course_id );
            }
            $current_sub_unit = get_user_meta($user_id, 'fw_current_sub_unit', true );
            if (empty($current_sub_unit)) {
                FundaWande()->lms->fw_set_first_sub_unit($user_id);
            }
        }




    }// end set_user_language_preference()

    /**
     * Check correct course
     *
     * @param int $user_current_course_id. User current course id
     * @param int $course_id. Course ID of course to check
     *
     */
    public function fw_correct_course_lang($user_current_course_id, $course_id) {
        if ($user_current_course_id != $course_id) {
            wp_redirect(get_permalink($user_current_course_id));
        }
        return;
    }// end fw_correct_course_lang()

    /**
     * Check correct module
     *
     * @param int $user_current_course_id. User current course id
     * @param int $module_id. Module ID of module to check
     *
     */
    public function fw_correct_module_lang($user_current_course_id, $module_id) {

        $course_modules = Sensei()->modules->get_course_modules( $user_current_course_id );
        $course_module_ids = array();
        foreach ($course_modules as $course_module) {
            $course_module_ids[] = $course_module->term_id;
        }
        if (!in_array($module_id,$course_module_ids)) {
            // get the unit unique key
            $module_key = get_term_meta($module_id, 'fw_unique_key', true);
            $args = array(
                'taxonomy'   => 'module',
                'number'  => 1,
                'hide_empty' => false,
                'include' => $course_module_ids,
                'meta_query' => array(
                    array(
                        'key'       => 'fw_unique_key',
                        'value'     => $module_key,
                        'compare'   => '='
                    )
                )
            );
            $current_course_module = get_terms($args);
            if ( is_array($current_course_module) && 1 == count($current_course_module) ) {
                $current_course_module = array_shift($current_course_module);
            }
            wp_redirect(get_term_link($current_course_module));
        }
        return;
    }// end fw_correct_module_lang()

    /**
     * Check correct lesson
     *
     * @param int $user_current_course_id. User current course id
     * @param int $lesson_id. Lesson ID of lesson to check
     *
     */
    public function fw_correct_lesson_lang($user_current_course_id, $lesson_id) {

        $course_lessons = Sensei()->course->course_lessons($user_current_course_id);

        $course_lesson_ids = array();
        foreach ($course_lessons as $course_lesson) {
            $course_lesson_ids[] = $course_lesson->ID;
        }
        if (!in_array($lesson_id,$course_lesson_ids)) {
            // get the unit unique key
            $lesson_key = get_post_meta($lesson_id, 'fw_unique_key', true);
            $args = array(
                'post_type' => 'lesson',
                'number'  => 1,
                'include' => $course_lesson_ids,
                'meta_query' => array(
                    array(
                        'key'       => 'fw_unique_key',
                        'value'     => $lesson_key,
                        'compare'   => '='
                    )
                )
            );
            $current_course_lesson = get_posts($args);
            if ( is_array($current_course_lesson) && 1 == count($current_course_lesson) ) {
                $current_course_lesson = array_shift($current_course_lesson);
            }
            wp_redirect(get_permalink($current_course_lesson));
        }
        return;
    }// end fw_correct_lesson_lang()

} // end FundaWande_Language
