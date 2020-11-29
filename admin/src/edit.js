/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
	InspectorControls,
	URLInput,
	useBlockProps } from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	PanelRow,
	TextControl,
	SelectControl,
	Spinner} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { Fragment, useState } from '@wordpress/element';

export default function CwraGoolgeGraphEdit( props ) {
	const {
		attributes: { cwraggDataSourceType, cwraggDataSource,
		    cwraggLocalFile },
		setAttributes,
	} = props;

	const { createErrorNotice } = useDispatch( 'core/notices' );
	const [ isValidating, setIsValidating ] = useState( false );
	const post_id = wp.data.select("core/editor").getCurrentPostId();
	console.log('Post ID is ', post_id);

	function validate() {
		setIsValidating( true );

		apiFetch( {
		    path: '/cwraggb/v1/setremotedatasrc',
		    method: 'POST',
		    data: { 'url': cwraggDataSource,
		    	    'type': 'remote-csv',
			    'postId': post_id } } 
		).then( ( localFileName ) => {
			console.log('API returned ', localFileName);
			setAttributes( { cwraggLocalFile: localFileName } );
			console.log('Got local file name ', localFileName);
			console.log('cwraggLocalFile is ', cwraggLocalFile);
		}).catch( ( error ) => {
			console.log('doh!', error);
			createErrorNotice(
				sprintf(
				    __( 'Could not validate data source. %s' ),
				    error.message),
				{
					id: 'cwragg-validate-error',
					type: 'snackbar'
				}
			);
		}).finally( () => {
			setIsValidating( false );
		});
	}

	return (
	  <>
	    <Fragment>
	    	<InspectorControls>
		    <PanelBody
		      title={__( 'Data Configuration' )}
		      initialOpen={ true }>
		    	<PanelRow>
			    <TextControl
			      label={ __( 'Data URL', 'cwraggb' ) }
			      help={ __( 'Enter the URL from which to get '
			        + 'the data.', 'cwraggb') }
			      value={ cwraggDataSource }
			      onChange={ (newDataSource) => {
				setAttributes( {
				  cwraggDataSource: newDataSource } )
			      }} />
			</PanelRow>
			<PanelRow>
			    <Button
			      onClick={ validate }
			      label={ __( 'Retrieve', 'cwraggb') }
			      aria-disabled={ isValidating }
			      disabled={ isValidating }
			      isPrimary>Retrieve</Button>
			    { isValidating && <Spinner /> }
			</PanelRow>
		    </PanelBody>
		</InspectorControls>
	    </Fragment>
	    <Fragment>
		<div className={ "it_worked" }>Generating graph from 
		  { cwraggDataSource }</div>
		<div>Local data source is { cwraggLocalFile }</div>
	    </Fragment>
	  </>
	);
}
