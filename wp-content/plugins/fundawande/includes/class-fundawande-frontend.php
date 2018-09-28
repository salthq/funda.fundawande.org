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

		//Walkthrough Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'fw_tour_scripts' ));

	} // End __construct()


	/**
	 * Enqueue frontend JavaScripts.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {


        // Include single quiz
        if (( is_singular('lesson') ) || ( is_singular('quiz') )) {
            wp_enqueue_script(FundaWande()->token .'-single-lesson', FundaWande()->plugin_url . 'assets/js/single-lesson.min.js', array(), FW_VER, true);
            wp_localize_script( FundaWande()->token .'-single-lesson', 'fundawande_ajax_object', array( 'ajaxurl' => FundaWande()->plugin_url . '/fundawande_ajax.php') );

        }

        // Include review activity page assets
        if (( is_page_template('template-login.php')  )) {
            wp_enqueue_script(FundaWande()->token .'-login', FundaWande()->plugin_url . 'assets/js/login.min.js', array(), FundaWande()->version, true);
        }

        // Include sortable.js
		wp_enqueue_script('sortable-js-script', FundaWande()->plugin_url . 'assets/js/sortable.min.js', array('jquery'), FundaWande()->version, true);
		
		//Include Login Modals
		wp_enqueue_script('login-modals', FundaWande()->plugin_url . 'assets/js/login-modals.min.js', array('jquery'), FundaWande()->version, true);

		//Include scroll to current script
		wp_enqueue_script('scroll-to-current', FundaWande()->plugin_url . 'assets/js/scroll-to-current.min.js', array('jquery'), FundaWande()->version, true);

	} // End enqueue_scripts()

	/**
	 * Enqueue walkthrough scripts
	 */
	public function fw_tour_scripts() {

		//Include Anno
		wp_enqueue_script('anno-script', FundaWande()->plugin_url . 'assets/js/anno.min.js', array('jquery'), FundaWande()->version, true);


		//Get the user language preference, which will be used to pull in the right script. 
		$user_id = get_current_user_id();
		$language = get_user_meta($user_id, 'language_preference', true);

		// Clicking on the 'view tooltips' button on the navbar will trigger the Anno walkthrough script.
		// The following wp_enqueue script calls are wrapped in conditionals to ensure that
		// only the right script is loaded for the page the user is currently viewing.
	
		//Include Module Page Walkthrough Script if user is on the modules page
		if( is_singular('course') ) {
			wp_enqueue_script('modules-tour-script', FundaWande()->plugin_url . 'assets/js/tour-scripts/'.$language.'-modules-tour.min.js', array('jquery'), FundaWande()->version, true);
		}

		//Include Unit Page Walkthrough Script if user is on the units page
		if( is_tax('module') ) {
			wp_enqueue_script('units-tour-script', FundaWande()->plugin_url . 'assets/js/tour-scripts/'.$language.'-units-tour.min.js', array('jquery'), FundaWande()->version, true);
		}

		//Include Lesson Page Walkthrough Script if user is on a lesson page
		if( is_singular('lesson') ) {
			wp_enqueue_script('lessons-tour-script', FundaWande()->plugin_url . 'assets/js/tour-scripts/'.$language.'-lessons-tour.min.js', array('jquery'), FundaWande()->version, true);
		}
	
	} // End fw_tour_scripts()

} // End FundaWande_Frontend Class
