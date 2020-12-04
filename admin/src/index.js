/**
 * WordPress dependencies
 */
import { __, _x } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';

const { name } = metadata;

const settings = {
    title: __('Google Graph Block', 'cwra-google-graph-block'),
    description: __('Display a graph using the Google Graph API',
      'cwra-google-graph-block'),
    keywords: [ _x('graph', 'block keywords', 'cwra-google-graph-block'),
      _x('chart', 'block keywords', 'cwra-google-graph-block') ],
    icon: 'chart-bar',
    category: 'widgets',
    attributes: {
    	cwraggChartType: {
		type: 'string',
		default: 'line'
	},
    	cwraggDataSource: {
		type: 'string',
		default: 'remote-csv'
	},
    	cwraggDataSourceType: {
		type: 'string'
	},
    	cwraggLocalFile: {
		type: 'string'
	},
    	cwraggTitle: {
		type: 'string'
	}
    },
    example: {
        attributes: {
	    cwragg_datasource: 'Nothing to see here'
	}
    },
    edit,
}

registerBlockType( name, settings );
