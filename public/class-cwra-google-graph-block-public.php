<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/public
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Graph_Block_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.99.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.99.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Debugger
	 *
	 * @since    0.99.1
	 * @access   private
	 * @var      CWRA_Google_Graph_Block_Debug    $debugger   Debugger
	 *     instantiation.
	 */
	private $debugger;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.99.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $debugger ) {

		$this->debugger = $debugger;
		$this->debugger->debug('Public initiated');
		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.99.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CWRA_Google_Graph_Block_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CWRA_Google_Graph_Block_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cwra-google-graph-block-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.99.1
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name,
		    plugin_dir_url( __FILE__ )
		        . 'js/cwra-google-graph-block-public.js',
		    array( $this->plugin_name . 'googlecharts', 'jquery' ),
		    $this->date_version(
		        'js/cwra-google-graph-block-public.js'), false );

		wp_enqueue_script( $this->plugin_name . 'googlecharts',
		    'https://www.gstatic.com/charts/loader.js',
		    array(), $this->version, false );

	}

	/**
	 * Render the public facing DOM
	 *
	 * @since    0.99.1
	 */
	public function render( $block_attributes, $content = '' ) {
		$this->debugger->debug('Outputting to public.');
		return sprintf(
		    '<div id="chart_div"></div>'
		);
	}

}
