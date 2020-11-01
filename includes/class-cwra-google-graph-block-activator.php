<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.99.1
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Graph_Block_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Long Description.
	 *
	 * @since    0.99.1
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;

		$plugin = isset( $_REQUEST['plugin'] )
		    ? $_REQUEST['plugin'] : '';

		$dm = new CWRA_Google_Graph_Block_Data();

		$dm->make_data_dir();
	}

}
