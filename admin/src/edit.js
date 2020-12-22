/**
 * WordPress dependencies
 */

import apiFetch from '@wordpress/api-fetch';
import {
	Button,
	ButtonGroup,
	Disabled,
	Placeholder,
	TextControl,
	Spinner} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import { CHART_TYPES } from './constants';
import * as chartLib from './chart';

export default function CwraGoogleChartEdit( props ) {
	const {
		attributes: {
		    cwragcBaseId,
		    cwragcChartType,
		    cwragcDataSourceType,
		    cwragcDataSource,
		    cwragcLocalFile,
		    cwragcTitle,
		    cwragcUserCanEdit
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
	if ( ! cwragcBaseId ) {
		console.log('cwragcBaseId is ', cwragcBaseId);
		console.log('clientId is ', clientId);
		setAttributes( { cwragcBaseId: clientId } );
	}

	let chart = {};
	switch ( cwragcChartType ) {
	    case 'line':
		chart = new chartLib.LineChart( { attributes, setAttributes } );
		break;
	    case 'pie':
		chart = new chartLib.PieChart( { attributes, setAttributes } );
		break;
	    default:
		createErrorNotice(
			sprintf(
			    __( 'Unknown chart type: %s.',
				'cwragc' ), cwragcChartType),
			{
				id: 'cwragc-instantiate-error',
				type: 'snackbar'
			}
		);
	}


	// fill out the key-value pairs for the select chart-type dropdown
	let chartSelectType = [];
	if (CHART_TYPES) {
		for (const key in CHART_TYPES) {
			chartSelectType.push( { value: __( key, 'cwragc' ),
			    label: CHART_TYPES[key] }
			);
		}
	}

	const DataSrcButtonGroup = () => (
		<>
		<ButtonGroup>
		    <Button
		      label={ __( 'Upload', 'cwragc') }
		      isPrimary>{ __( 'Upload', 'cwragc') }</Button>
		    <Button
		      onClick={ validate }
		      label={ __( 'Retrieve from URL', 'cwragc') }
		      aria-disabled={ isValidating }
		      disabled={ isValidating }
		      isTertiary>{ __( 'Retrieve from URL',
		        'cwragc')}</Button>
		    { isValidating && <Spinner /> }
		    <Button
		      label={ __( 'Schedule from URL',
		          'cwragc') }
		      isTertiary>{ __( 'Schedule from URL',
		        'cwragc') }</Button>
		</ButtonGroup>
		</>
	);

	function validate() {
		setIsValidating( true );

		apiFetch( {
		    path: '/cwragc/v1/setremotedatasrc',
		    method: 'POST',
		    data: { 'url': cwragcDataSource,
		    	    'type': 'remote-csv',
			    'postId': post_id } } 
		).then( ( localFileName ) => {
			setAttributes( { cwragcLocalFile: localFileName } );
		}).catch( ( error ) => {
			createErrorNotice(
				sprintf(
				    __( 'Could not validate data source. %s',
					'cwragc' ), error.message),
				{
					id: 'cwragc-validate-error',
					type: 'snackbar'
				}
			);
		}).finally( () => {
			setIsValidating( false );
		});
	}

	/*
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
				      label={ __( 'Data URL', 'cwragc' ) }
				      help={ __( 'Enter the URL from which to get '
					+ 'the data.', 'cwragc') }
				      value={ cwragcDataSource }
				      onChange={ (newDataSource) => {
					setAttributes( {
					  cwragcDataSource: newDataSource } )
				      }} />
				    <Button
				      onClick={ validate }
				      label={ __( 'Retrieve', 'cwragc') }
				      aria-disabled={ isValidating }
				      disabled={ isValidating }
				      isPrimary>Retrieve</Button>
				    { isValidating && <Spinner /> }
				</div>
			</div>
		    </>
		);
	}
	*/

	const ChartRender = () => {
		return(
		    <Disabled>
		        <ServerSideRender
		            block={ props.name }
			    attributes={{ ...attributes }} />
		    </Disabled>
		);
	}

	const ChartPlaceholder = () => {
	    return(
		<Placeholder
		  icon='chart-bar'
		  label="Google Chart"
		  instructions={ __( 'Upload a data file, get one from a URL, '
		      + 'or schedule retrieval from a URL.', 'cwragc') }>
		    { <DataSrcButtonGroup /> }
		</Placeholder>
	    );
	};

	const Component = cwragcLocalFile
		? ChartRender
		: ChartPlaceholder;

	return <Component />;
}
