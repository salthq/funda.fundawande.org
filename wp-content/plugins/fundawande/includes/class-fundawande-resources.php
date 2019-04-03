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

        // Set up resource post type
        add_action( 'init', array( $this, 'setup_resource_post_type' ), 100 );
        // Load any scripts needed for the resource post type
        add_action( 'admin_enqueue_scripts', array($this, 'load_resource_cpt_scripts'), 10, 1 );
        
        // Manage Public Resources table columns
        if ( is_admin() ) {
            add_filter( 'manage_edit-resource_columns', array( $this, 'add_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'add_column_data' ), 10, 2 );
        }

        // Add Resource Type metabox
        add_action( 'add_meta_boxes_resource', array($this, 'resource_type_add_meta_box' ));
        // Save Resource Type meta
        add_action('save_post', array($this, 'resource_type_save_meta'), 1, 2);
        // Add Resource Details metabox
        add_action( 'add_meta_boxes_resource', array($this, 'resource_details_add_meta_box' ));
        // Save Resource Type meta
        add_action('save_post', array($this, 'resource_details_save_meta'), 1, 2);

        // Set up callback function for category filter AJAX request
        add_action('FUNDAWANDE_AJAX_HANDLER_resource_filter_ajax_request', array($this,'resource_filter_ajax_request'));
		add_action('FUNDAWANDE_AJAX_HANDLER_nopriv_resource_filter_ajax_request', array($this,'resource_filter_ajax_request'));

    }

    /**
     * Load any scripts needed for the resource post type
     */
    function load_resource_cpt_scripts( $hook ) {
        global $typenow;
		if( $typenow == 'resource' ) {
			wp_enqueue_media();
            // Registers and enqueues the required javascript.
            wp_enqueue_script('resource-metabox-media', FundaWande()->plugin_url . 'assets/js/media-uploader.min.js', array('jquery'), FundaWande()->version, true);
		}
    } // end load_resource_cpt_scripts()

    /**
	 * Setup the "resource" custom post type
	 * @since  1.1.6
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
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
		    'supports' => array( 'title', 'custom-fields' )
        );

        /**
         * Register the Resource post type
         * @since 1.1.6
         */
        register_post_type( 'resource', $args );
        
        /** 
         * Add categories for the Resource post type
         * @since 1.1.6
         */
        register_taxonomy( 'resource-categories', array('resource'), array(
            'hierarchical' => true, 
            'label' => 'Categories', 
            'singular_label' => 'Category',
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true, 
            'query_var' => true,
            'rewrite' => array( 'slug' => 'categories', 'with_front'=> true )
            )
        );
    
        register_taxonomy_for_object_type( 'resource-categories', 'Resources' );

    } // End setup_resource_post_type()

    /**
	 * Add column headings to the "resource" post list screen.
	 * @access public
	 * @since  1.1.6
	 * @param  array $defaults
	 * @return array $cols
	 */
	public function add_column_headings ( $defaults ) {
        $cols = array(
            'cb'       => '<input type="checkbox" />',
            'title'    => 'Title',
            'type'     => 'Type',
            'category'    => 'Categories',
            'description' => 'Description'
          );

        if ( isset( $defaults['date'] ) ) {
		    $cols['date'] = $defaults['date'];
        }   
               
        return $cols;
	} // End add_column_headings()

	/**
	 * Add data for the newly-added custom columns.
	 * @access public
	 * @since  1.1.6
	 * @param  string $column_name
	 * @param  int $post_id
	 * @return void
	 */
	public function add_column_data ( $column_name, $post_id ) {

		switch ( $column_name ) {
			case 'type':
                echo get_post_meta($post_id, 'resource_type', true);
            break;

            case 'category':
                $output = strip_tags( get_the_term_list( $post_id, 'resource-categories', '', ', ', '' ) );
                if( ! $output ) {
                    $output = '&mdash;';
                }
                echo $output;
            break;
            
            case 'description':
                if(get_post_meta($post_id, 'resource_type', true) == 'Video') {
                    echo get_post_meta($post_id, 'video_description', true);
                }
                else {
                    echo get_post_meta($post_id, 'pdf_description', true);
                }
            break;

			default:
			break;

		}

    } // End add_column_data()

    /**
     * Add Resource Type meta box
     * @since 1.1.6
     */
    function resource_type_add_meta_box ( $post ){

        add_meta_box( 'type_meta_box', 'Resource Type', array($this,'resource_type_build_meta_box'), 'resource', 'normal', 'low' );

    } // end resource_type_add_meta_box()


    /**
     * Build Resource type meta box
     * @since 1.1.6
     */
    function resource_type_build_meta_box ( $post ) {

        include_once(FundaWande()->plugin_path . 'templates/resource-type-metabox.php');
        
    } // end resource_type_build_meta_box()

    /**
     * Save the metadata for the resource type
     * @since 1.1.6
     */
    function resource_type_save_meta($post_id, $post) {

        // Return if the user doesn't have edit permissions.
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
        }
        
        $resource_type_meta['type'] = $_POST ? esc_textarea( $_POST['resource_type'] ) : '';

        $this->resource_save_meta($post_id, $post, 'resource_type', $resource_type_meta['type']);

        
    } // end resource_type_save_meta()
    

    /**
     * Add Resource details meta box
     *
     * @param post $post
     * @since 1.1.6
     */
    function resource_details_add_meta_box ( $post ){

        add_meta_box( 'details_meta_box', 'Resource Details', array($this,'resource_details_build_meta_box'), 'resource', 'normal', 'low' );

    } // end resource_details_add_meta_box()

    /**
     * Build Resource details meta box
     * @since 1.1.6
     */
    function resource_details_build_meta_box ( $post ) {

        include_once(FundaWande()->plugin_path . 'templates/resource-details-metabox.php');

    } // end resource_details_build_meta_box()


    /**
     * Save Resource details metabox data
     * @since 1.1.6
     */
    function resource_details_save_meta($post_id, $post) {

        // Return if the user doesn't have edit permissions.
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
        }
        
        $resource_details_meta['video_file_name'] = $_POST ? esc_textarea( $_POST['video_file_name'] ) : '';
        $resource_details_meta['video_media'] =  $_POST ? esc_textarea( $_POST['video_media'] ) : '';
        $resource_details_meta['video_description'] =  $_POST ? esc_textarea( $_POST['video_description'] ) : '';
        $resource_details_meta['pdf_media'] = $_POST ? esc_textarea( $_POST['pdf_media'] ) : '';
        $resource_details_meta['pdf_description'] = $_POST ? esc_textarea( $_POST['pdf_description'] ) : '';

        foreach ($resource_details_meta as $key => $value) {
            $this->resource_save_meta($post_id, $post, $key, $value);
        }
        
    } // end resource_details_save_meta()


    /** 
     * Save the post meta once 'update' or 'published' is clicked.
     * @since 1.1.6
     */
    public function resource_save_meta($post_id, $post, $key, $value) {
        // Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
        }
        
        if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}
		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
        }
    } // end resource_save_meta()

    /**
     * Gets the category from an ajax call made on template-public-resources.twig and renders a new list of items which
     * belong to that category
     * 
     * @since 1.1.6
     */
    function resource_filter_ajax_request() {

        if (isset($_REQUEST)) {
            $cat = $_REQUEST['category'];

            switch ($cat) {
                case 'all-categories':
                    $args = array(
                        'post_type' => 'resource',
                        'numberposts' => -1,
                    );
                    break;   
                default:
                    $args = array(
                        'post_type' => 'resource',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'resource-categories',
                                'field' => 'slug',
                                'terms' => $cat,
                                'include_children' => true,
                                'operator' => 'IN',
                            ),
                        ),
                    );
                    break;
            }
            
    
        }

        $context['media_url'] = FundaWande()->lms->fw_get_media_url();
        $context['filtered_resources'] =  Timber::get_posts($args);
    
        Timber::render(array('template-resource-item.twig', 'page.twig'), $context);
    
    
        die();
    } // end resource_filter_ajax_request()

} // end FundaWande_Resources
