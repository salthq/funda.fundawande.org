<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Tools;

use DeliciousBrains\WP_Offload_Media\Pro\Background_Processes\Background_Tool_Process;
use DeliciousBrains\WP_Offload_Media\Pro\Background_Processes\Remove_Local_Files_Process;
use DeliciousBrains\WP_Offload_Media\Pro\Background_Tool;

class Remove_Local_Files extends Background_Tool {

	/**
	 * @var string
	 */
	protected $tool_key = 'remove_local_files';

	/**
	 * @var string
	 */
	protected $tab = 'media';

	/**
	 * Initialize the tool.
	 */
	public function init() {
		parent::init();

		if ( ! $this->as3cf->is_pro_plugin_setup() ) {
			return;
		}

		add_action( 'as3cfpro_load_assets', array( $this, 'load_assets' ) );
		add_action( 'as3cf_form_hidden_fields', array( $this, 'render_hidden_field' ) );
		add_action( 'as3cf_after_settings', array( $this, 'render_modal' ) );
		add_action( 'as3cf_pre_save_settings', array( $this, 'maybe_start_tool_on_settings_save' ) );
	}

	/**
	 * Get the details for the sidebar block
	 *
	 * @return array|bool
	 */
	protected function get_sidebar_block_args() {
		if ( ! $this->as3cf->is_pro_plugin_setup() ) {
			return false;
		}

		return parent::get_sidebar_block_args();
	}

	/**
	 * Load assets.
	 */
	public function load_assets() {
		if ( ! $this->should_display_prompt() ) {
			return;
		}

		$this->as3cf->enqueue_script( 'as3cf-pro-remove-local-files', 'assets/js/pro/tools/remove-local-files', array(
			'jquery',
			'wp-util',
		) );
	}

	/**
	 * Render hidden form field on settings screen.
	 */
	public function render_hidden_field() {
		if ( ! $this->should_display_prompt() ) {
			return;
		}

		echo '<input type="hidden" name="remove-local-files-prompt" value="0" />';
	}

	/**
	 * Render modal in footer.
	 */
	public function render_modal() {
		if ( ! $this->should_display_prompt() ) {
			return;
		}

		$this->as3cf->render_view( 'modals/remove-local-files' );
	}

	/**
	 * Should we display the prompt to the user?
	 *
	 * @return bool
	 */
	protected function should_display_prompt() {
		if ( $this->count_media_files() > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Maybe start removal if user selected 'Yes' to prompt.
	 */
	public function maybe_start_tool_on_settings_save() {
		if ( ! isset( $_POST['remove-local-files-prompt'] ) || 0 === (int) $_POST['remove-local-files-prompt'] ) {
			return;
		}

		if ( $this->is_queued() ) {
			return;
		}

		$session = $this->create_session();

		$this->background_process->push_to_queue( $session )->save()->dispatch();
	}

	/**
	 * Should render.
	 *
	 * @return bool
	 */
	public function should_render() {
		return $this->count_media_files() && (bool) $this->as3cf->get_setting( 'remove-local-file', false );
	}

	/**
	 * Count media files in bucket.
	 *
	 * @return int
	 */
	protected function count_media_files() {
		static $count;

		if ( is_null( $count ) ) {
			$count = $this->as3cf->get_media_library_provider_total( true );
		}

		return $count;
	}

	/**
	 * Get title text.
	 *
	 * @return string
	 */
	public function get_title_text() {
		return __( 'Remove all files from server', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get more info text.
	 *
	 * @return string
	 */
	public function get_more_info_text() {
		return __( 'You can use this tool to delete all Media Library files from your local server that have already been offloaded.', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get button text.
	 *
	 * @return string
	 */
	public function get_button_text() {
		return __( 'Begin Removal', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get queued status text.
	 *
	 * @return string
	 */
	public function get_queued_status() {
		return __( 'Removing Media Library files from your local server.', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get background process class.
	 *
	 * @return Background_Tool_Process|null
	 */
	protected function get_background_process_class() {
		return new Remove_Local_Files_Process( $this->as3cf, $this );
	}
}