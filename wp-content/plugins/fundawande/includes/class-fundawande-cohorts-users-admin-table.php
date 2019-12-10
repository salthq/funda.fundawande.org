<?php

class FundaWande_Cohort_Users_Admin_Table extends WP_List_Table {
	private $cohort_id;

	public function __construct( $cohort_id ) {
		$this->cohort_id = (int) $cohort_id;


		parent::__construct( array(
			'singular' => 'user',
			'plural'   => 'users',
			'ajax'     => false
		) );
	}

	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" class="_cb" name="%1$s[]" value="%2$s">',
			$this->_args['singular'],
			$item->user_id
		);
	}

	protected function column_user( $item ) {
		return sprintf(
			'
				<a class="row-title" href="%s">%s</a> %s | id:%s<br>
				<div class="row-actions">
					<span class="edit"><a href="%s">View user</a> | </span><span class="trash"><a href="#" class="submitdelete" data-action="delete">Remove</a></span>
				</div>
			',
			get_edit_user_link( $item->user_id ),
			$item->display_name,
			$item->user_email,
			$item->user_id,
			get_edit_user_link( $item->user_id )
		);
	}

	protected function column_status( $item ) {
		$labels = FundaWande()->cohort_users_admin->get_status_labels();
		$links  = [];
		foreach ( $labels as $key => $label ) {
			if ( $key == $item->status ) {
				continue;
			}

			$links[] = sprintf(
				'<a href="#" data-action="set_status_to_%s">%s</a>',
				$key,
				$label
			);
		}

		return sprintf(
			'%s<br>%s',
			$item->status,
			implode( ' | ', $links )
		);
	}


	protected function column_start_date( $item ) {
		return date( 'd F Y', strtotime( $item->start_date ) );
	}

	protected function column_last_updated( $item ) {
		return date( 'd F Y', strtotime( $item->last_updated ) );
	}

	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'user'         => 'User',
			'status'       => 'Status',
			'start_date'   => 'Start Date',
			'last_updated' => 'Last Updated',
		);
	}

	protected function get_sortable_columns() {
		return array(
			'user'         => [ 'display_name', false ],
			'status'       => [ 'status', false ],
			'start_date'   => [ 'start_date', false ],
			'last_updated' => [ 'last_updated', false ]
		);
	}

	public function get_bulk_actions() {
		$actions = [
			'delete' => 'Remove'
		];

		$labels = FundaWande()->cohort_users_admin->get_status_labels();
		foreach ( $labels as $key => $label ) {
			$actions[ 'set_status_to_' . $key ] = 'Set status to ' . $label;
		}

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
			FundaWande()->cohort_users_admin->remove_users_from_cohort( $request_ids, $this->cohort_id );

			return;
		}

		// Set status.
		$status_prefix = 'set_status_to_';
		if ( substr( $this->current_action(), 0, strlen( $status_prefix ) ) === $status_prefix ) {
			$status = substr( $this->current_action(), strlen( $status_prefix ) );
			FundaWande()->cohort_users_admin->set_status_for_cohort_users( $request_ids, $this->cohort_id, $status );

			return;
		}
	}

	protected function extra_tablenav( $which ) {
		if ( $which !== 'top' ) {
			return;
		}

	}

	public function prepare_items() {
		global $wpdb;
		$table = FundaWande()->cohort_users_admin->cohort_users_table;
		$query = "
			SELECT
				t.id,
				t.user_id,
				t.status,
				t.start_date,
				t.last_updated,
				u.display_name,
				u.user_nicename,
				u.user_login,
				u.user_email
			FROM $table AS t
			JOIN $wpdb->users AS u ON (u.ID = t.user_id)
			WHERE cohort_id = $this->cohort_id
		";

		$status_filter = ! empty( $_GET["status"] ) ? esc_sql( $_GET["status"] ) : -1;

		// Status filter
		if ( $status_filter !== -1 ) {
			$query .= ' AND status = "' . $status_filter.'"';
		}

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