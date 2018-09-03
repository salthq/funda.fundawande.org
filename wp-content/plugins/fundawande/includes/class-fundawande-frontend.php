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

        // Include review activity page assets
        if (( is_page_template('template-login.php')  )) {
            wp_enqueue_script(FundaWande()->token .'-login', FundaWande()->plugin_url . 'assets/js/login.min.js', array(), FundaWande()->version, true);
        }

        // Include sortable.js
        wp_enqueue_script('sortable-js-script', FundaWande()->plugin_url . 'assets/js/Sortable.min.js', array('jquery'), FundaWande()->version, true);

	} // End enqueue_scripts()

} // End FundaWande_Frontend Class
