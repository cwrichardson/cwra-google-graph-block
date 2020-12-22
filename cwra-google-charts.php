<?php

/**
 *
 * @link              https://www.chrisrichardson.info
 * @since             0.99.1
 * @package           CWRA_Google_Charts
 *
 * @wordpress-plugin
 * Plugin Name:       Google Chart Block
 * Plugin URI:        https://www.cwrichardson.com/open-source/cwra-google-charts
 * Description:       A Gutenberg block for the Google Chart API
 * Version:           0.99.1
 * Author:            Chris Richardson
 * Author URI:        https://www.chrisrichardson.info
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cwragc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'CWRA_GOOGLE_CHARTS_VERSION', '0.99.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in
 * includes/class-cwra-google-charts-activator.php
 */
function activate_cwra_google_charts() {
	require_once plugin_dir_path( __FILE__ )
	    . 'includes/class-cwra-google-charts-activator.php';
	CWRA_Google_Charts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in
 * includes/class-cwra-google-charts-deactivator.php
 */
function deactivate_cwra_google_charts() {
	require_once plugin_dir_path( __FILE__ )
	    . 'includes/class-cwra-google-charts-deactivator.php';
	CWRA_Google_Charts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cwra_google_charts' );
register_deactivation_hook( __FILE__, 'deactivate_cwra_google_charts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ )
    . 'includes/class-cwra-google-charts.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.99.1
 */
function run_cwra_google_charts() {

	$plugin = new CWRA_Google_Charts();
	$plugin->run();
}
run_cwra_google_charts();
