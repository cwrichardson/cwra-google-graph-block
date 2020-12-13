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

		wp_enqueue_style( $this->plugin_name . '-public-styles',
		    plugin_dir_url( __FILE__ )
		        . 'css/cwra-google-graph-block-public.css',
		    array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . 'bootstrap',
		    'https://cdn.jsdelivr.net/npm/bootstrap'
		        . '@4.5.3/dist/css/bootstrap.min.css',
		    array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.99.1
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . '-public',
		    plugin_dir_url( __FILE__ )
		        . 'js/cwra-google-graph-block-public.js',
		    array( $this->plugin_name . 'googlecharts', 'jquery' ),
		    $this->date_version(
		        'js/cwra-google-graph-block-public.js'), false );
		wp_localize_script( $this->plugin_name . '-public',
		    'cwraggbp',
		    array(
		    	'contentdir' => wp_upload_dir()['baseurl']
			    . '/cwraggb'
		    ));

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

		// if we don't have data, we can't do anything
		if ( ! array_key_exists('cwraggLocalFile',
		    $block_attributes) ) {
		        return '<div id="'
			    . print_r($block_attributes["cwraggBaseId"], true)
		    	    . '_control_div" style="width: 100%; '
			    . 'min-height: 50px;" class="cwraggbp"></div>';
		}

		$controlEl = '<div id="'
		    . print_r($block_attributes["cwraggBaseId"], true)
		    . '_control_div" style="width: 100%; min-height: 50px;"'
		    . ' class="cwraggbp cwraggbp_control '
		    . ' cwraggbp_chart_column_selector">'
		    . '</div>';

		$chartEl = '<div id="'
		    . print_r($block_attributes["cwraggBaseId"], true)
		    . '" style="width: 100%; height: 300px;" ';

		$chartEl .= 'class="cwraggbp cwraggbp_chart"'
		    . ' data-cwraggbp-src="'
		    . print_r($block_attributes["cwraggLocalFile"], true)
		    . '" data-cwraggbp-type="'
		    . print_r($block_attributes["cwraggChartType"], true)
		    . '"';

		if ( array_key_exists('cwraggHeight', $block_attributes) ) {
			$chartEl .= ' data-cwraggbp-height="'
			    . print_r($block_attributes["cwraggHeight"], true)
			    . '"';
		}

		if ( array_key_exists('cwraggWidth', $block_attributes) ) {
			$chartEl .= ' data-cwraggbp-width="'
			    . print_r($block_attributes["cwraggWidth"], true)
			    . '"';
		}

		if ( array_key_exists('cwraggTitle', $block_attributes) ) {
			$chartEl .= ' data-cwraggbp-title="'
			    . print_r($block_attributes["cwraggTitle"], true)
			    . '"';
		}

		if ( array_key_exists('cwraggHAxisTitle', $block_attributes) ) {
			$chartEl .= ' data-cwraggbp-haxis-title="'
			    . print_r($block_attributes["cwraggHAxisTitle"],
			        true)
			    . '"';
		}

		if ( array_key_exists('cwraggVAxisTitle', $block_attributes) ) {
			$chartEl .= ' data-cwraggbp-vaxis-title="'
			    . print_r($block_attributes["cwraggVAxisTitle"],
			        true)
			    . '"';
		}

		$chartEl .= '></div>';

		$colSelectEl = '<div id="'
		    . print_r($block_attributes["cwraggBaseId"], true)
		    . '_col_control_div" style="min-height: 50px;"'
		    . ' class="cwraggbp cwraggbp_control cwraggbp_col_control'
		    . ' form-group">'
		    . '</div>';

		$rangeSliderEl = '<div id="'
		    . print_r($block_attributes["cwraggBaseId"], true)
		    . '_range_control_div" style="min-height: 50px; display: none;"'
		    . ' class="cwraggbp cwraggbp_control '
		    . 'cwraggbp_range_control form-group">'
		    . '</div>';

		$el = '<div id="'
		    . print_r($block_attributes["cwraggBaseId"], true)
		    . '_dashboard_div"'
		    . ' class="cwraggbp cwraggbp_dashboard">'
		    . '<div class="outer-wrapper">'
		    . '<div class="chart-body">'
		    . '<div>'
		    . '<div>' . $colSelectEl . $rangeSliderEl . '</div>'
		    . '</div>'
		    . '<div>'
		    . '<div>'
		    . $chartEl
		    . '</div>'
		    . '</div>'
		    . '<div>'
		    . '<div>'
		    . $controlEl
		    . '</div>'
		    . '</div>'
		    . '</div>'
		    . '</div>'
		    . '</div>';

		return $el;
	}

}
