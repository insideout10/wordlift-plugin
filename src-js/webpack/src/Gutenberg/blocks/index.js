/* globals wp */
/*
 * External dependencies.
 */
import React from "react";

/*
 * Internal dependencies.
 */
import WordLiftIcon from "../svg/wl-logo-big.svg";
import * as Constants from "../constants";
import { humanize } from "../helpers";

/*
 * Packages via WordPress global
 */
const { InspectorControls } = wp.editor;
const { PanelBody, TextControl, CheckboxControl, RangeControl, ColorPicker, ServerSideRender } = wp.components;

const BlockPreview = ({ title, attributes }) => (
  <React.Fragment>
    <h4>{title}</h4>
    {attributes &&
      Object.keys(attributes).map(key => (
        <div style={{ fontSize: "0.8rem" }}>
          <span style={{ width: "140px", display: "inline-block", fontWeight: "bold" }}>{humanize(key)}</span>{" "}
          {typeof attributes[key] === "boolean" ? JSON.stringify(attributes[key]) : attributes[key]}
        </div>
      ))}
  </React.Fragment>
);

export default {
  [`${Constants.PLUGIN_NAMESPACE}/faceted-search`]: {
    title: "Wordlift Faceted Search",
    description: "Configure Faceted Search block within your content.",
    category: "wordlift",
    icon: <WordLiftIcon />,
    attributes: {
      title: {
        default: "Related articles"
      },
      show_facets: {
        default: true
      },
      with_carousel: {
        default: true
      },
      squared_thumbs: {
        default: false
      },
      limit: {
        default: 20
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { title, show_facets, with_carousel, squared_thumbs, limit } = attributes;
      return (
        <div>
          <BlockPreview title="Wordlift Faceted Search" attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={title => setAttributes({ title })} />
              <RangeControl
                label="Limit"
                value={limit}
                min={2}
                max={100}
                onChange={limit => setAttributes({ limit })}
              />
              <CheckboxControl
                label="Show Facets"
                checked={show_facets}
                onChange={show_facets => setAttributes({ show_facets })}
              />
              <CheckboxControl
                label="With Carousel"
                checked={with_carousel}
                onChange={with_carousel => setAttributes({ with_carousel })}
              />
              <CheckboxControl
                label="Squared Thumbnails"
                checked={squared_thumbs}
                onChange={squared_thumbs => setAttributes({ squared_thumbs })}
              />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${Constants.PLUGIN_NAMESPACE}/navigator`]: {
    title: "Wordlift Navigator",
    description: "Configure Navigator block within your content.",
    category: "wordlift",
    icon: <WordLiftIcon />,
    attributes: {
      title: {
        default: "Related articles"
      },
      with_carousel: {
        default: true
      },
      squared_thumbs: {
        default: false
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { title, with_carousel, squared_thumbs } = attributes;
      return (
        <div>
          <BlockPreview title="Wordlift Navigator" attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={title => setAttributes({ title })} />
              <CheckboxControl
                label="With Carousel"
                checked={with_carousel}
                onChange={with_carousel => setAttributes({ with_carousel })}
              />
              <CheckboxControl
                label="Squared Thumbnails"
                checked={squared_thumbs}
                onChange={squared_thumbs => setAttributes({ squared_thumbs })}
              />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${Constants.PLUGIN_NAMESPACE}/chord`]: {
    title: "Wordlift Chord",
    description: "Configure Chord block within your content.",
    category: "wordlift",
    icon: <WordLiftIcon />,
    attributes: {
      width: {
        default: "100%"
      },
      height: {
        default: "500px"
      },
      main_color: {
        default: "#000"
      },
      depth: {
        default: 2
      },
      global: {
        default: false
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { width, height, main_color, depth, global } = attributes;
      return (
        <div>
          <BlockPreview title="Wordlift Chord" attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Width" value={width} onChange={width => setAttributes({ width })} />
              <TextControl label="Height" value={height} onChange={height => setAttributes({ height })} />
              <RangeControl label="Depth" value={depth} min={1} max={10} onChange={depth => setAttributes({ depth })} />
              <label className="components-base-control__label">Main color</label>
              <ColorPicker
                color={main_color}
                onChangeComplete={value => setAttributes({ main_color: value.hex })}
                disableAlpha
              />
              <CheckboxControl label="Global" checked={global} onChange={global => setAttributes({ global })} />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${Constants.PLUGIN_NAMESPACE}/geomap`]: {
    title: "Wordlift Geomap",
    description: "Configure Geomap block within your content.",
    category: "wordlift",
    icon: <WordLiftIcon />,
    attributes: {
      width: {
        default: "100%"
      },
      height: {
        default: "300px"
      },
      global: {
        default: false
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { width, height, global } = attributes;
      return (
        <div>
          <BlockPreview title="Wordlift Geomap" attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Width" value={width} onChange={width => setAttributes({ width })} />
              <TextControl label="Height" value={height} onChange={height => setAttributes({ height })} />
              <CheckboxControl label="Global" checked={global} onChange={global => setAttributes({ global })} />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${Constants.PLUGIN_NAMESPACE}/cloud`]: {
    title: "Wordlift Entities Cloud",
    description: "Entities Cloud block within your content.",
    category: "wordlift",
    icon: <WordLiftIcon />,
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      return (
        <div>
          <BlockPreview title="Wordlift Entities Cloud" />
          <InspectorControls />
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  }
};
