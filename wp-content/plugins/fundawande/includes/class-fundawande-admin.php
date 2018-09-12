<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * FundaWande Admin Class
 *
 * All functionality pertaining to the admin of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Admin {

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct () {
		// Scripts and Styles
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );

	} // End __construct()

    /**
     * Enqueue admin scripts.
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_script('theme-admin-script', FundaWande()->plugin_url . 'assets/js/admin.min.js', array('jquery'), FundaWande()->version, true);
    }

} // End FundaWande_Frontend Class
