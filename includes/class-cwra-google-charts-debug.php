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
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/includes
 */

/**
 * The core dubg class.
 *
 * This is used to define debugging functions.
 *
 * @since      0.99.1
 * @package    CWRA_Google_Charts
 * @subpackage CWRA_Google_Charts/includes
 * @author     Chris Richardson <cwr@cwrichardson.com>
 */
class CWRA_Google_Charts_Debug {


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
		if ( (defined( 'CWRA_GOOGLE_CHARTS_DEBUG' ) 
		    && CWRA_GOOGLE_CHARTS_DEBUG === true )
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
		} else {
			$this->print_html( $code );
		}
	}

	/**
	 * Output all actions and filters
	 *
	 * @since    0.99.1
	 * @access   public
	 */
	public function getFilters() {
		if ( ! $this->enabled ) return;

		$code = "Starting filter dump ... \n";
		foreach ( $GLOBALS['wp_filter'] as $tag => $priority_sets ) {
			$code .= "debugging for tag "
			    . print_r($tag, true) . "\n";

			/* Each [priority] */
			foreach ( $priority_sets as $priority => $idxs ) {
				$code .= "\tpriority " . $priority . ":\n";

				/* Each [callback] */
				foreach ( $idxs as $idx => $callback ) {
					if ( gettype($callback['function']) ==
					    'object' ) {
						$function = '{ closure }';
					} else if ( is_array(
					    $callback['function'] ) ) {
						$function = print_r(
						    $callback['function'][0],
						    true );
						$function .= ':: ' . print_r(
						    $callback['function'][1],
							true );
					} else {
						$function =
						    $callback['function'];
					}

					$code .= "\t\t" . $function . '('
					    . $callback['accepted_args']
					    . ' arguments)';
				}
				$code .= "\n";
			}
			$code .= "\n";

			$this->debug($code);
		}
	}

	/**
	 * Pretty print debugging to screen.
	 *
	 * @since    0.99.1
	 * @access   private
	 */
	private function print_html( $code ) {

		$output = 'CWRAGGB: \n';

		if ( is_null( $code ) || is_string($code) || is_int( $code )
		    || is_bool($code) || is_float( $code ) 
		    || is_object($code) || is_array($code) ) {
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

		error_log( "CWRAGGB: " . $code );

	}

}
