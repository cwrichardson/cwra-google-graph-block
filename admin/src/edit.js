/**
 * WordPress dependencies
 */

import apiFetch from '@wordpress/api-fetch';
import {
	Button,
	TextControl,
	ToggleControl,
	SelectControl,
	Spinner} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { Fragment, useCallback, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { CHART_TYPES } from './constants';
import * as chartLib from './chart';

export default function CwraGoolgeGraphEdit( props ) {
	const {
		attributes: {
		    cwraggBaseId,
		    cwraggChartType,
		    cwraggDataSourceType,
		    cwraggDataSource,
		    cwraggLocalFile,
		    cwraggTitle,
		    cwraggUserCanEdit
		},
		clientId,
		setAttributes,
	} = props;
	const attributes = props.attributes;

	const { createErrorNotice } = useDispatch( 'core/notices' );
	const [ isValidating, setIsValidating ] = useState( false );
	const post_id = wp.data.select("core/editor").getCurrentPostId();
	console.log('Post ID is ', post_id);

	// save the clientId as an attribute so we can use it on public side
	setAttributes( { cwraggBaseId: clientId } );

	let chart = {};
	switch ( cwraggChartType ) {
	    case 'line':
		chart = new chartLib.LineChart( { attributes, setAttributes } );
		break;
	    case 'pie':
		chart = new chartLib.PieChart( { attributes, setAttributes } );
		break;
	    default:
		createErrorNotice(
			sprintf(
			    __( 'Unknown chart type: %s. %s',
				'cwraggb' ), cwraggChartType, error.message),
			{
				id: 'cwragg-instantiate-error',
				type: 'snackbar'
			}
		);
	}


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
			setAttributes( { cwraggLocalFile: localFileName } );
		}).catch( ( error ) => {
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

	function ChartEdit( { title = '', userCanEdit = false, chartType,
	    setAttributes } ) {

		function setTextAttr ( attr, value ) {
			setAttributes( { [ attr ]: value } );
		}

		return (
		    <>
			<div>
				<div>
				    <TextControl
				      label={ __( 'Data URL', 'cwraggb' ) }
				      help={ __( 'Enter the URL from which to get '
					+ 'the data.', 'cwraggb') }
				      value={ cwraggDataSource }
				      onChange={ (newDataSource) => {
					setAttributes( {
					  cwraggDataSource: newDataSource } )
				      }} />
				    <Button
				      onClick={ validate }
				      label={ __( 'Retrieve', 'cwraggb') }
				      aria-disabled={ isValidating }
				      disabled={ isValidating }
				      isPrimary>Retrieve</Button>
				    { isValidating && <Spinner /> }
				</div>
			</div>
		    </>
		);
	}

	return (
	  <>
	    { chart.sidebar() }
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
	    </Fragment>
	  </>
	);
}
