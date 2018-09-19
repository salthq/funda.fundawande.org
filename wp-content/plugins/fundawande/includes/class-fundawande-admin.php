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
        add_action('acf/input/admin_head', array($this, 'fw_remove_media_buttons'));

	} // End __construct()

    /**
     * Enqueue admin scripts.
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_script('theme-admin-script', FundaWande()->plugin_url . 'assets/js/admin.min.js', array('jquery'), FundaWande()->version, true);
    }

    /**
     * Remove add media buttons from ACF
     */
    function fw_remove_media_buttons(){
        remove_action( 'media_buttons', 'media_buttons' );
     }
    
     

} // End FundaWande_Admin Class
