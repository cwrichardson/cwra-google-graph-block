/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { Button, TextControl, SelectControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';

export default function Edit( props ) {
	const {
		attributes: { cwraggDataSourceType, cwraggDataSource,
		    cwraggLocalFile },
		setAttributes,
	} = props;

	let default_dst = 'remote-csv';
	let is_valid = true;
	let validating = false;

	const post_id = wp.data.select("core/editor").getCurrentPostId();

	const DataSrcSelectControl = withState( {
		dataSourceType: cwraggDataSourceType
	} )( ( { dataSourceType, setState } ) => (
		<SelectControl
		    label="Data Source"
		    value={ dataSourceType }
		    options={ [
		        { label: 'Remote CSV', value: 'remote-csv' },
		        { label: 'Remote JSON', value: 'remote-json' },
		        { label: 'Upload CSV', value: 'upload-csv' },
		        { label: 'Upload JSON', value: 'upload-json' }
		    ] }
		    onChange={ ( dataSourceType ) => {
		        setState( { dataSourceType } );
			setAttributes( { 
			    cwraggDataSourceType: dataSourceType } ) } }
		/>
	) );

	const DataSrcTextControl = withState( {
		dataSource: cwraggDataSource
	} )( ( { dataSource, setState } ) => (
		<TextControl
		    label={ __( 'Data URL', 'cwra-google-graph-block' ) }
		    help={ __( 'Enter the URL from which to get '
		      + 'a JSON file with the data for the graph.',
		      'cwra-google-graph-block') }
		    value={ dataSource }
		    onChange={ (dataSource) => {
			setState( { dataSource } );
		    	setAttributes( { cwraggDataSource: dataSource } );
		    }} />
	) );

	const DataButton = ( ) => {
		return (<Button
		    isPrimary
		    onClick={ doValidate }>{ __("Validate", 
		        'cwra-google-graph-block')}</Button>);
	}

	const doValidate = ( ) => {
		let dst = cwraggDataSourceType;
		if ( !dst ) {
			dst = default_dst;
		}
		console.log('Doing validation');
		apiFetch( {
		    path: '/cwraggb/v1/setremotedatasrc',
		    method: 'POST',
		    data: { 'url': cwraggDataSource,
		    	    'type': cwraggDataSourceType,
			    'postId': post_id } } 
		).then( ( localFileName ) => {
			setAttributes( {
			    cwraggLocalFile: localFileName
			    } );
			return localFileName;
		}).catch( ( error ) => {
			console.log('doh!');
		});
	};

	return (
	    <Fragment>
		<div>
		    <DataSrcSelectControl />
		    <DataButton />
		</div>
		<div>
	            <DataSrcTextControl />
		</div>
		<div className={ "it_worked" }>Generating graph from 
		  { cwraggDataSource }</div>
	    </Fragment>
	);
}
