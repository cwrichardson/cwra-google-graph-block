<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.chrisrichardson.info
 * @since      1.0.0
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/admin
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Graph_Block_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Debugger
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      CWRA_Google_Graph_Block_Debug    $debugger   Debugger
	 *     instantiation.
	 */
	private $debugger;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version   The version of this plugin.
	 * @param    string    $version   The version of this plugin.
	 * @param    CWRA_Google_Graph_Block_Public    $plugin_public    The
	 *     instance of the class that handles public-facing activities.
	 */
	public function __construct( $plugin_name, $version, $plugin_public,
	    $debugger) {

		$this->debugger = $debugger;
		$this->debugger->debug('Admin initiated');
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_public = $plugin_public;

	}

	/**
	 * Append file modification time to version
	 */
	private function date_version( $suffix ) {
		return $this->version . '.'
		    . filemtime( plugin_dir_path( __FILE__ )
		        . $suffix );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name,
		    plugin_dir_url( __FILE__ )
		        . 'css/cwra-google-graph-block-admin.css',
		    array(),
		    $this->date_version(
		        'css/cwra-google-graph-block-admin.css'),
		    'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name,
		    plugin_dir_url( __FILE__ )
		        . 'js/cwra-google-graph-block-admin.js',
		    array( 'jquery' ),
		    $this->date_version(
		        'js/cwra-google-graph-block-admin.js'),
		    false );
	}

	/**
	 * Register the stylesheets for the block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_gutenberg_styles() {
		wp_enqueue_style( $this->plugin_name,
		    plugin_dir_url( __FILE__ )
		        . 'block/css/cwra-google-graph-block-gutenberg.css',
		    array( 'wp-blocks', 'wp-element' ),
		    $this->date_version(
		        'block/css/cwra-google-graph-block-gutenberg.css'),
		    'all' );
	}

	/**
	 * Register the JavaScript for the block editor in the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_gutenberg_scripts() {
		$asset_file = include( plugin_dir_path( __FILE__ )
		    . 'block/js/cwra-google-graph-block-admin.asset.php');

		// register the compiled JS for the backend
		wp_register_script( $this->plugin_name . '-block-edit',
		    plugin_dir_url( __FILE__ )
		        . 'block/js/cwra-google-graph-block-admin.js',
		    $asset_file['dependencies'],
		    $this->date_version(
		        'block/js/cwra-google-graph-block-admin.js'));

		$this->debugger->debug("Registering block type.");
		$this->debugger->debug("plugin_public is ");
		$this->debugger->debug( $this->plugin_public );
		register_block_type( 'cwra-google-graph-block/graph-block',
		    array(
		        'editor_script' => $this->plugin_name . '-block-edit',
			'render_callback' => array( $this->plugin_public,
			    'render')
		) );
	}

}
