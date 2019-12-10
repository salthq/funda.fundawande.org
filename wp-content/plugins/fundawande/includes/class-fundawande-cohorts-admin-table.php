<?php

class FundaWande_Cohorts_Admin_Table extends WP_List_Table {
	

	public function __construct( ) {

		parent::__construct( array(
			'singular' => 'cohort',
			'plural'   => 'cohorts',
			'ajax'     => false
		) );
	}

	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" class="_cb" name="%1$s[]" value="%2$s">',
			$this->_args['singular'],
			$item->cohort_id
		);
	}

	protected function column_cohort( $item ) {
		return sprintf(
			'
				<a class="row-title" href="%s">%s</a><br>
				<div class="row-actions">
					<span class="edit"><a href="%s">Edit cohort</a></span> | <span class="edit"><a href="%s">Cohort users</a></span>
				</div>
			',
			'/wp-admin/admin.php?page=add_edit_cohort&cohort_id='.$item->cohort_id,
			$item->title,
			'/wp-admin/admin.php?page=add_edit_cohort&cohort_id='.$item->cohort_id,
			'/wp-admin/admin.php?page=cohort_users&cohort_id='.$item->cohort_id
		);
	}

	protected function column_users( $item ) {

		$active_users = FundaWande()->cohort_users_admin->get_cohort_users($item->cohort_id,'active');
		$in_active_users = FundaWande()->cohort_users_admin->get_cohort_users($item->cohort_id,'in_active');
		$content = '<a href="/wp-admin/admin.php?page=cohort_users&cohort_id='.$item->cohort_id.'&status=active">Active users</a> ('.count($active_users).')  <br> Inactive users ('.count($in_active_users).')';
		
		return $content;
	}

	protected function column_description( $item ) {

		return $item->description;
	}


	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'cohort'         => 'Cohort',
			'users'  => 'Users',
			'description'  => 'Description'
		);
	}

	public function get_bulk_actions() {
		$actions = [
			'delete' => 'Remove'
		];

		return $actions;
	}

	public function process_bulk_action() {
		$key         = $this->_args['singular'];
		$request_ids = isset( $_REQUEST[ $key ] ) ? wp_parse_id_list( wp_unslash( $_REQUEST[ $key ] ) ) : [];

		// Nothing selected?
		if ( ! count( $request_ids ) ) {
			return;
		}

		// Delete.
		if ( $this->current_action() === 'delete' ) {
			FundaWande()->cohorts_utils->delete_cohorts( $request_ids );

			return;
		}
	}

	public function prepare_items() {
		global $wpdb;
		$table = 'wp_cohorts';
		$query = "
			SELECT
				t.cohort_id,
				t.title,
				t.description,
				t.code
			FROM $table AS t
		";

		// Order
		$orderby = ! empty( $_GET["orderby"] ) ? esc_sql( $_GET["orderby"] ) : '';
		$order   = ! empty( $_GET["order"] ) ? esc_sql( $_GET["order"] ) : 'ASC';
		if ( ! empty( $orderby ) && ! empty( $order ) ) {
			$query .= " ORDER BY $orderby $order";
		}

		// Paging
		$per_page     = 20;
        $paged = isset($_GET['paged']) ? max(0, intval($_GET['paged']) - 0) : 0;
		$total_items  = $wpdb->query( $query );
		if ( ! empty( $paged ) && ! empty( $per_page ) ) {
			$offset = (int) ( ( $paged - 1 ) * $per_page );
			$query  .= " LIMIT $per_page OFFSET $paged";
		}
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		// Columns
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Process bulk actions.
		$this->process_bulk_action();

		// Fetch items.
		$this->items = $wpdb->get_results( $query );
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