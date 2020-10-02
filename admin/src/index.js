import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { Fragment } from "@wordpress/element";
import {
  InspectorControls,
  PanelColorSettings,
  RichText
} from "@wordpress/editor";

registerBlockType( 'cwra-google-graph-block/graph-block', {
    title: __('Google Graph Block', "cwra-google-graph-block"),
    description: __('Display a graph using the Google Graph API',
      "cwra-google-graph-block"),
    icon: 'chart-bar',
    category: 'widgets',
    keywords: [ __('Google Graph', "cwra-google-graph-block"),
      __('graph', "cwra-google-graph-block") ],
    example: {},

    edit() {
	return 'Nothing to see here';
    },
} );

