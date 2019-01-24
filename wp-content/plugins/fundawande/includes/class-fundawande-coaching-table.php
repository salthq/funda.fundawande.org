<?php

class FundaWande_Coaching_Table extends WP_List_Table {
	public $coaches;

	public function __construct( ) {

		// Necessary to assign screen manually to avoid errors
		$this->screen = get_current_screen();

		// Assign the coaches the local variable for use  in table
		$this->coaches = FundaWande()->coaching_utils->get_coaches();

	}

	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" class="_cb" name="user_select[%s]" value="%s">',
			$item->ID,
			$item->ID
		);
	}

	protected function column_user( $item ) {
		return sprintf(
			'
				<p>%s</p>
				<div class="row-actions">
					<span class="edit"><a href="%s">View user</a></span>
				</div>
			',
			$item->display_name,
			'/wp-admin/user-edit.php?user_id='.$item->ID
		);
	}

	

	protected function column_coach( $item ) {
		$coaches = $this->coaches;
		$data =  sprintf(
			'<select id="coach_id" name="coaches[%s]" class="form-control  customSelect searchSelect" >
			<option value="" selected disabled>Assign a coach</option>',
			$item->ID

		);

		foreach ($coaches as $coach) { 
			$selected = ($coach->ID == $item->coach) ? 'selected' : '';
			$name = $coach->ID .' | '.$coach->display_name;
			$data .= sprintf( '
			 <option value="%s" %s>%s</option>',
			 $coach->ID,
			 $selected,
			 $name
			);
		}
		$data .= '</select>';

		return $data;
	}


	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'user'         => 'User',
			'coach'  => 'Coach',
		);
	}

	protected function extra_tablenav( $which ) {
		if ( $which !== 'top' ) {
			return;
		}

		$coaches = $this->coaches;
		$data =  sprintf(
			'<select id="bulk_coach_id" name="bulk_coach" class="form-control  customSelect searchSelect" >
			<option value="all" selected disabled>Bulk assign a coach</option>'

		);

		foreach ($coaches as $coach) { 
			$selected = '';
			$name = $coach->ID .' | '.$coach->display_name;
			$data .= sprintf( '
			 <option value="%s" %s>%s</option>',
			 $coach->ID,
			 $selected,
			 $name
			);
		}
		$data .= '</select>';
		?>
		<div class="alignleft actions">
			<label for="bulk_coach_id" class="screen-reader-text">Bulk add coach</label>
			
				<?php
				echo $data;
				?>
			</div >
		<?php
	}

	protected function get_sortable_columns() {
		return array(
			'user'         => [ 'display_name', false ]
		);
	}

	

	public function process_bulk_action() {
		// $key         = $this->_args['singular'];
		// $request_ids = isset( $_REQUEST[ $key ] ) ? wp_parse_id_list( wp_unslash( $_REQUEST[ $key ] ) ) : [];

		// // Nothing selected?
		// if ( ! count( $request_ids ) ) {
		// 	return;
		// }

		// // Delete.
		// if ( $this->current_action() === 'delete' ) {
		// 	LMS()->courses_admin_utils->delete_courses( $request_ids );

		// 	return;
		// }

		// // Set status.
		// $status_prefix = 'set_status_to_';
		// if ( substr( $this->current_action(), 0, strlen( $status_prefix ) ) === $status_prefix ) {
		// 	$status = (int) substr( $this->current_action(), strlen( $status_prefix ) );
		// 	AdvantageLearn()->cohorts->set_status_for_cohort_users( $request_ids, $this->cohort_id, $status );

		// 	return;
		// }
	}

	// protected function extra_tablenav( $which ) {
	// 	if ( $which !== 'top' ) {
	// 		return;
	// 	}

	// 	$query   = new WP_Query( [
	// 		'post_type'      => 'cohorts',
	// 		'posts_per_page' => - 1
	// 	] );
	// 	$cohorts = $query->get_posts();
	// 
	// }

	public function prepare_items() {
		
		if ( isset( $_GET['course_id'] ) ) {
			$course_id = (int) $_GET['course_id'];
            
		}
		// Order
		$orderby = ! empty( $_GET["orderby"] ) ? esc_sql( $_GET["orderby"] ) : 'display_name';
		$order   = ! empty( $_GET["order"] ) ? esc_sql( $_GET["order"] ) : 'ASC';
	
		$users = FundaWande()->coaching_utils->get_course_users($course_id,$orderby,$order);

		// Columns
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Process bulk actions.
		$this->process_bulk_action();
		// Fetch items.
		$this->items = $users;
	}

	public function display() {
		parent::display();

		?>
		<script>
            jQuery(document).ready(function ($) {
                $('a[data-action]').on('click', function () {
                    // Deselect all other rows.
                    $('#the-list input._cb').prop('checked', false);

                    // Select this row.
                    $(this).closest('tr').find('input._cb').prop('checked', true);

                    // Set action.
                    $('#bulk-action-selector-top').val($(this).attr('data-action'));

                    // Submit form.
                    $(this).closest('form').submit();

                    return false;
                });
            });
		</script>
		<?php
	}
}