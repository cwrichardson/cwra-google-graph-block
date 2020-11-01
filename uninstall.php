<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query
 *   string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once
 *   sitewide.
 *
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Graph_Block
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Is the user allowed to do this?
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}
check_admin_referer( 'bulk-plugins' );

// Are we the right file to be doing this?
if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
	return;
}

// OK. Go ahead and uninstall.
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
if ( ! $wp_filesystem->rmdir($upload_dir, true) ) {
	return new WP_Error( 'uninstall_problem',
	    __('There was a problem removing the data directory.'
	    . ' Uninstall failed.', 'cwra-google-graph-block'));
}

return;
