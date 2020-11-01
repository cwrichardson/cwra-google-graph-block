<?php

/**
 * Data management
 *
 * A class definition that handles the data management for the graphs.
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 */

/**
 * The core data class.
 *
 *
 * @since      0.99.1
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Graph_Block_Data {


	/**
	 * Class constructor
	 *
	 * @since    0.99.1
	 */
	public function __construct() {
	}

	/**
	 * Create the data storage directory.
	 *
	 * Checks for existence of
	 * wp-content/uploads/plugin_name and creates it if it doesn't exist. 
	 * This directory is where data files are stored (either when uploaded
	 * directly, or grabbed from a remote website/API.
	 *
	 * @since    0.99.1
	 */
	public function make_data_dir() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if (! WP_Filesystem()) {
			return new WP_Error( 'filesystem_problem',
			    __('Call to WP_Filesystem failed.',
			    'cwra_google_graph_block') );
		}

		global $wp_filesystem;
		$upload_dir = wp_upload_dir();

		if ( $upload_dir['error'] ) {
			return new WP_Error( 'upload_dir_problem',
			__('There was a problem detecting the upload dir.',
			'cwra_google_graph_block') );
		} else {
			$upload_dir = trailingslashit($upload_dir['basedir'])
			    . 'cwra-google-graph-block';
		}

		if ( ! $wp_filesystem->exists($upload_dir) ) {
			if (! $wp_filesystem->mkdir($upload_dir) ) {
				return new WP_Error( 'no_can_make_dir',
				    __("Couldn't create upload dir.",
				    'cwra_google_graph_block'));
			}

			// success. Dir didn't exist, and we made it
			return;
		} else if ( ! $wp_filesystem->is_dir($upload_dir) ) {
			return new WP_Error( 'upload_dir_not_a_dir',
			    __("Plugin upload directory exists but isn't"
			    . "a directory", 'cwra_google_graph_block'));
		}

		// dir was already there. Good to go!
		return true;
	}

}
