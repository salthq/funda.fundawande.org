<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // End if().

/**
 * Responsible for loading FundaWande and setting up the Main WordPress hooks.
 *
 * @package Core
 * @author Pango
 * @since 1.0.0
 */
class FundaWande_Main {

	/**
	 * @var string
	 * Reference to the main plugin file name
	 */
	private $main_plugin_file_name;

	/**
	 * @var FundaWande_Main $_instance to the the main and only instance of the FundaWande class.
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main reference to the plugins current version
	 */
	public $version;

	/**
	 * Public token, referencing for the text domain.
	 */
	public $token = 'woothemes-fundawande';

	/**
	 * Plugin url and path for use when access resources.
	 */
	public $plugin_url;
	public $plugin_path;
	public $template_url;

	/**
	 * @var FundaWande_PostTypes
	 * All FundaWande sub classes. Currently used to access functionality contained within
	 */
	public $post_types;

	/**
	 * @var FundaWande_Settings
	 */
	public $settings;

	/**
	 * Constructor method.
	 *
	 * @param  string $file The base file of the plugin.
	 * @since  1.0.0
	 */
	private function __construct( $main_plugin_file_name, $args ) {

		// Setup object data
		$this->main_plugin_file_name = $main_plugin_file_name;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $this->main_plugin_file_name ) );
		$this->plugin_path = trailingslashit( dirname( $this->main_plugin_file_name ) );
		$this->template_url	= apply_filters( 'fundawande_template_url', 'fundawande/' );
		$this->version = isset( $args['version'] ) ? $args['version'] : null;

		// Initialize the core FundaWande functionality
		$this->init();

		// Run this on activation.
		register_activation_hook( $this->main_plugin_file_name, array( $this, 'activation' ) );


	} // End __construct()

	/**
	 * Load the foundations of FundaWande.
	 *
	 * @since 1.0.0
	 */
	protected function init() {


		$this->initialize_global_objects();

	}

	/**
	 * Global FundaWande Instance
	 *
	 * Ensure that only one instance of the main FundaWande class can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC()
	 * @return FundaWande Instance.
	 */
	public static function instance( $args ) {

		if ( is_null( self::$_instance ) ) {


			// FundaWande requires a reference to the main FundaWande plugin file
			$fundawande_main_plugin_file = dirname( dirname( __FILE__ ) ) . '/fundawande.php';

			self::$_instance = new self( $fundawande_main_plugin_file, $args  );

		}

		return self::$_instance;

	} // end instance()

	/**
	 * This function is linked into the activation
	 * hook to reset flush the urls to ensure FundaWande post types show up.
	 *
	 * @since 1.0.0
	 *
	 * @param $plugin
	 */
	public static function activation_flush_rules( $plugin ) {

		if ( strpos( $plugin, '/fundawande.php' ) > 0 ) {

			flush_rewrite_rules( true );

		}

	}

	/**
	 * Load the properties for the main FundaWande object
	 *
	 * @since 1.0.0
	 */
	public function initialize_global_objects() {

		// Setup language class
        $this->language = new FundaWande_Language();

        // Setup post types - COMMENTED OUT FOR NOW
        // $this->post_types = new FundaWande_PostTypes();

        // Setup quiz functionality class
        $this->quiz = new FundaWande_Quiz();

        // Setup question functionality class
        $this->question = new FundaWande_Question();

        // Setup grading functionality class
        $this->grading = new FundaWande_Grading();


	}

	/**
	 * Initialize all FundaWande hooks
	 *
	 * @since 1.0.0
	 */
	public function load_hooks() {

		/**
		 * Load all Template hooks
		 */
//		if ( ! is_admin() ) {
//			require_once( $this->resolve_path( 'includes/hooks/template.php' ) );
//		}
	}

	/**
	 * Determine the relative path to the plugin's directory.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string $fundawande_plugin_path
	 */
	public function plugin_path() {

		if ( $this->plugin_path ) {

			$fundawande_plugin_path = $this->plugin_path;

		} else {

			$fundawande_plugin_path = plugin_dir_path( __FILE__ );

		}

		return $fundawande_plugin_path;

	} // End plugin_path()



} // End FundaWande_Main Class