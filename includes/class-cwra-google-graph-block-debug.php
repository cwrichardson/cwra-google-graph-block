<?php

/**
 * Add debugging output
 *
 * A class definition that includes attributes and functions used
 * to output debugging information
 *
 * @link       https://www.chrisrichardson.info
 * @since      0.99.1
 *
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 */

/**
 * The core dubg class.
 *
 * This is used to define debugging functions.
 *
 * @since      0.99.1
 * @package    CWRA_Google_Graph_Block
 * @subpackage CWRA_Google_Graph_Block/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Graph_Block_Debug {


	/**
	 * Are we debugging?
	 *
	 * @since    0.99.1
	 * @access   protected
	 * @var      bool    $enabled    Whether debugging is enabled or not
	 *
	 */
	protected $enabled = false;

	/**
	 * Fire up debugging!
	 *
	 * @since    0.99.1
	 */
	public function __construct() {
		// conditionally load the debugging code
		if ( (defined( 'CWRA_GOOGLE_GRAPH_BLOCK_DEBUG' ) 
		    && CWRA_GOOGLE_GRAPH_BLOCK_DEBUG === true )
		    || WP_DEBUG ) {
		    	$this->enabled = true;
		}
	}

	/**
	 * Output debugging info
	 *
	 * @since    0.99.1
	 * @access   public
	 */
	public function debug( $code ) {
		if ( ! $this->enabled ) return;
		if (WP_DEBUG_LOG) {
			$this->print_log( $code );
			$this->print_html( $code );
		} else {
			$this->print_html( $code );
		}
	}

	/**
	 * Pretty print debugging to screen.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function print_html( $code ) {

		$output = '';

		if ( is_null( $code ) || is_string($code) || is_int( $code )
		    || is_bool($code) || is_float( $code ) 
		    || is_object($code) ) {
			$output .= print_r( $code, true );
		} else {
			$output .=  $code;
		}

		error_log( $output );

	}

	/**
	 * Output to php log
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function print_log( $code ) {

		if ( is_null( $code ) || is_string($code) || is_int( $code )
		    || is_bool($code) || is_float( $code ) ) {
			$code = var_export( $code, true );
		} else {
			$code = print_r( $code, true );
		}

		error_log( $code );

	}

}
