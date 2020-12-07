/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import {
    Panel,
    PanelBody,
    PanelRow,
    TextControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	DEFAULT_BACKGROUND_COLOR,
	DEFAULT_CHART_AREA,
	DEFAULT_ENABLE_INTERACTIVITY,
	DEFAULT_FONT_NAME,
	DEFAULT_FORCEL_FRAME,
	DEFAULT_HEIGHT,
	DEFAULT_LEGEND,
	DEFAULT_TEXT_STYLE,
	DEFAULT_WIDTH
} from './constants';

class Chart {
	constructor ( args ) {

		// Assign passed parameters to block attributes by name
		// setting defaults if not passed.
		({
		    cwraggBackgroundColor: this.cwraggBackgroundColor =
		        DEFAULT_BACKGROUND_COLOR,
		    cwraggChartArea: this.cwraggChartArea = DEFAULT_CHART_AREA,
		    /* XXX cwraggColors: this.cwraggColors = DEFAULT_COLORS, */
		    cwraggEnableInteractivity: this.cwraggEnableInteractivity = 
		        DEFAULT_ENABLE_INTERACTIVITY,
		    cwraggFontName: this.cwraggFontName = DEFAULT_FONT_NAME,
		    cwraggFontSize: this.cwraggFontSize = '',
		    cwraggTitle: this.cwraggTitle = '',
		    cwraggForcelFrame: this.cwraggForcelFrame = 
		        DEFAULT_FORCEL_FRAME,
		    cwraggHeight: this.cwraggHeight = DEFAULT_HEIGHT,
		    cwraggLegend: this.cwraggLegend = DEFAULT_LEGEND,
		    cwraggTheme: this.cwraggTheme = '',
		    /* XXX cwraggTooltip: this.cwraggTooltip = DEFAULT_TOOLTIP */
		    cwraggWidth: this.cwraggWidth = DEFAULT_WIDTH
		} = args.attributes );

		({ setAttributes: this.setAttributes } = args );

		this.sidebarElements = [];

	}

	get height() {
		return this.cwraggHeight;
	}

	set height(newHeight) {
		this.setAttributes( { cwraggHeight: newHeight } );
	}

	get title() {
		return this.cwraggTitle;
	}

	set title(newTitle) {
		this.setAttributes( { cwraggTitle: newTitle } );
	}

	get width() {
		return this.cwraggWidth;
	}

	set width(newWidth) {
		this.setAttributes( { cwraggWidth: newWidth } );
	}

	addMain() {
		// don't add it if it's already there
		if (this.sidebarElements.length !== 0) return;

		this.sidebarElements.push((
			<PanelBody
			  key='cwraggbpicmain'
			  title={ __( 'Main Settings', 'cwraggb' ) }>
			    <PanelRow>
			        <TextControl
				  label={ __( 'Title', 'cwraggb' ) }
				  value={ this.title }
				  onChange={ (value) => this.title = value } />
			    </PanelRow>
			    <PanelRow>
			        <TextControl
				  label={ __( 'Height', 'cwraggb' ) }
				  value={ this.height }
				  onChange={ (value) => this.height = value } />
			        <TextControl
				  label={ __( 'Width', 'cwraggb' ) }
				  value={ this.width }
				  onChange={ (value) => this.width = value } />
			    </PanelRow>
			</PanelBody>
		));
	}

	sidebarWrap( elements = [] ) {
		return (
		  <>
		    <InspectorControls>
		        <Panel
			  header={ __( 'Google Graph Settings', 'cwraggb' )}>
			    { elements }
		        </Panel>
		    </InspectorControls>
		  </>
		);
	}

	sidebar() {
		this.addMain();
		return this.sidebarWrap( this.sidebarElements );
	}
}

class HasAxes extends Chart {

	constructor( { attributes, setAttributes } ) {
		super({ attributes, setAttributes });
	}
}

class LineLike extends HasAxes {

	constructor( { attributes, setAttributes } ) {
		super({ attributes, setAttributes });
	}
}

export class LineChart extends LineLike {

	constructor( { attributes, setAttributes } ) {
		super({ attributes, setAttributes });
	}
}

export class PieChart extends LineLike {
	constructor( { attributes, setAttributes } ) {
		super({ attributes, setAttributes });
	}
}
