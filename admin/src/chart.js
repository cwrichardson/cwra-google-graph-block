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
		    cwragcBackgroundColor: this.cwragcBackgroundColor =
		        DEFAULT_BACKGROUND_COLOR,
		    cwragcChartArea: this.cwragcChartArea = DEFAULT_CHART_AREA,
		    /* XXX cwragcColors: this.cwragcColors = DEFAULT_COLORS, */
		    cwragcEnableInteractivity: this.cwragcEnableInteractivity = 
		        DEFAULT_ENABLE_INTERACTIVITY,
		    cwragcFontName: this.cwragcFontName = DEFAULT_FONT_NAME,
		    cwragcFontSize: this.cwragcFontSize = '',
		    cwragcTitle: this.cwragcTitle = '',
		    cwragcForcelFrame: this.cwragcForcelFrame = 
		        DEFAULT_FORCEL_FRAME,
		    cwragcHeight: this.cwragcHeight = DEFAULT_HEIGHT,
		    cwragcLegend: this.cwragcLegend = DEFAULT_LEGEND,
		    cwragcTheme: this.cwragcTheme = '',
		    /* XXX cwragcTooltip: this.cwragcTooltip = DEFAULT_TOOLTIP */
		    cwragcWidth: this.cwragcWidth = DEFAULT_WIDTH
		} = args.attributes );

		({ setAttributes: this.setAttributes } = args );

		this.sidebarElements = [];

	}

	get height() {
		return this.cwragcHeight;
	}

	set height(newHeight) {
		this.setAttributes( { cwragcHeight: newHeight } );
	}

	get title() {
		return this.cwragcTitle;
	}

	set title(newTitle) {
		this.setAttributes( { cwragcTitle: newTitle } );
	}

	get width() {
		return this.cwragcWidth;
	}

	set width(newWidth) {
		this.setAttributes( { cwragcWidth: newWidth } );
	}

	addMain() {
		// don't add it if it's already there
		if (this.sidebarElements.length !== 0) return;

		this.sidebarElements.push((
			<PanelBody
			  key='cwragcpicmain'
			  title={ __( 'Main Settings', 'cwragc' ) }>
			    <PanelRow>
			        <TextControl
				  label={ __( 'Title', 'cwragc' ) }
				  value={ this.title }
				  onChange={ (value) => this.title = value } />
			    </PanelRow>
			    <PanelRow>
			        <TextControl
				  label={ __( 'Height', 'cwragc' ) }
				  value={ this.height }
				  onChange={ (value) => this.height = value } />
			        <TextControl
				  label={ __( 'Width', 'cwragc' ) }
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
			  header={ __( 'Google Chart Settings', 'cwragc' )}>
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
