<?php
/**
 * All functionality pertaining to cohorts.
 *
 * @package Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FundaWande Cohorts Class
 *
 * All functionality pertaining to cohorts.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.3.1
 */
class FundaWande_Cohort_Users_Admin {
	const OPTION_KEY_DB_VERSION = 'fw_cohorts_db_version';
	const DB_VERSION = 1;
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'in-active';
	public $cohort_users_table;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->cohort_users_table = $wpdb->prefix . 'cohort_users';

		add_action( 'admin_init', [ $this, 'register_db_update_hook' ] );
		register_activation_hook( FW_PLUGIN_MAIN_FILE, [ $this, 'register_activation_hook' ] );
	
	
		// Action to handle cohorts after the deletion of a user on WP
		add_action( 'delete_user', array($this,'handle_cohorts_deleted_user'), 10 );
	
		// Action to add cohort user admin menu page
		add_action( 'admin_menu', array($this,'register_cohort_users_menu_page' ));


	}

	/**
	 * Register cohort users menu page
	 */
	function register_cohort_users_menu_page(){
	

		add_submenu_page(
			'cohorts_user_admin',
			'Cohort Users',
			'Cohort Users',
			'manage_options',
			'cohort_users',
			array($this,'render_cohort_user_admin_page')
		);

		// hide the page link
		add_action( 'admin_head', function () {
			remove_submenu_page( 'cohorts_user_admin', 'cohort_users' );
		} );

	
		
	}

	public function register_activation_hook() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "
			CREATE TABLE $this->cohort_users_table (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				cohort_id bigint(20) NOT NULL,
				user_id bigint(20) NOT NULL,
				status text NOT NULL,
				start_date datetime NOT NULL,
				last_updated datetime NOT NULL,
				PRIMARY KEY (id),
				UNIQUE INDEX cohort_and_user_id (cohort_id, user_id)
			) $charset_collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( self::OPTION_KEY_DB_VERSION, self::DB_VERSION );
	}

	public function register_db_update_hook() {

		$db_version = get_option( self::OPTION_KEY_DB_VERSION);

		if ($db_version < self::DB_VERSION) {
			self::register_activation_hook();
			update_option( self::OPTION_KEY_DB_VERSION, self::DB_VERSION ,'yes');
		}
	}

	public function get_status_labels() {
		return [
			self::STATUS_INACTIVE => 'in-active',
			self::STATUS_ACTIVE   => 'active'
		];
	}

	/**
	 * Add a user to one or more cohorts.
	 *
	 * @param int $user_id The user ID
	 * @param int[] $cohort_ids The cohort IDs
	 * @param int $associated_post_id The associated order or subscription ID, if any.
	 */
	public function add_user_to_cohorts( $user_id, array $cohort_ids ) {
		global $wpdb;

		if ( ! $user_id || ! is_array( $cohort_ids ) ) {
			return;
		}

		
		foreach ( $cohort_ids as $cohort_id ) {
			if ( ! $cohort_id ) {
				continue;
			}

			// Store in database, ignore if entry already exists (check is done automatically using a unique index).
			$wpdb->query( $wpdb->prepare( "
				INSERT IGNORE INTO $this->cohort_users_table (cohort_id, user_id, status, start_date, last_updated)
				VALUES (%d, %d, %s, NOW(), NOW());
			", [
				$cohort_id,
				$user_id,
                self::STATUS_INACTIVE
			] ) );

		}
	}

	/**
	 * Remove one or more users from a cohort
	 *
	 * @param int[] $user_ids An array of user IDs
	 * @param int $cohort_id The cohort ID
	 */
	public function remove_users_from_cohort( $user_ids, $cohort_id ) {
		global $wpdb;

		$user_ids_array = implode( ', ', array_map( function ( $id ) {
			return (int) $id;
		}, $user_ids ) );


		$wpdb->query( $wpdb->prepare( "
			DELETE FROM $this->cohort_users_table 
			WHERE user_id IN (" . $user_ids_array . ") AND cohort_id = %d;
		", [
			$cohort_id
		] ) );

		// remove intercom tag(s) from user(s)
        // foreach ($user_ids as $user_id) {
        //     StartupSchool()->intercom->remove_cohort_tags_from_user_intercom($user_id, array($cohort_id));
        // }
	}

	/**
	 * Remove one or more users from a cohort
	 *
	 * @param int[] $user_ids An array of user IDs
	 * @param int $cohort_id The cohort ID
	 */
	public function remove_relationships_from_cohort($cohort_id ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM $this->cohort_users_table 
			WHERE cohort_id = %d;
		", [
			$cohort_id
		] ) );

	}

	/**
	 * Get all data related to a user from a particular cohort.
	 *
	 * @param int $user_id
	 * @param int $cohort_id
	 *
	 * @return object
	 */
	public function get_cohort_user( $user_id, $cohort_id ) {
		global $wpdb;

		// Fetch from database.
		$results =  $wpdb->get_results( $wpdb->prepare( "
			SELECT * FROM $this->cohort_users_table
			WHERE user_id = %d AND cohort_id = %d;
		", [
			$user_id,
			$cohort_id
		] ) );

		if (is_array($results) && 1 == count($results)) {
			$results = array_shift($results);
		}

		return $results;
	}

	/**
	 * Get all cohort data for a user.
	 *
	 * @param int $user_id
	 *
	 * @return object
	 */
	public function get_cohorts_for_user( $user_id ) {
		global $wpdb;

		// Fetch from database.
		return $wpdb->get_results( $wpdb->prepare( "
			SELECT * FROM $this->cohort_users_table
			WHERE user_id = %d;
		", [
			$user_id,
		] ) );
	}

	/**
	 * Set the status for one or more users in a cohort.
	 *
	 * @param int[] $user_ids
	 * @param int $cohort_id
	 * @param int $status The status; Can be one of self::STATUS_ACTIVE, self::STATUS_INACTIVE, or self::STATUS_ALUMNI.
	 */
	public function set_status_for_cohort_users( array $user_ids, $cohort_id, $status ) {
		global $wpdb;

		$user_ids = implode( ', ', array_map( function ( $id ) {
			return (int) $id;
		}, $user_ids ) );

		$wpdb->query( $wpdb->prepare( "
			UPDATE $this->cohort_users_table SET status = %s, last_updated = NOW() 
			WHERE user_id IN (" . $user_ids . ") AND cohort_id = %d;
		", [
			$status,
			$cohort_id
		] ) );
	}

	/**
	 * Set the status for one or more users in a cohort.
	 *
	 * @param int[] $user_ids
	 * @param int $cohort_id
	 * @param int $status The status; Can be one of self::STATUS_ACTIVE, self::STATUS_INACTIVE, or self::STATUS_ALUMNI.
	 */
	public function set_status_for_cohort_user( $user_id, $cohort_id, $status ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			UPDATE $this->cohort_users_table SET status = %s, last_updated = NOW() 
			WHERE user_id = $user_id AND cohort_id = %d;
		", [
			$status,
			$cohort_id
		] ) );

		// $this->add_tags_cohorts_user($user_id, [$cohort_id], $status);
	}

	/**
	 * Add the cohort intercom tag to user depending on the cohort.
	 *
	 * @param int[] $user_ids
	 * @param int $cohort_id
	 * @param int $status The status; Can be one of self::STATUS_ACTIVE, self::STATUS_INACTIVE, or self::STATUS_ALUMNI.
	 */
	public function add_tags_cohorts_user( $user_id, $cohort_ids, $status ) {

		 // determine whether to add or remove Intercom tag
// 		 switch ($status) {
//             // TODO add action for alumni status
// //            case self::STATUS_ALUMNI:
// //                StartupSchool()->intercom->add_cohort_tags_to_user_intercom($user_id,$cohort_ids);
// //                break;
//             case self::STATUS_ACTIVE:
//                 StartupSchool()->intercom->add_cohort_tags_to_user_intercom($user_id,$cohort_ids);
//                 break;
//             case self::STATUS_INACTIVE:
//                 StartupSchool()->intercom->remove_cohort_tags_from_user_intercom($user_id,$cohort_ids);
//                 break;
//             default:
//                 StartupSchool()->intercom->add_cohort_tags_to_user_intercom($user_id,$cohort_ids);

//         }
    }

	/**
	 * Set the status for one or more users in a cohort.
	 *
	 * @param int $user_id
	 * @param int $cohort_id
	 * @param int $status status to check for, default is active
	 * @return boolean True if user has active cohort, false if not
	 */
	public function user_has_cohort( $user_id, $cohort_id, $status = 'active') {
        $user_cohort = self::get_cohort_user($user_id,$cohort_id);
		if (!empty($user_cohort) && (int) $user_cohort->status ==  $status) {
           
            return true;
            
		}
		return false;
	}

	/**
	 * Get details of relationship users in a cohort.
	 *
	 * @param int $cohort_id
	 *
	 * @return object
	 */
	public function get_cohort_users( $cohort_id , $status = null) {
		global $wpdb;

		if ($status) {
			$status_array = $status;
		
		} else {
			$status_array = 'active,in-active';
		}

		
		// Fetch from database.
		$results =  $wpdb->get_results( $wpdb->prepare( "
			SELECT * FROM $this->cohort_users_table
			WHERE cohort_id = %d
			AND status IN (%s)
		", [
			$cohort_id,
			$status_array
		] ) );

		return $results;
	}

    /**
     * Update the status for the cohorts or a user.
     *
     * @param int $user_id
     * @param int[] $cohort_ids
     * @param int $status The status; Can be one of self::STATUS_ACTIVE, self::STATUS_INACTIVE, or self::STATUS_ALUMNI.
     */
    public function update_status_for_user_cohorts( $user_id, $cohort_ids, $status = self::STATUS_ACTIVE ) {
        global $wpdb;

        foreach ( $cohort_ids as $cohort_id ) {
            if ( ! $cohort_id ) {
                continue;
            }

            // Store in database, ignore if entry already exists (check is done automatically using a unique index).
            $wpdb->query( $wpdb->prepare( "
				UPDATE $this->cohort_users_table SET status = %s, last_updated = NOW() 
			WHERE user_id = %d AND cohort_id = %d;
		", [
            $status,
            $user_id,
            $cohort_id
        ] ) );

        }

        // $this->add_tags_cohorts_user($user_id, $cohort_ids, $status);

	}


	/**
	 * Get all products linked to a cohort.
	 *
	 * @param int $cohort_id
	 *
	 * @return array Linked products containing just two properties: ID and post_title.
	 */
	public function get_linked_products( $cohort_id ) {
		// global $wpdb;

		// // Get products that are linked to cohorts.
		// $results = $wpdb->get_results( $wpdb->prepare( "
		// 	SELECT
		// 		p.post_title,
		// 		pm.post_id,
		// 		pm.meta_key,
		// 		pm.meta_value
		// 	FROM $wpdb->posts AS p 
		// 	JOIN $wpdb->postmeta AS pm ON (pm.post_id = p.ID)
		// 	WHERE
		// 		p.post_type = 'product' AND
		// 		pm.meta_key IN (%s, %s)
		// ", [
		// 	StartupSchool_Cohorts_WooCommerce::META_KEY_APPLICATION_COHORTS,
		// 	StartupSchool_Cohorts_WooCommerce::META_KEY_COURSE_COHORTS
		// ] ) );

		// // Search for this cohort.
		// $products = [];
		// foreach ( $results as $result ) {
		// 	@$cohort_ids = unserialize( $result->meta_value );
		// 	if ( in_array( $cohort_id, $cohort_ids ) ) {
		// 		$products[ $result->post_id ] = [
		// 			'ID'         => $result->post_id,
		// 			'post_title' => $result->post_title
		// 		];
		// 	}
		// }

		// // Return matching products.
		// return $products;
	}

	

   
	
	/**
	 * Handle cohort actions for a deleted user
	 *
	 * When a user is deleted there are knock on effects for the cohorts they were associated with
	 * 
	 * @param integer $user_id ID of the user being deleted
	 * 
	 */
	function handle_cohorts_deleted_user( $user_id ) {
		
		// Get the user's cohorts so we can remove them
		$user_cohorts = $this->get_cohorts_for_user($user_id);

		// Loop through the cohorts 
		foreach ($user_cohorts as $user_cohort ) {

			// Remove the user from the cohort
			// Note 1: We wrap the user ID in an array as the function expects array of users
			// Note 2: The function includes a loop to remove the user from intercom tags related to the cohort
			self::remove_users_from_cohort([$user_id], $user_cohort->cohort_id);

		};

		// Archive user in intercom
		// StartupSchool()->intercom->archive_user_intercom($user_id);

		// Cohorts removed.
		return true;
	}


	public function render_cohort_user_admin_page() {
		global $wpdb;

		$cohort_id = $_GET['cohort_id'];
		$cohort    = FundaWande()->cohorts_utils->get_cohort( $cohort_id );
		if ( ! $cohort ) {
			?>
			Cannot find cohort.
			<?php

			return;
		}

		

		// Add user to cohort.
		if ( array_key_exists( 'add_user_to_cohort', $_POST ) ) {
			$user = $_POST['add_user_to_cohort'];
			$user = get_user_by( 'slug', $user );
			$cohort_status = $_POST['add_to_cohort_status'];
			self::add_user_to_cohorts( $user->ID, [ $cohort_id ] );
			self::update_status_for_user_cohorts($user->ID,[ $cohort_id ] , $cohort_status);

		}

		// Status filters
		$status       =  isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : - 1;
		$url          = 'admin.php?page=cohort_users&cohort_id=' . $cohort_id;
		$labels       = self::get_status_labels();
		$table        = $this->cohort_users_table;
		$status_count = $wpdb->get_results( $wpdb->prepare( "
			SELECT status, COUNT(*) AS count 
			FROM $table AS t
			WHERE t.cohort_id = %d
			GROUP BY status; 
		", [
			$cohort_id
		] ) );
		$status_count = array_reduce( $status_count, function ( $new, $old ) {
			$new[ $old->status ] = $old->count;

			return $new;
		}, [] );
		$status_all   = array_sum( $status_count );

		// Prepare list table.
		$wp_list_table = new FundaWande_Cohort_Users_Admin_Table( $cohort_id );
		$wp_list_table->prepare_items();

		?>
		<div class="wrap">
			<h1 class="wp-heading">
				Cohort users (<?= $cohort->title ?>)
			</h1>

			<ul class="subsubsub">
				<li class="all">
					<a href="<?= $url ?>" class="<?= ! array_key_exists( 'status', $_REQUEST ) ? 'current' : '' ?>">
						All <span class="count">(<?= $status_all ?>)</span>
					</a>
					|
				</li>
				<?php
				$i = 0;
				foreach ($labels as $key => $label) {
					$i++;
					?>
					<li class="">
						<a href="<?= add_query_arg( 'status', $key, $url ) ?>" class="<?= $status === $key ? 'current' : '' ?>">
							<?= $label ?> <span class="count">(<?= isset( $status_count[ $key ] ) ? $status_count[ $key ] : 0 ?>)</span>
						</a>
						<?= $i === count( $labels ) ? '' : '|' ?>
					</li>
					<?php
				}
				?>
			</ul>

			<div>
				<form method="get" action="">
					<input type="hidden" name="cohort_id" value="<?= isset( $_GET['cohort_id'] ) ? $_GET['cohort_id'] : '' ?>">
					<input type="hidden" name="page" value="<?= isset( $_GET['page'] ) ? $_GET['page'] : '' ?>">
					<input type="hidden" name="order" value="<?= isset( $_GET['order'] ) ? $_GET['order'] : '' ?>">
					<?php $wp_list_table->display(); ?>
				</form>
			</div>
			<br>

			<form method="post">
				<h4>Add user to cohort</h4>
				<input type="text" id="add_user_to_cohort" name="add_user_to_cohort" class="form-control search-autocomplete" placeholder="Type name or ID">
				<select class="form-control" name="add_to_cohort_status" id="add_to_cohort_status">
					<option disabled>Choose an status</option>
					<option value="active" selected>Active</option>
					<option value="in-active">In-active</option>
				</select>
				<button type="submit" class="button">Add user</button>
			</form>
			<br>

			<div>
				<h4>Linked courses</h4>

				<?php
				/* $products = self::get_linked_products( $cohort_id );
				foreach ( $products as $product ) {
					?>
					<a href="<?= admin_url() ?>post.php?post=<?= $product['ID'] ?>&action=edit"><?= $product['post_title'] ?></a>
					&nbsp;&nbsp;&nbsp;
					<?php
				} */
				?>
			</div>
		</div>

		<script>
            jQuery(document).ready(function ($) {
                var search_request;
                var root = <?=json_encode( esc_url_raw( rest_url() ) )?>;
                var nonce = <?=json_encode( wp_create_nonce( 'wp_rest' ) )?>;

                $('#add_user_to_cohort').autocomplete({
                    minChars: 3,
                    source: function (term, suggest) {
                        try {
                            search_request.abort();
                        } catch (e) {
                        }

                        search_request = $.ajax({
                            url: root + 'wp/v2/users/',
                            data: {
                                filter: {
                                    'posts_per_page': 10
                                },
                                search: term.term
                            },
                            dataType: 'json',
                            type: 'GET',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('X-WP-Nonce', nonce);
                            },
                            success: function (users) {
                                var s = [];

                                for (var i = 0; i < users.length; i++) {
                                    s.push({
                                        label: users[i].name + ' (' + users[i].slug + ', #' + users[i].id + ')',
                                        value: users[i].slug
                                    });
                                }

                                suggest(s);
                            }
                        });
                    }
                });
            });
		</script>
		<?php

		wp_enqueue_script( 'jquery-ui-autocomplete' );
	}

} // end FundaWande_Cohorts
