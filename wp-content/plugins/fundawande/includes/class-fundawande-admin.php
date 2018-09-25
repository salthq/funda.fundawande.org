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

        //Remove editor capabilities
        add_action('init', array($this, 'fw_remove_editor_caps') );

        //Remove add media button from ACF fields
        add_action('acf/input/admin_head', array($this, 'fw_remove_media_buttons') );
    

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
     } // end fw_remove_media_buttons();

    /**
     * Remove access to pages or comments for Funda Wande editors 
    */

    function fw_remove_editor_caps() {

        //Get the global role object
        $editor = get_role('editor');

        //List of capabilities to remove
        $caps = array(
        'delete_pages',
        'delete_posts',
        'delete_others_pages',
        'delete_others_posts',
        'delete_published_pages',
        'delete_published_posts',
        'edit_pages',
        'edit_posts',
        'edit_others_pages',
        'edit_others_posts',
        'edit_private_pages',
        'edit_private_posts',
        'edit_published_pages',
        'edit_published_posts',  
        'manage_categories',
        'manage_links',
        'moderate_comments',
        'publish_pages',
        'publish_posts',
        'read_private_pages',
        'read_private_posts'
        );

        foreach($caps as $cap) {
            //Remove the capability
            $editor->remove_cap($cap);
        }
    } // end fw_remove_editor_caps();
    
     

} // End FundaWande_Admin Class
