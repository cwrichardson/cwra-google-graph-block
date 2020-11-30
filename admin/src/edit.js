/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
	SelectControl,
	Spinner} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { Fragment, useCallback, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { CHART_TYPES } from './constants';

function ChartEdit( { title = '', userCanEdit = false, chartType,
    setAttributes } ) {

	function setTextAttr ( attr, value ) {
		setAttributes( { [ attr ]: value } );
	}

	function onSetTitle ( value ) {
		setTextAttr('cwraggTitle', value);
	}

	function onSetHAxisTitle ( value ) {
		return;
		/*( setTextAttr('cwraggHAxisTitle', value);*/
	}

	function onSetVAxisTitle ( value ) {
		return;
		/*setTextAttr('cwraggHAxisTitle', value);*/
	}

	return (
	    <>
		<div>
			<TextControl
			    label={ __( 'Title', 'cwraggb' ) }
			    value={ title || '' }
			    onChange={ onSetTitle }
			/>
			<TextControl
			    label={ __( 'Horizontal Axis Title', 'cwraggb' ) }
			    value={ /*cwraggHAxisTitle || ''*/ '' }
			    onChange={ onSetHAxisTitle }
			/>
			<TextControl
			    label={ __( 'Vertical Axis Title', 'cwraggb' ) }
			    value={ /*cwraggVvAxisTitle || ''*/ '' }
			    onChange={ onSetVAxisTitle }
			/>
			<ToggleControl
			    label={ __( 'Allow user interaction', 'cwraggb' ) }
			    help={ userCanEdit
			        ? __( 'Google visualization controls enabled.',
				    'cwraggb' )
				: __( 'Google visualization controls disabled.',
				    'cwraggb' ) }
			    checked={ userCanEdit }
			    onChange={ () => setAttributes( { cwraggUserCanEdit:
			        ! userCanEdit } ) } />
		</div>
	    </>
	);
}

export default function CwraGoolgeGraphEdit( props ) {
	const {
		attributes: {
		    cwraggChartType,
		    cwraggDataSourceType,
		    cwraggDataSource,
		    cwraggLocalFile,
		    cwraggTitle,
		    cwraggUserCanEdit
		},
		setAttributes,
	} = props;

	const { createErrorNotice } = useDispatch( 'core/notices' );
	const [ isValidating, setIsValidating ] = useState( false );
	const post_id = wp.data.select("core/editor").getCurrentPostId();
	console.log('Post ID is ', post_id);

	// fill out the key-value pairs for the select chart-type dropdown
	let chartSelectType = [];
	if (CHART_TYPES) {
		for (const key in CHART_TYPES) {
			chartSelectType.push( { value: __( key, 'cwraggb' ),
			    label: CHART_TYPES[key] }
			);
		}
	}

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
				    __( 'Could not validate data source. %s',
				        'cwraggb' ), error.message),
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
		      title={ __( 'Data Configuration', 'cwraggb' )}
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
		<div className={ "graph_settings" }>
		    <SelectControl
		      label={ __( 'Chart Type', 'cwraggb') }
		      value={ cwraggChartType }
		      options={ chartSelectType }
		      onChange={ (chartType) => {
		      	setAttributes( {
			  cwraggChartType: chartType } )
		      }} />
		</div>
		<ChartEdit
		    title={ cwraggTitle }
		    userCanEdit={ cwraggUserCanEdit }
		    chartType={ cwraggChartType }
		    setAttributes={ setAttributes } />
		<div className={ "it_worked" }>Generating graph from 
		  { cwraggDataSource }</div>
		<div>Local data source is { cwraggLocalFile }</div>
	    </Fragment>
	  </>
	);
}
