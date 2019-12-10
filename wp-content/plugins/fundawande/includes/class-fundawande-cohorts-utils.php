<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Funda Wande Cohorts Admin Class
 *
 * All admin functionality pertaining to the cohorts Utils in Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.3.1
 */
class FundaWande_Cohorts_Utils {
	public $cohorts_table;
    
	/**
	 * Constructor
	 * @since  1.3.1
	 */
	public function __construct () {
		global $wpdb;

		$this->cohorts_table = $wpdb->prefix . 'cohorts';

		add_action( 'rest_api_init', array( $this,'cohorts_register_route'));
		
	} // End __construct()


	 /**
	 * Add the Rest API routes for cohorts
     * @since 1.3.1
     */
    public function cohorts_register_route() {
		
		register_rest_route( 'fw', 'cohorts/(?P<username>[a-zA-Z0-9-_]+)', array(
			'methods' => 'GET',
			'callback' => 'FundaWande_Cohorts_Utils::get_user_cohorts',
	
			)
		);

		register_rest_route( 'fw', 'cohorts', array(
			'methods' => 'GET',
			'callback' => 'FundaWande_Cohorts_Utils::get_public_cohorts',
	
			)
		);

		register_rest_route( 'fw', 'cohort_users/(?P<id>[0-9]+)', array(
			'methods' => 'GET',
			'callback' => 'FundaWande_Cohorts_Utils::get_cohort_users',
	
			)
		);

	}

	/**
	 * Get all public cohorts
	 * @since 1.3.1
     */
    public static function get_public_cohorts() {

	    // get all cohorts data
        $cohorts_data = FundaWande()->cohorts_utils->get_cohorts();
		
		wp_reset_postdata();
		return rest_ensure_response( $cohorts_data );

	}

	/**
	 * Get the cohorts which a user belongs to
	 *
     */
    public static function get_user_cohorts($data) {

		// if ID is set
		if( isset( $data[ 'username' ] ) ) {
			$username = $data[ 'username' ];
			$user =  get_user_by('login',$username);
			$cohorts_data = FundaWande()->cohort_users_admin->get_cohorts_for_user($user->ID);
		}
		  
		wp_reset_postdata();
		return rest_ensure_response( $cohorts_data );

	}

	/**
	 * Get all the users which belong to a specific cohort
	 *
     */
    public static function get_cohort_users($data) {

		// if ID is set
		if( isset( $data[ 'id' ] ) ) {
			$cohort_id = (int) $data[ 'id' ];
			$cohort_users_data = FundaWande()->cohort_users_admin->get_cohort_users($cohort_id,'active');
		}
		$cohort_user_ids = array();
		foreach ($cohort_users_data as $cohort_user) {
			$cohort_user_ids[] = $cohort_user->user_id;
		}
		  
		wp_reset_postdata();
		return rest_ensure_response( $cohort_user_ids );

	}
	

	/**
	 * Add a user to one or more cohorts.
	 *
	 * @param string $title The cohort title
	 * @param string $description The cohort description
	 * @param string $cohort_code The cohort ID code.
	 */
	public function publish_cohort( $title, $description, $code ) {
		global $wpdb;

		if ( ! $title ) {
			return;
		}
		

		// Store in database, ignore if entry already exists (check is done automatically using a unique index).
		$wpdb->query( "
			INSERT INTO $this->cohorts_table (code, title, description)
			VALUES ('$code','$title', '$description');
		");

		
		$cohort_id = $wpdb->insert_id;

		return $cohort_id;
		
	}

	/**
	 * Update a cohort given a cohort ID.
	 *
	 * @param string $title The cohort title
	 * @param string $description The cohort description
	 * @param string $cohort_code The cohort ID code.
	 */
	public function update_cohort($cohort_id, $title, $description, $code ) {
		global $wpdb;

		if ( ! $title ) {
			return;
		}
		
		// Store in database, ignore if entry already exists (check is done automatically using a unique index).
		$wpdb->query( "
			UPDATE $this->cohorts_table SET code = '$code', title = '$title', description = '$description'
		 	WHERE cohort_id  = $cohort_id;
		");
		
		return $cohort_id;	
	}

	/**
	 * Get a cohort from its ID.
	 *
	 * @param int $cohort_id The cohort ID
	 */
	public function get_cohort( $cohort_id ) {
		global $wpdb;

		if ( ! $cohort_id ) {
			return;
		}
	

		// Fetch from database.
		$result = $wpdb->get_row( $wpdb->prepare( "
			SELECT * FROM $this->cohorts_table
			WHERE cohort_id = %d
			LIMIT 1;
		", [
			$cohort_id
		] ) );
		return $result;
	}

	/**
	 * Get a cohort title from its ID.
	 *
	 * @param int $cohort_id The cohort ID
	 */
	public function get_cohort_title( $cohort_id ) {
		global $wpdb;

		if ( ! $cohort_id ) {
			return;
		}
	

		// Fetch from database.
		$result = $wpdb->get_row( $wpdb->prepare( "
			SELECT title FROM $this->cohorts_table
			WHERE cohort_id = %d
			LIMIT 1;
		", [
			$cohort_id
		] ) );

		return $result->title;
	}

	/**
	 * Get all cohorts.
	 *
	 */
	public function get_cohorts( ) {
		global $wpdb;

		// Fetch from database.
		$result = $wpdb->get_results( "
			SELECT * FROM $this->cohorts_table
		");
		return $result;
	}
	
	/**
	 * Delete cohorts from the DB
	 *
	 * @param array $cohort_ids Array of cohort IDs to delete
	 */
	public function delete_cohorts( $cohort_ids ) {
		global $wpdb;

		if ( ! $cohort_ids ) {
			return;
		}
	

		$cohort_ids_string = implode( ', ', array_map( function ( $id ) {
			return (int) $id;
		}, $cohort_ids ) );

		$result = $wpdb->query( "
			DELETE FROM $this->cohorts_table 
			WHERE cohort_id IN (" . $cohort_ids_string . ")
		"  );

		foreach ($cohort_ids as $cohort_id) {
			FundaWande()->cohort_users_admin->remove_relationships_from_cohort($cohort_id);
		}
 
		return $result;
	}	


} // End FundaWande_Cohorts_Admin Class
