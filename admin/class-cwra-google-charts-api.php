<?php

/**
 * Extensions to the Wordpress RESTful API
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/admin
 */

/**
 * The plugin API.
 *
 * Registers plugin specific routes and endpoints.
 *
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/admin
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Charts_API {

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
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version   The version of this plugin.
	 * @param    string    $debugger   The debugger.
	 */
	public function __construct( $plugin_name, $version, $debugger) {
		$this->debugger = $debugger;
		$this->debugger->debug('API definition initiated');
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Initialize the REST routes.
	 *
	 * @since    0.99.1
	 */
	public function rest_init() {
		register_rest_route(
		    'cwragc/v1',
		    '/setremotedatasrc',
		    array(
		    	'methods'	=> 'POST',
			'callback'	=> array( $this, 'set_remote_data_src'),
			'args'		=> $this->set_remote_data_src_args(),
			'permission_callback' => array( $this,
			    'can_edit_permissions_check' ),
		        'schema' => array( $this, 'set_remote_data_src_schema' )
		    )
		);
	}

	/**
	 * Set a remote data src (a URL to a CSV or JSON file).
	 *
	 * @since 0.99.1
	 * @param	WP_REST_Request	$request The request from the REST call
	 *
	 * @return	WP_REST_Response or WP_Error
	 */
	public function set_remote_data_src( WP_REST_Request $request ) {
		$url = $request['url'];

		// If the function it's not available, require it.
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$tmp_file = download_url( $url );

		if ( is_wp_error( $tmp_file) ) return $tmp_file;

		global $wp_filesystem;
		$upload_dir = wp_upload_dir();

		if ( $upload_dir['error'] ) {
			return new WP_Error( 'upload_dir_problem',
			    __('There was a problem detecting the upload dir.',
			    'cwragc') );
		} else {
			$upload_dir = trailingslashit($upload_dir['basedir'])
			    . 'cwragc';
			wp_mkdir_p($upload_dir);
		}

		$save_name = wp_unique_filename($upload_dir, $tmp_file);

		copy( $tmp_file, $upload_dir . '/' . $save_name );
		@unlink( $tmp_file );

		return rest_ensure_response( $save_name );
	}

	/***
	 **
	 ** argument schema
	 **
	 ***/

	/**
	 * Get the argument schema for set_remote_data_src.
	 *
	 * @since 0.99.1
	 *
	 * @return	$args	array
	 */
	public function set_remote_data_src_args() {
		$args = array();

		$args['url'] = array(
		    'description'	=> esc_html__('The URL from which to '
		        . 'retrieve the data for the chart.',
		        'cwragc'),
		    'type'		=> 'string',
		    'required'		=> true,
		    'validate_callback' => array($this, 'validate_url'),
		    'sanitize_callback' => array($this, 'sanitize_url')
		);

		$args['type'] = array(
		    'description' 	=> esc_html__('The type of data in '
			. 'the file. Currently supports "csv" and '
			. '"json".', 'cwragc'),
		    'type'	  	=> 'string',
		    'default'		=> 'remote-csv',
		    'validate_callback'	=> array($this,
		        'validate_remote_data_src_type')
		);

		$args['postId'] = array(
		    'description' 	=> esc_html__('The ID of the post '
			. "for which you're trying to update a data source.",
			'cwragc'),
		    'type'	  	=> 'integer',
		    'required'		=> true,
		    'validate_callback'	=> array($this,
		        'validate_is_post_id')
		);

		return $args;
	}

	/***
	 **
	 ** General validate and sanitize callbacks.
	 **
	 **/

	/**
	 * validate post ID
	 *
	 * @since 0.99.1
	 *
	 * @param mixed           $value   Value of the parameter to validate.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter.
	 *
 	 * @return true|WP_Error  True if the data is valid, WP_Error otherwise.
	 */
	public function validate_is_post_id( $value, $request, $param ) {
		$attributes = $request->get_attributes();

		if ( isset( $attributes['args'][ $param ] ) ) {
			$argument = $attributes['args'][ $param ];
			// is it an int?
			if ( 'integer' !== $argument['type'] 
			    || ! is_int( $value )) {
				return new WP_Error( 'rest_invalid_param',
				    sprintf( esc_html__('%1$s is not of '
				    . 'type %2$s', 'cwragc'),
				    $param, 'integer' ),
				    array( 'status' => 400 ) );
			}

			// is there such a post?
			$post = get_post( $value );
			if (is_wp_error($post)) { return $post; }
		} else {
			// can't really be valid if it isn't there
			return new WP_Error( 'rest_invalid_param',
			    sprintf( esc_html__( '%s was not registered as a '
			    . 'request argument.', 'cwragc' ),
			    $param ), array( 'status' => 400 ) );
		}

		return true;
	}

	/**
	 * validate url
	 *
	 * @since 0.99.1
	 *
	 * @param mixed           $value   Value of the parameter to validate.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter.
	 *
 	 * @return true|WP_Error  True if the data is valid, WP_Error otherwise.
	 */
	public function validate_url( $value, $request, $param ) {
		$attributes = $request->get_attributes();

		if ( isset( $attributes['args'][ $param ] ) ) {
			$argument = $attributes['args'][ $param ];
			// is the argument a string?
			if ( 'string' === $argument['type'] 
			    && ! is_string( $value )) {
				return new WP_Error( 'rest_invalid_param',
				    sprintf( esc_html__('%1$s is not of '
				    . 'type %2$s', 'cwragc'),
				    $param, 'string' ),
				    array( 'status' => 400 ) );
			}

			// is the argument a url?
			if ( ! wp_http_validate_url( $value ) ) {
				return new WP_Error( 'rest_invalid_param',
				    sprintf( esc_html__('%1$s is not a valid '
				    . 'url.', 'cwragc'),
				    $value),
				    array( 'status' => 400 ) );
			}
		} else {
			// can't really be valid if it isn't there
			return new WP_Error( 'rest_invalid_param',
			    sprintf( esc_html__( '%s was not registered as a '
			    . 'request argument.', 'cwragc' ),
			    $param ), array( 'status' => 400 ) );
		}

		return true;
	}

	/**
	 * sanitize url
	 *
	 * @since 0.99.1
	 *
	 * @param mixed           $value   Value of the parameter to sanitize.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter.
	 *
 	 * @return string|WP_Error Sanitized url or WP_Error otherwise.
	 */
	public function sanitize_url( $value, $request, $param ) {
		$sane_url = wp_http_validate_url( $value );

		if ( $sane_url) {
			return $sane_url;
		}

		// That's insanity!
		//
		// This shouldn't happen, as it should have been already 
		// validated, but ... you never know.
		return new WP_Error( 'rest_api_sad',
		    esc_html__( "Your submitted URL can't be made sane.",
		    'cwragc' ), array( 'status' => 500 ) );
	}

	/***
	 **
	 ** End-point specific validate and sanitize callbacks.
	 **
	 **/

	/**
	 * Validate remote_data_src type
	 *
	 * @since 0.99.1
	 *
	 * @param mixed           $value   Value of the parameter to validate.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter.
	 *
 	 * @return true|WP_Error  True if the data is valid, WP_Error otherwise.
	 */
	public function validate_remote_data_src_type( $value, $request,
	    $param ) {
		$attributes = $request->get_attributes();

		// if not set, we have a default, so this is the only case
		// that matters
		if ( isset( $attributes['args'][ $param ] ) ) {
			$argument = $attributes['args'][ $param ];
			// is the argument a string?
			if ( 'string' === $argument['type'] 
			    && ! is_string( $value )) {
				return new WP_Error( 'rest_invalid_param',
				    sprintf( esc_html__('%1$s is not of '
				    . 'type %2$s', 'cwragc'),
				    $param, 'string' ),
				    array( 'status' => 400 ) );
			}

			// is it a string we understand?
			if ( ! ( 'remote-csv' === $value
			    || 'remote-json' === $value 
			    || 'upload-csv' === $value 
			    || 'upload-json' === $value 
			    ) ) {
				return new WP_Error( 'rest_invalid_param',
				    sprintf( esc_html__('%1$s is not a type '
				    . 'I understand.',
				    'cwragc'),
				    $value),
				    array( 'status' => 400 ) );
			}
		}

		return true;
	}

	/***
	 **
	 ** Permission checks
	 **
	 **/

	/**
	 * Checks if a given request has access to edit a post.
	 *
	 * @since 0.99.1
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has edit access for the
	 * 			 item, WP_Error object otherwise.
	 */
	public function can_edit_permissions_check( $request ) {
		$post = get_post( $request['postId'] );

		if (is_wp_error($post)) { return $post; }

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return new WP_Error( 'rest_cannot_edit',
			    esc_html__('Forbidden! '
			    . "You don't have permission to edit this post.",
			    'cwragc'), array( 'status' =>
			    rest_authorization_required_code() ));
		}

		return true;
	}

	/***
	 **
	 ** Schema defintions
	 **
	 **/

	/**
	 * Get the schema for set_remote_data_src.
	 *
	 * @since 0.99.1
	 * @return	$schema	array
	 */
	public function set_remote_data_src_schema() {
		$schema = array(
		    '$schema'		  => 'http://json-schema.org/'
		        . 'draft-04/schema#',
		    'title'		  => 'remote_data_src',
		    'type'		  => 'object',
		    'properties'	  => array(
		        'local-data-src-name'  => array(
			    'description' => esc_html__('The local file name '
			        . 'where the remote data was stored.',
				'cwragc'),
			    'type'	  => 'string')
			)
		);

		return $schema;
	}

}
