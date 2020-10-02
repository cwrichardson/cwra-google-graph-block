<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    Cwra_Google_Graph_Block
 * @subpackage Cwra_Google_Graph_Block/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.99.1
 * @package    Cwra_Google_Graph_Block
 * @subpackage Cwra_Google_Graph_Block/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class Cwra_Google_Graph_Block_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.99.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cwra-google-graph-block',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
