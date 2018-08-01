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

        // Create switch case depending on the $language
        // Add language prefix to $language_obj
        // Add language url suffix to $language_obj
        // Add language identifier to $language_obj
        switch ($language) {
            case 'xho':
                $language_obj->prefix = 'xho-' ;
                $language_obj->url_param = 'lang=xho';
                $language_obj->id = 'xho';
                break;
            case 'eng':
                $language_obj->prefix = 'eng-' ;
                $language_obj->url_param = 'lang=eng';
                $language_obj->id = 'eng';
                break;
            // Add default just as a catch all
            default:
                $language_obj->prefix = 'xho-' ;
                $language_obj->url_param = 'lang=xho';
                $language_obj->id = 'xho';
        }

        // return $language_obj
        return $language_obj;


    } // end get_language()

} // end FundaWande_Language
