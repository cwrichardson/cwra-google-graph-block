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
    attributes: {
        content: {
            source: 'html',
            selector: 'h2',
        },
        backgroundColor: {
            type: 'string',
            default: '#900900',
        },
        textColor: {
            type: 'string',
            default: '#ffffff',
        }
    },
 
    edit: props => {
        const {
	    attributes: { backgroundColor, textColor, content },
	    setAttributes,
	    className
	} = props;

        return (
            <Fragment> 
                <InspectorControls> 
                    <PanelColorSettings
                        title={ __("Color Settings", "jsforwp") }
                        colorSettings={[
                            {
                                label: __("Background Color", "jsforwp"),
                                value: backgroundColor,
                                onChange: ( newBackgroundColor ) => {
                                    setAttributes({ backgroundColor: newBackgroundColor });
                                }
                            },
                            {
                                label: __("Text Color", "jsforwp"),
                                value: textColor,
                                onChange: ( newColor ) => {
                                    setAttributes({ textColor: newColor });
                                }
                            }
                        ]}
		    />
		</InspectorControls>
                <RichText
                    tagName="h2"
                    className={ className }
                    value={ content }
                    style={ {
                        backgroundColor: backgroundColor,
                        color: textColor
                    } }
                    onChange={ ( newContent ) => {
                        setAttributes( { content: newContent } );
                    } }
		/>
	    </Fragment>
        );
    },
 
    save: props => {
    	const {
	    attributes: { backgroundColor, textColor, content },
	    className
	} = props;
        return (
	    <RichText.Content
            	tagName="h2" 
		className={ className }
            	value={ content }
            	style={ {
                    backgroundColor: backgroundColor,
                    color: textColor
                } }        
	    />
        );
    }
} );

