<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * FW Cohorts User Import Class
 *
 * All admin functionality pertaining to importing user cohorts in FW.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Cohorts_Admin_Import {

	/**
	 * Constructor
	 * @since  1.0.0
	 */
	public function __construct () {
		global $wpdb;

		// Setup construct
        
		add_action( 'admin_enqueue_scripts', array($this,'enqueue_import_users_styles' ));

		add_action( 'admin_menu', array($this,'register_users_menu_page' ));

		add_action( 'wp_ajax_import_users_from_file', array($this,'import_users_from_file' ));

	} // End __construct()

    

	/**
	 * Register a custom menu page.
	 */
	function register_users_menu_page(){
		
		$page_hook_suffix = add_submenu_page( 
			'cohorts_admin',
			'Import User Cohorts Admin',
			'Import user cohorts',
			'manage_options',
			'cohorts_user_import',
			array($this,'import_users_menu_page')
	
		); 

	}

	function enqueue_import_users_styles($hook) {
		if ($hook != 'cohorts_page_cohorts_user_import') {
			return;
		}

		wp_enqueue_script( 'user-cohorts-import-js', plugins_url( 'assets/js/user_cohorts_import.min.js' , dirname(__FILE__) ),array('jquery') );
		wp_localize_script('user-cohorts-import-js', 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
		
	}
	
	/**
	 * AJAX import units incrementally from file
	 */
	function import_users_from_file() {
		
		// Get the start position of batch
		$start = (int) $_POST['start_position'];
		// get the import data
		$users = $_POST['users'];
		// decode the import data
		$users_array = json_decode(stripslashes($users));
		// set initial position 
		$position = $start;
		// Set up empty return object
		$return_object = new stdClass();
		$return_object->log = '<ul>';
		for ($i = $start; $i < ($start + 10); $i++) {
			// If the row has data
			if ($users_array[$i]) {
				// Set empty user ID
				$user_id='';
				// If the row has no user ID then try create user
				if (!$users_array[$i]->user_id) {
					// No user ID
					
					$return_object->log .= "<li>Invalid user and details for row #".($i + 1)."</li>"; 
					
				} else { // Else if user ID exists then assign
					// Check if user actually exists
					$user = get_user_by( 'ID', $users_array[$i]->user_id );
					if ($user) { // If they exists then assign user ID
						$user_id = $users_array[$i]->user_id;
						if (isset($users_array[$i]->role)) {
							// Fetch the WP_User object of our user.
							$u = new WP_User( $user_id );

							// Replace the current role with 'editor' role
							$u->set_role( $users_array[$i]->role );
						}
					} else {
						// Add log entry
						$return_object->log .= "<li>Invalid user ID #".$users_array[$i]->user_id." for row #".($i + 1)."</li>"; 
					}
				
				}

				// If there is a cohort ID set, add the user to the cohorts
				if ($users_array[$i]->cohort_id && $user_id) {
					// Add user to cohort
					FundaWande()->cohort_users_admin->add_user_to_cohorts($user_id,[$users_array[$i]->cohort_id]);
					// Set status for cohorts
					FundaWande()->cohort_users_admin->update_status_for_user_cohorts($user_id,[$users_array[$i]->cohort_id], $users_array[$i]->cohort_status);
					// Add log entry
					$return_object->log .= "<li>User #".$user_id." added to cohort #".$users_array[$i]->cohort_id."</li>"; 
				}

				// If there is a remove cohort ID set, remove the user from the cohort
				if ($users_array[$i]->remove_cohort_id && $user_id) {
					// Remove user from cohort
					FundaWande()->cohort_users_admin->remove_users_from_cohort( [$user_id], $users_array[$i]->remove_cohort_id );
					// Add log entry
					$return_object->log .= "<li>User #".$user_id." removed from cohort #".$users_array[$i]->remove_cohort_id."</li>"; 
				}
				// Set the new position
				$position = $i;
			} else { // If data empty, then set stop var to true

				$return_object->stop = true;
			}
		}
		$return_object->log .= '</ul>';

		// Set new start position
		$return_object->start = $position +1 ;
		// Return the json success obj
		wp_send_json_success($return_object);
		die;
	}

	/**
	 * Display users menu page
	 */
	function import_users_menu_page(){
		// Set up the import page
		

		?>
	
		<div class="wrap">
			<h1 class="wp-heading-inline">Import Users to LMS</h1>
			<p>Please upload a .csv file to import Users. They will be imported 10 at a time - please wait for the alert to confirm success.</p>
			<p><b>Columns and their descriptions</b></p>
			<ul>
			<li>user_id - The user ID if they exist</li>
			<li>cohort_id - The cohort ID you want to add the user to</li>
			<li>cohort_status - The cohort status [active ; in-active]</li>
			<li>remove_cohort_id - The cohort ID you want to remove the user from</li>
			</ul>

		</div> 
		
		<div id="upload-wrapper">
			<input type="file" id="fileUpload" />
			
			<input type="button" id="upload" class="button button-primary button-large" value="Upload" />
			<div class="spinner" style="float:none"></div>
			<hr />
			<div id="dvCSV">
			</div>
		</div>

		<div id="import-log">
		</div>
		<?php
	}


} // FundaWande_Cohorts_Admin_Import
