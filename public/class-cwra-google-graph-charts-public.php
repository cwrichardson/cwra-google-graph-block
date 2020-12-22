<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/public
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Charts_Public {

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
	 * @var      CWRA_Google_Charts_Debug    $debugger   Debugger
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
		        . 'css/cwra-google-charts-public.css',
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
		        . 'js/cwra-google-charts-public.js',
		    array( $this->plugin_name . 'googlecharts', 'jquery' ),
		    $this->date_version(
		        'js/cwra-google-charts-public.js'), false );
		wp_localize_script( $this->plugin_name . '-public',
		    'cwragc',
		    array(
		    	'contentdir' => wp_upload_dir()['baseurl']
			    . '/cwragc'
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
		if ( ! array_key_exists('cwragcLocalFile',
		    $block_attributes) ) {
		        return '<div id="'
			    . print_r($block_attributes["cwragcBaseId"], true)
		    	    . '_control_div" style="width: 100%; '
			    . 'min-height: 50px;" class="cwragc"></div>';
		}

		$controlEl = '<div id="'
		    . print_r($block_attributes["cwragcBaseId"], true)
		    . '_control_div" style="width: 100%; min-height: 50px;"'
		    . ' class="cwragc cwragc_control '
		    . ' cwragc_chart_column_selector">'
		    . '</div>';

		$chartEl = '<div id="'
		    . print_r($block_attributes["cwragcBaseId"], true)
		    . '" style="width: 100%; height: 300px;" ';

		$chartEl .= 'class="cwragc cwragc_chart"'
		    . ' data-cwragc-src="'
		    . print_r($block_attributes["cwragcLocalFile"], true)
		    . '" data-cwragc-type="'
		    . print_r($block_attributes["cwragcChartType"], true)
		    . '"';

		if ( array_key_exists('cwragcHeight', $block_attributes) ) {
			$chartEl .= ' data-cwragc-height="'
			    . print_r($block_attributes["cwragcHeight"], true)
			    . '"';
		}

		if ( array_key_exists('cwragcWidth', $block_attributes) ) {
			$chartEl .= ' data-cwragc-width="'
			    . print_r($block_attributes["cwragcWidth"], true)
			    . '"';
		}

		if ( array_key_exists('cwragcTitle', $block_attributes) ) {
			$chartEl .= ' data-cwragc-title="'
			    . print_r($block_attributes["cwragcTitle"], true)
			    . '"';
		}

		if ( array_key_exists('cwragcHAxisTitle', $block_attributes) ) {
			$chartEl .= ' data-cwragc-haxis-title="'
			    . print_r($block_attributes["cwragcHAxisTitle"],
			        true)
			    . '"';
		}

		if ( array_key_exists('cwragcVAxisTitle', $block_attributes) ) {
			$chartEl .= ' data-cwragc-vaxis-title="'
			    . print_r($block_attributes["cwragcVAxisTitle"],
			        true)
			    . '"';
		}

		$chartEl .= '></div>';

		$colSelectEl = '<div id="'
		    . print_r($block_attributes["cwragcBaseId"], true)
		    . '_col_control_div" style="min-height: 50px;"'
		    . ' class="cwragc cwragc_control cwragc_col_control'
		    . ' form-group">'
		    . '</div>';

		$rangeSliderEl = '<div id="'
		    . print_r($block_attributes["cwragcBaseId"], true)
		    . '_range_control_div" style="min-height: 50px; display: none;"'
		    . ' class="cwragc cwragc_control '
		    . 'cwragc_range_control form-group">'
		    . '</div>';

		$el = '<div id="'
		    . print_r($block_attributes["cwragcBaseId"], true)
		    . '_dashboard_div"'
		    . ' class="cwragc cwragc_dashboard">'
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
