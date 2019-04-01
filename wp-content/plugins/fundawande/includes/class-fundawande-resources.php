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
        add_action( 'admin_enqueue_scripts', array($this, 'load_resource_cpt_scripts'), 10, 1 );
        
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

    }

    function load_resource_cpt_scripts( $hook ) {
        global $typenow;
		if( $typenow == 'resource' ) {
			wp_enqueue_media();
            // Registers and enqueues the required javascript.
            wp_enqueue_script('resource-metabox-media', FundaWande()->plugin_url . 'assets/js/media-uploader.min.js', array('jquery'), FundaWande()->version, true);
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
            'has_archive' => true,
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
        
        /** 
         * Add categories for the post type
         */
        register_taxonomy( 'categories', array('resource'), array(
            'hierarchical' => true, 
            'label' => 'Categories', 
            'singular_label' => 'Category',
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true, 
            'query_var' => true,
            'rewrite' => array( 'slug' => 'categories', 'with_front'=> false )
            )
        );
    
        register_taxonomy_for_object_type( 'categories', 'Resources' );

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
        $cols = array(
            'cb'       => '<input type="checkbox" />',
            'title'    => 'Title',
            'type'     => 'Type',
            'media'    => 'Media',
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
	 * @since  1.1.2
	 * @param  string $column_name
	 * @param  int $id
	 * @return void
	 */
	public function add_column_data ( $column_name, $post_id ) {

		switch ( $column_name ) {
			case 'type':
                echo get_post_meta($post_id, 'resource_type', true);
            break;

            case 'media':
                if(get_post_meta($post_id, 'resource_type', true) == 'Video') {
                    echo get_post_meta($post_id, 'video_media', true);
                }
                else {
                    echo get_post_meta($post_id, 'pdf_media', true);
                }
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
     */
    function resource_type_add_meta_box ( $post ){
        add_meta_box( 'type_meta_box', 'Resource Type', array($this,'resource_type_build_meta_box'), 'resource', 'normal', 'low' );

    }

    function resource_type_build_meta_box ( $post ) {
        ?>
        
        <div class="resource-metabox">
            <?php $resource_type = get_post_meta($post->ID, 'resource_type', true) ?>
            <p>
                <label for="resource_type">Resource Type</label><br>
                <select name="resource_type" id="resource_type" class="postbox">
                    <option value="Video" <?php selected($resource_type, 'Video'); ?>>Video</option>
                    <option value="PDF" <?php selected($resource_type, 'PDF'); ?>>PDF</option>
                </select>
            </p>
        </div>

        <?php
    } // end resource_type_build_meta_box()

    function resource_type_save_meta($post_id, $post) {

        // Return if the user doesn't have edit permissions.
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
        }
        
        $resource_type_meta['type'] = esc_textarea( $_POST['resource_type'] );

        // Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
        }
        
        if ( get_post_meta( $post_id, 'resource_type', false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, 'resource_type', $resource_type_meta['type'] );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, 'resource_type', $resource_type_meta['type']);
		}
		if ( ! $resource_type_meta['type'] ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, 'resource_type' );
        }
        
    } // end resource_type_save_meta()
    

    /**
     * Add Resource details meta box
     *
     * @param post $post The post object
     * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
     */
    function resource_details_add_meta_box ( $post ){
        add_meta_box( 'details_meta_box', 'Resource Details', array($this,'resource_details_build_meta_box'), 'resource', 'normal', 'low' );

    }

    function resource_details_build_meta_box ( $post ) {
        ?>

        <div class="resource-metabox">

            <!-- Video Resource Fields -->
            <div class="resource-fields" id="resource-video">
                <?php $video_file_name = get_post_meta($post->ID, 'video_file_name', true) ?>
                <div>
                    <label for="video_file_name">Video File Name:</label><br>
                    <input name="video_file_name" id="video_file_name" value="<?php esc_textarea( $video_file_name ) ?>" type="text" class="widefat">
                </div>

                <?php $saved_video = get_post_meta($post->ID, 'video_media', true) ?>
                <div>
                    <label for="video_file">Holding Image:</label><br>
                    <input type="url" class="large-text" name="video_media" id="video_media" type="button" value="<?php echo esc_attr($saved_video) ?>" readonly><br>
                    <button type="button" class="button" id="video_upload_btn" data-media-uploader-target="#video_media">Upload Holding Image</button>
                </div>
                <br>

                <?php $video_description = get_post_meta($post->ID, 'video_description', true) ?>
                <div>
                    <label for='video_description'>Video Description:</label><br>
                    <textarea name="video_description" id="video_description" value="<?php esc_textarea( $video_description ) ?>"  rows="5"></textarea>
                </div>
            </div>


            <!-- PDF Resource Fields -->
            <div class="resource-fields" id="resource-pdf">
                <?php $saved_pdf = get_post_meta($post->ID, 'pdf_media', true) ?>
                <div>
                    <label for="pdf_file">PDF File:</label><br>
                    <input type="url" class="large-text" name="pdf_media" id="pdf_media" type="button" value="<?php echo esc_attr($saved_pdf) ?>" readonly><br>
                    <button type="button" class="button" id="pdf_upload_btn" data-media-uploader-target="#pdf_media">Upload PDF File</button>
                </div>
                <br>

                <?php $pdf_description = get_post_meta($post->ID, 'pdf_description', true) ?>
                <div>
                    <label>PDF Description:</label><br>
                    <textarea name="pdf_description" id="pdf_description" value="<?php esc_textarea( $pdf_description ) ?>"  rows="5"></textarea>
                </div>
            </div>
        </div>

        <?php
    }

    function resource_details_save_meta($post_id, $post) {

        // Return if the user doesn't have edit permissions.
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
        }
        
        $resource_details_meta['video_file_name'] = esc_textarea( $_POST['video_file_name'] );
        $resource_details_meta['video_media'] = esc_textarea( $_POST['video_media'] );
        $resource_details_meta['video_description'] = esc_textarea( $_POST['video_description'] );
        $resource_details_meta['pdf_media'] = esc_textarea( $_POST['pdf_media'] );
        $resource_details_meta['pdf_description'] = esc_textarea( $_POST['pdf_description'] );

        foreach ($resource_details_meta as $key => $value) {
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
        }
        
    } // end resource_type_save_meta()
 



} // end FundaWande_Resources
