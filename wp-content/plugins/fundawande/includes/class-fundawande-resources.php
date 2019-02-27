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

        if ( is_admin() ) {
            add_filter( 'manage_edit-resource_columns', array( $this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'add_column_data' ), 10, 2 );
        }
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

    /**
	 * Add column headings to the "resource" post list screen.
	 * @access public
	 * @since  1.1.2
	 * @param  array $defaults
	 * @return array $new_columns
	 */
	public function add_column_headings ( $defaults ) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = 'Title';
		$new_columns['resource-type'] = 'Type';
		if ( isset( $defaults['date'] ) ) {
			$new_columns['date'] = $defaults['date'];
		}

		return $new_columns;
	} // End add_column_headings()

	/**
	 * Add data for the newly-added custom columns.
	 * @access public
	 * @since  1.1.2
	 * @param  string $column_name
	 * @param  int $id
	 * @return void
	 */
	public function add_column_data ( $column_name, $id ) {
		global $wpdb, $post;

		switch ( $column_name ) {

			case 'id':
				echo $id;
			break;

			case 'resource-type':
				$resource_type = strip_tags( get_the_term_list( $id, 'resource-type', '', ', ', '' ) );
				$output = '&mdash;';
				if( isset( $this->resource_types[ $resource_type ] ) ) {
					$output = $this->resource_types[ $resource_type ];
				}
				echo $output;
			break;

			default:
			break;

		}

	} // End add_column_data()


} // end FundaWande_Resources
