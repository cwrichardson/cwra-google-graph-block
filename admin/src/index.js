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
    title: __('Google Chart Block', 'cwragc'),
    description: __('Display a chart using the Google Chart API',
      'cwragc'),
    keywords: [ _x('graph', 'block keywords', 'cwragc'),
      _x('Google chart', 'block keywords', 'cwragc'),
      _x('Google graph', 'block keywords', 'cwragc'),
      _x('chart', 'block keywords', 'cwragc') ],
    icon: 'chart-bar',
    category: 'widgets',
    attributes: {
    	cwragcBaseId: {
		type: 'string',
	},
    	cwragcChartType: {
		type: 'string',
		default: 'line'
	},
    	cwragcDataSource: {
		type: 'string',
		default: 'remote-csv'
	},
    	cwragcDataSourceType: {
		type: 'string'
	},
    	cwragcHeight: {
		type: 'number'
	},
    	cwragcLocalFile: {
		type: 'string'
	},
    	cwragcTitle: {
		type: 'string'
	},
    	cwragcWidth: {
		type: 'number'
	}
    },
    example: {
        attributes: {
	    cwragc_datasource: 'Nothing to see here'
	}
    },
    edit,
}

registerBlockType( name, settings );
