<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used
 * across both the public-facing side of the site and the admin area.
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.99.1
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Charts {

	/**
	 * The loader that's responsible for maintaining and
	 * registering all hooks that power the plugin.
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      CWRA_Google_Charts_Loader    $loader    Maintains and
	 *     registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely
	 *     identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The instance of the public facing class; responsible for rendering
	 * the output of the gutenberg block.
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      CWRA_Google_Charts_Public $plugin_public   The
	 *     public-facing class. Needs to be passed to the admin class, so
	 *     that the block can register the generation function.
	 */
	protected $plugin_public;

	/**
	 * Plugin debugger
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      CWRA_Google_Charts_Debug $debugger   A collection
	 *     of debugging tools.
	 */
	public $debugger;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be
	 * used throughout the plugin.  Load the dependencies, define
	 * the locale, and set the hooks for the admin area and the
	 * public-facing side of the site.
	 *
	 * @since    0.99.1
	 */
	public function __construct() {
		if ( defined( 'CWRA_GOOGLE_CHARTS_VERSION' ) ) {
			$this->version = CWRA_GOOGLE_CHARTS_VERSION;
		} else {
			$this->version = '0.99.1';
		}
		$this->plugin_name = 'cwra-google-charts';

		// normally this is all handled by the loader, but we want
		// debugging very early
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'includes/class-cwra-google-charts-debug.php';
		$this->debugger = new CWRA_Google_Charts_Debug();

		/*
		 * XXX cwr: not currenty outputting to screen, so we don't
		 * need this.
		add_action('wp_enqueue_scripts', array( $this, 
		    'enqueue_debug_style'), 9);
		 */

		$this->load_dependencies();
		$this->set_locale();
		// need public to come first as it gets passed in admin
		$this->define_public_hooks();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CWRA_Google_Charts_Loader. Orchestrates the hooks of the
	 *   plugin.
	 * - CWRA_Google_Charts_i18n. Defines internationalization
	 *   functionality.
	 * - CWRA_Google_Charts_Admin. Defines all hooks for the admin
	 *   area.
	 * - CWRA_Google_Charts_Public. Defines all hooks for the public
	 *   side of the site.
	 * - CWRA_Google_Charts_Data. Defines all hooks for handling
	 *   the data used by Google Charts.
	 * - CWRA_Google_Charts_API. Initialize and register the
	 *   plugin specific RESTful API routes and endpoints.
	 *
	 * Create an instance of the loader which will be used to register the
	 * hooks with WordPress.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and
		 * filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'includes/class-cwra-google-charts-loader.php';

		/**
		 * The class responsible for defining internationalization
		 * functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'includes/class-cwra-google-charts-i18n.php';

		/**
		 * The class responsible for handling data management.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'includes/class-cwra-google-charts-data.php';

		/**
		 * The class responsible for defining all actions that occur
		 * in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'admin/class-cwra-google-charts-admin.php';

		/**
		 * The class responsible for defining all actions that occur
		 * in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'admin/class-cwra-google-charts-api.php';

		/**
		 * The class responsible for defining all actions that occur
		 * in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) )
		    . 'public/class-cwra-google-charts-public.php';

		$this->loader = new CWRA_Google_Charts_Loader( 
		    $this->debugger );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CWRA_Google_Charts_i18n class in order to set the
	 * domain and to register the hook with WordPress.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CWRA_Google_Charts_i18n(
		    $this->debugger );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n,
		    'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new CWRA_Google_Charts_Admin(
		    $this->get_plugin_name(), $this->get_version(),
		    $this->get_public(), $this->debugger );

		// general admin functionality for the plugin
		$this->loader->add_action( 'admin_enqueue_scripts',
		    $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts',
		    $plugin_admin, 'enqueue_scripts' );

		// gutenberg editor specific functionality
		$this->loader->add_action( 'enqueue_block_editor_assets',
		    $plugin_admin, 'enqueue_gutenberg_styles' );
		$this->loader->add_action( 'init',
		    $plugin_admin, 'enqueue_gutenberg_scripts' );

		// RESTful API
		$plugin_api = new CWRA_Google_Charts_API(
		    $this->get_plugin_name(), $this->get_version(),
		    $this->debugger);
		$this->loader->add_action( 'rest_api_init',
		    $plugin_api, 'rest_init' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->plugin_public = new CWRA_Google_Charts_Public(
		    $this->get_plugin_name(), $this->get_version(),
		    $this->debugger );

		$this->loader->add_action( 'wp_enqueue_scripts',
		    $this->plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'init',
		    $this->plugin_public, 'enqueue_scripts' );

	}

	/**
	 * The reference to the class that handles the front-end.
	 *
	 * @since     0.99.1
	 * @return    CWRA_Google_Charts_Public    Handles public-facing
	 *     functionality
	 */
	protected function get_public() {
		return $this->plugin_public;
	}

	/*
	 * XXX cwr: comment this out for the moment, as we're not outputting
	 * to screen, only to log, so we don't need styles yet.
	 *
	public function enqueue_debug_style() {
		wp_enqueue_style( $this->plugin_name . '-debug',
		    plugin_dir_url( __FILE__ )
		    . '../public/css/cwra-google-charts-debug.css',
		    array(), $this->version, 'all' );
	}
	*/

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.99.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it
	 * within the context of WordPress and to define internationalization
	 * functionality.
	 *
	 * @since     0.99.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks
	 * with the plugin.
	 *
	 * @since     0.99.1
	 * @return    CWRA_Google_Charts_Loader    Orchestrates the hooks
	 *     of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.99.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
