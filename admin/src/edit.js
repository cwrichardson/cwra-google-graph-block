/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { TextControl } from '@wordpress/components';

export default function Edit( props ) {
	const {
		attributes: {  cwraggDataSource },
		setAttributes,
	} = props;
	return (
	    <Fragment>
	        <TextControl
	          label={ __( 'Data URL ' ) }
	          help={ __( 'Enter the URL from which to get '
		    + 'a CSV file with the data for the graph.') }
	          value={ cwraggDataSource }
	          onChange={ ( value ) => setAttributes( {
		    cwraggDataSource: value } ) } />
		<div class="it_worked">Generating graph from
		  { cwraggDataSource }</div>
	    </Fragment>
	);
}
