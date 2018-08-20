<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Quiz Class
 *
 * All functionality pertaining to the custom quiz functionality of Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Quiz {

    /**
     * Constructor
     */
    public function __construct() {

        // Load single quiz content template
        add_action('fundawande_single_quiz_content', array($this, 'load_single_quiz_template'), 8,1);

    }
    /**
     *  Add a notice to the array of pastpapers for display at a later stage.
     *
     * @param array $language. This is the language variable as set by the url on each page via url parameters
     *
     * @return object $language_obj
     */
    public function load_single_quiz_template() {

        require(ABSPATH . 'wp-content/plugins/fundawande/templates/single-quiz.php');


    } // end load_single_quiz_template()

} // end FundaWande_Quiz
