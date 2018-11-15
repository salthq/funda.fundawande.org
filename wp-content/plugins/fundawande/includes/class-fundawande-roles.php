<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * FundaWande Roles Class
 *
 * This class handles the various roles available on Funda Wande
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Roles
{
    /**
	 * Constructor.
	 * @since  1.0.0
	 */
    public function __construct()
    {
        //Remove editor capabilities
        add_action('init', array($this, 'fw_remove_editor_caps'));
    } // end __construct()

    /**
     * Remove access to pages or comments for Funda Wande editors 
     */
    function fw_remove_editor_caps()
    {

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

        foreach ($caps as $cap) {
            //Remove the capability
            $editor->remove_cap($cap);
        }
    } // end fw_remove_editor_caps()

} // end FundaWande_Roles 