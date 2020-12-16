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
    title: __('Google Graph Block', 'cwraggb'),
    description: __('Display a graph using the Google Graph API',
      'cwraggb'),
    keywords: [ _x('graph', 'block keywords', 'cwraggb'),
      _x('chart', 'block keywords', 'cwraggb') ],
    icon: 'chart-bar',
    category: 'widgets',
    attributes: {
    	cwraggBaseId: {
		type: 'string',
	},
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
    	cwraggHeight: {
		type: 'number'
	},
    	cwraggLocalFile: {
		type: 'string'
	},
    	cwraggTitle: {
		type: 'string'
	},
    	cwraggWidth: {
		type: 'number'
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
