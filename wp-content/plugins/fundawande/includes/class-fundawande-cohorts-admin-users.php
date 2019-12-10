<?php
/**
 * All functionality pertaining to cohorts admin user.
 *
 * @package Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Funda Wande Cohorts Admin User Class
 *
 * All functionality pertaining to cohorts admin user.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.3.1
 */
class FundaWande_Cohorts_Admin_Users {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'show_user_profile', [ $this, 'edit_user_profile' ], 999 );
		add_action( 'edit_user_profile', [ $this, 'edit_user_profile' ], 999 );
	}

	public function edit_user_profile( $user ) {
		// Remove user from cohort.
		if ( isset( $_POST['remove_user_from_cohort'] ) ) {
			$cohort_id = $_POST['remove_user_from_cohort'];
			FundaWande()->cohort_users_admin->remove_users_from_cohort( [ $user->ID ], $cohort_id );
		}

		// Change user cohort status.
		if ( isset( $_POST['change_user_cohort_status'] ) ) {
			$cohort_status = $_POST['change_user_cohort_status'];
			$cohort_id = (int) $_POST['change_user_cohort_id'];
			FundaWande()->cohort_users_admin->update_status_for_user_cohorts($user->ID,[ $cohort_id ] , $cohort_status);
		}

		// Add user to cohort.
		if ( isset( $_POST['add_to_cohort'] ) ) {
			$cohort_id = (int) $_POST['add_to_cohort'];
			$cohort_status = $_POST['add_to_cohort_status'];
			FundaWande()->cohort_users_admin->add_user_to_cohorts( $user->ID, [ $cohort_id ] );
			FundaWande()->cohort_users_admin->update_status_for_user_cohorts($user->ID,[ $cohort_id ] , $cohort_status);

		}

		$cohorts = FundaWande()->cohort_users_admin->get_cohorts_for_user( $user->ID );
		?>

		<h2>Cohorts</h2>

		<table class="widefat fixed">
			<thead>
			<tr>
				<th>Cohort</th>
				<th>Start Date</th>
			
				<th>Status</th>
				<th>Change</th>
				<th>Remove</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $cohorts as $cohort ) {
				
				?>
				<tr>
					<td>
						<?= FundaWande()->cohorts_utils->get_cohort_title($cohort->cohort_id )?>
					</td>
					<td>
						<?= $cohort->start_date ?>
					</td>
					<td>
						<?=  $cohort->status ?>
					</td>
					<td>
					<?php $statuses = array('active','in-active') ?>
				
						<?php foreach ($statuses as $status) { 
						if ($status !== $cohort->status ) { ?>

							<a class="change_user_cohort_status" href=# data-status="<?= $status ?>" data-cohort="<?= $cohort->cohort_id ?>"><?= $status ?></a> 
						 <?php	} 
						} ?>
					</td>
					<td>
						<button class="button button-secondary" name="remove_user_from_cohort" data-value="<?= $cohort->cohort_id ?>">Remove</button>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

		<h3>Add to cohort</h3>
		<?php $cohorts = FundaWande()->cohorts_utils->get_cohorts(); ?>

		<select id="add_to_cohort" name="add_to_cohort" class="form-control  customSelect searchSelect" >
			<option value="all" selected="selected" disabled>Choose a cohort to add</option>
			<?php foreach ($cohorts as $cohort_choice) { ?>
				<option value="<?php echo (int) $cohort_choice->cohort_id; ?>" ><?php echo $cohort_choice->cohort_id .' | '.$cohort_choice->title; ?></option>
			<?php } ?> 
		</select>
							
		<select class="form-control" name="add_to_cohort_status" id="add_to_cohort_status">
			<option disabled>Choose an status</option>
			<option value="active" selected>Active</option>
			<option value="in-active">In-active</option>
		</select>
		<button class="button button-secondary" id="add_to_cohort_button">Add</button>

		<script>
            jQuery(document).ready(function ($) {
                var search_request;
                var root = <?=json_encode( esc_url_raw( rest_url() ) )?>;
                var nonce = <?=json_encode( wp_create_nonce( 'wp_rest' ) )?>;

                $('#add_to_cohort_button').on('click', function () {
                    var form = $('<form method="post">');
                    var input = $('<input name="add_to_cohort">');
                    input.val($('#add_to_cohort').val());
					input.appendTo(form);
					var input2 = $('<input name="add_to_cohort_status">');
                    input2.val($('#add_to_cohort_status').val());
                    input2.appendTo(form);
                    form.appendTo($('body'));
                    form.submit();
                });

                $('.change_user_cohort_status').on('click', function (e) {
					e.preventDefault();
                    var form = $('<form method="post">');
					var input = $('<input name="change_user_cohort_status">');
					var input2 = $('<input name="change_user_cohort_id">');
					input.val($(this).attr('data-status'));
					input2.val($(this).attr('data-cohort'));
					input.appendTo(form);
					input2.appendTo(form);
                    form.appendTo($('body'));
                    form.submit();
				});

				$('[name=remove_user_from_cohort]').on('click', function () {
                    var form = $('<form method="post">');
                    var input = $('<input name="remove_user_from_cohort">');
                    input.val($(this).attr('data-value'));
                    input.appendTo(form);
                    form.appendTo($('body'));
                    form.submit();
				});
				
            });
		</script>
		<?php
	}
} // end FundaWande_Cohorts_Admin_Users
