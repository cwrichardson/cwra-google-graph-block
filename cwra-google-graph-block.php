<?php

/**
 *
 * @link              https://www.chrisrichardson.info
 * @since             0.99.1
 * @package           CWRA_Google_Graph_Block
 *
 * @wordpress-plugin
 * Plugin Name:       Google Graph Block
 * Plugin URI:        https://www.cwrichardson.com/open-source/cwra-google-graph-block
 * Description:       A Gutenberg block for the Google Graph API
 * Version:           0.99.1
 * Author:            Chris Richardson
 * Author URI:        https://www.chrisrichardson.info
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cwra-google-graph-block
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'CWRA_GOOGLE_GRAPH_BLOCK_VERSION', '0.99.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in
 * includes/class-cwra-google-graph-block-activator.php
 */
function activate_cwra_google_graph_block() {
	require_once plugin_dir_path( __FILE__ )
	    . 'includes/class-cwra-google-graph-block-activator.php';
	CWRA_Google_Graph_Block_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in
 * includes/class-cwra-google-graph-block-deactivator.php
 */
function deactivate_cwra_google_graph_block() {
	require_once plugin_dir_path( __FILE__ )
	    . 'includes/class-cwra-google-graph-block-deactivator.php';
	CWRA_Google_Graph_Block_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cwra_google_graph_block' );
register_deactivation_hook( __FILE__, 'deactivate_cwra_google_graph_block' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ )
    . 'includes/class-cwra-google-graph-block.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.99.1
 */
function run_cwra_google_graph_block() {

	$plugin = new CWRA_Google_Graph_Block();
	$plugin->run();

}
run_cwra_google_graph_block();
