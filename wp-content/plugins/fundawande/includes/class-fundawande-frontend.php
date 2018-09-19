<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * FundaWande Frontend Class
 *
 * All functionality pertaining to the frontend of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Frontend {

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct () {

		// Scripts and Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	} // End __construct()


	/**
	 * Enqueue frontend JavaScripts.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {


        // Include single quiz
        if (( is_singular('lesson') )) {
            wp_enqueue_script(FundaWande()->token .'-single-lesson', FundaWande()->plugin_url . 'assets/js/single-lesson.min.js', array(), FW_VER, true);
            wp_localize_script( FundaWande()->token .'-single-lesson', 'fundawande_ajax_object', array( 'ajaxurl' => FundaWande()->plugin_url . '/fundawande_ajax.php') );

        }

        // Include review activity page assets
        if (( is_page_template('template-login.php')  )) {
            wp_enqueue_script(FundaWande()->token .'-login', FundaWande()->plugin_url . 'assets/js/login.min.js', array(), FundaWande()->version, true);
        }

        // Include sortable.js
		wp_enqueue_script('sortable-js-script', FundaWande()->plugin_url . 'assets/js/sortable.min.js', array('jquery'), FundaWande()->version, true);
		
		//Include Terms and Conditions script
		if(FundaWande()->login->check_if_terms_accepted() == false) {
			wp_enqueue_script('terms-and-conditions', FundaWande()->plugin_url . 'assets/js/terms-and-conditions.min.js', array('jquery'), FundaWande()->version, true);
		}


	} // End enqueue_scripts()

} // End FundaWande_Frontend Class
