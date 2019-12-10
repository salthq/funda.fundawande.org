<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Funda Wande Cohorts Admin Class
 *
 * All admin functionality pertaining to the cohorts in Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.3.1
 */

 class FundaWande_Cohorts_Admin {
     public $cohorts_table;

     const ADD_EDIT_COHORT_PAGE = 'add_edit_cohort';
     const COHORTS_ADMIN_PAGE = 'cohorts_admin';

     /**
      * Constructor
      * @since 1.3.1
      */
    public function __construct() {
        global $wpdb;

        $this->cohorts_table = $wpdb->prefix . 'cohorts';

        register_activation_hook(FW_PLUGIN_MAIN_FILE, array($this, 'register_activation_hook'));

        add_action('admin_menu', array($this, 'register_cohorts_menu_page'));

        add_action('add_meta_boxes', array($this, 'add_edit_cohort_metaboxes') ,10,2);
    }

    /**
	 * Add the custom cohorts tables to the database on plugin activation
	 * @since  1.3.1
     */
    public function register_activation_hook() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "
			CREATE TABLE $this->cohorts_table (
				cohort_id bigint(20) NOT NULL AUTO_INCREMENT UNIQUE,
				code varchar(50) NOT NULL,
				title text NOT NULL,
				description longtext NOT NULL,
				categories longtext NOT NULL,
				PRIMARY KEY (cohort_id),
				INDEX id (cohort_id)
			) $charset_collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
    }
    
    /**
	 * Register a custom menu page.
     * @since  1.3.1
	 */
	function register_cohorts_menu_page(){
		add_menu_page( 
			'Cohorts Admin',
			'Cohorts',
			'manage_options',
			self::COHORTS_ADMIN_PAGE,
			array($this,'cohorts_menu_page'),
			'dashicons-share',
			50
		); 

		$page_hook_suffix = add_submenu_page( 
			'cohorts_admin',
			'Add Cohorts Admin',
			'Add/Edit cohort',
			'manage_options',
			self::ADD_EDIT_COHORT_PAGE,
			array($this,'add_edit_cohort_menu_page')
	
		); 
    }

    /**
	 * Display cohorts menu page
     * @since  1.3.1
	 */
	function cohorts_menu_page(){
        // Prepare list table.
        // TODO: Add cohorts admin table class
		// $wp_list_table = new StartupSchool_Cohorts_Admin_Table();
		// $wp_list_table->prepare_items();
		$edit_url = '/wp-admin/admin.php?page='.self::ADD_EDIT_COHORT_PAGE;

		?>
	
		<div class="wrap">
			<h1 class="wp-heading-inline">Cohorts <a class="add-new-h2" href="<?php echo $edit_url ; ?>">Add new cohort</a></h1>
			<form method="get">
				<input type="hidden" name="page" value="<?= isset( $_GET['page'] ) ? $_GET['page'] : '' ?>">
				<input type="hidden" name="order" value="<?= isset( $_GET['order'] ) ? $_GET['order'] : '' ?>">
				<?php // $wp_list_table->display(); ?>
			</form>
		</div>
		<?php
    }
    
    /**
	 * Display add cohort menu page
     * @since  1.3.1
	 */
	function add_edit_cohort_menu_page(){
		// Check if cohort id is set 
		$cohort_id = (isset( $_GET['cohort_id'])) ? (int) $_GET['cohort_id'] :'';
		
		// Check if save cohort action is set handle save cohort
		if ( isset( $_POST['cohort_action']) && ($_POST['cohort_action'] == 'save')  ) {
			$cohort_id = $this->save_cohort($_POST);
		} elseif ( isset( $_GET['cohort_action']) && ($_GET['cohort_action'] == 'delete')) {
			$cohort_deleted = FundaWande()->cohorts_utils->delete_cohorts([$cohort_id]);
			echo 'Cohort #'.$cohort_id.' deleted.';
			exit();
		}
		$view_users_url = '/wp-admin/admin.php?page=cohort_users&cohort_id='.$cohort_id;
		$title = ($cohort_id !== '') ? 'Edit Cohort #'.$cohort_id. ' <a class="add-new-h2" href="'.$view_users_url.'">View cohort users</a>' : 'Add new Cohort';
	
		/* global vars */
		global $hook_suffix;
		/* enable add_meta_boxes function in this page. */
		do_action( 'add_meta_boxes', $hook_suffix , 10, 2);
	
		?>

		<div class="wrap">

			<h2><?php echo $title; ?></h2>

			<div class="">

				<form id="add-edit-cohort-form" method="post" action="">

					<div id="poststuff">

						<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

							<div id="postbox-container-1" class="postbox-container">

								<?php do_meta_boxes( $hook_suffix, 'side', $cohort_id ); ?>
								<!-- #side-sortables -->

							</div><!-- #postbox-container-1 -->

							<div id="postbox-container-2" class="postbox-container">

								<?php do_meta_boxes( $hook_suffix, 'normal', $cohort_id ); ?>
								<!-- #normal-sortables -->

								<?php do_meta_boxes( $hook_suffix, 'advanced', $cohort_id ); ?>
								<!-- #advanced-sortables -->

							</div><!-- #postbox-container-2 -->

						</div><!-- #post-body -->

						<br class="clear">

					</div><!-- #poststuff -->

				</form>

			</div><!--  -->

		</div><!-- .wrap -->

		<?php
    } // end add_edit_cohort_menu

    
    /**
	 * Save the cohort data
     * @since  1.3.1
	 */
	function save_cohort($data){
		
		$cohort_title =  $data['cohort_title'] ? $data['cohort_title'] : '';
		$cohort_description =  $data['cohort_description']? $data['cohort_description'] : '';
		$cohort_code =  $data['cohort_code']? $data['cohort_code'] : '';
		if ( isset( $data['cohort_id']) && ($data['cohort_id'] !== '') ) {
            $cohort_id =  $data['cohort_id'];
			$cohort_id = FundaWande()->cohorts_utils->update_cohort($cohort_id, $cohort_title, $cohort_description , $cohort_code);
		} else {
			$cohort_id = FundaWande()->cohorts_utils->publish_cohort($cohort_title, $cohort_description , $cohort_code);
		}
		return $cohort_id;
		
    }
    
    /**
	 * Set up the cohort edit page metaboxes.
	 *
	 * @access public
	 * @since  1.3.1
	 */
	public function add_edit_cohort_metaboxes($post_type, $post) {
		
		add_meta_box( 'add_edit_cohort_meta_box', 'Cohort details', array($this,'add_edit_cohort_panel'), 'cohorts_page_'.self::ADD_EDIT_COHORT_PAGE , 'normal', 'high' );
		add_meta_box( 'add_edit_cohort_submit_meta_box', 'Publish', array($this,'add_edit_cohort_submit_meta_box'), 'cohorts_page_'.self::ADD_EDIT_COHORT_PAGE, 'side', 'high' );
	
    }
    
    	/**
	 * Display add cohort menu page
	 */
	function add_edit_cohort_panel ($cohort_id){ 
		
		if ( isset( $cohort_id ) && ($cohort_id !== '') ) {
			$cohort = FundaWande()->cohorts_utils->get_cohort($cohort_id);
			$cohort_title =  $cohort->title;
			$cohort_description =  $cohort->description;
			$cohort_code =  $cohort->code;
		} else {
			$cohort_title =  '';
			$cohort_description =  '';
			$cohort_code =  '';
		}
		?>
		<form id="add-cohort-form" action=""  method="post">
				
			<div class="inside">
			
				<div id="titlediv">
					<input type="text" name="cohort_title" size="30" value="<?php echo $cohort_title; ?>" id="title" spellcheck="true" autocomplete="off" placeholder="Enter cohort title">
				</div>
				<div>

					<h3><label class="" id="" for="cohort_code">Cohort description</label></h3>

					<?php 
					$settings = array(
						'textarea_rows' => 10
					);
					wp_editor( $cohort_description, 'cohort_description', $settings  ); ?> 
					</div>
				<table class="form-table ">
					<tbody>
						
						<tr>
							<td class="first"><label class="" id="" for="cohort_code">Cohort code</label></td>
							<td>
								<input type="text" id="cohort_code" name="cohort_code" value="<?php echo $cohort_code; ?>">
							</td>
						</tr>
						
					</tbody>
				</table>
				
			
			</div>		
			
		</form>
		<?php
	}


	/**
	 * Submit Meta Box Callback
	 * @since 1.3.1
	 */
	function add_edit_cohort_submit_meta_box($cohort_id) {
																
		?>
		<div id="submitpost" class="submitbox">

			<div id="major-publishing-actios">
				
				<input name="cohort_id" type="hidden" id="cohort_id" value="<?php echo $cohort_id; ?>">
				<input name="cohort_action" type="hidden" id="cohort_action" value="save">

				<div id="delete-action">
					<a href="?page=add_edit_cohort&cohort_action=delete&cohort_id=<?php echo $cohort_id; ?>" class="submitdelete deletion">Delete Cohort</a>
				</div><!-- #delete-action -->

				<div id="publishing-action">
					<span class="spinner"></span>
					<?php submit_button( esc_attr( 'Save' ), 'primary', 'submit', false );?>
				</div>

				<div class="clear"></div>

			</div><!-- #major-publishing-actions -->

		</div><!-- #submitpost -->

		<?php
	}

 } // End FundaWande_Cohorts_Admin Class
