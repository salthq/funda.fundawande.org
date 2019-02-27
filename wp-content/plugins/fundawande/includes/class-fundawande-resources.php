<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Resources class
 *
 * All functionality pertaining to the resource public post type
 * 
 * @package Core
 * @author Pango
 *
 * @since 1.1.2
 */
class FundaWande_Resources {

    /**
     * Constructor
     */
    public function __construct() {
        $this->resource_types = $this->fw_resource_types();
        add_action( 'init', array( $this, 'setup_resource_post_type' ), 100 );
    }

    /**
	 * Setup the "resource" custom post type
	 * @since  1.1.2
	 * @return void
	 */
	public function setup_resource_post_type () {

		$args = array(
		    'labels' => array(
                'name' => 'Resources',
                'singular_name' => 'Resource',
                'menu_name' => 'Public Resources',
                'add_new_item' => 'Add new resource',
                'edit_item' => 'Edit resource',
                'new_item' => 'New resource',
                'view_item' => 'View resource',
                'view_items' => 'View Resources',
                'search_items' => 'Search Items',
                'not_found' => 'No resources found',
                'not_found_in_trash' => 'No resources found in trash',
                'all_items' => 'All resources',
                'archives' => 'Resource Archives',
                'attributes' => 'Resource Attributes'
            ),
            'public' => true,
            'publically_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'menu_icon' => 'dashicons-portfolio',
            'has_archive' => false,
            'hierarchical' => false,
		    'supports' => array( 'title', 'custom-fields' )
		);

        /**
         * Register the Resource post type
         *
         * @since 1.1.2
         * @param array $args
         */
		register_post_type( 'resource', $args );

    } // End setup_resource_post_type()
    
    //Add Public resource types
    public function fw_resource_types() {
        $types = array(
            'public-video' => 'Public Videos',
            'public-pdf' => 'Public PDF',
            'public-article' => 'Public Article',
        );

        return apply_filters('fw_resource_types', $types);
    } // end fw_resource_types()


} // end FundaWande_Resources
