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

/*
 * Packages via WordPress global
 */
const { InspectorControls } = wp.editor;
const { PanelBody, TextControl, CheckboxControl } = wp.components;

export default {
  [`${Constants.PLUGIN_NAMESPACE}/faceted-search`]: {
    title: "Faceted Search",
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
    edit: ({ attributes, setAttributes, className, isSelected }) => {
      const { title, show_facets, with_carousel, squared_thumbs, limit } = attributes;
      const onChangeTitle = newTitle => {
        setAttributes({ title: newTitle });
      };
      const onChangeLimit = newLimit => {
        setAttributes({ limit: newLimit });
      };
      const onChangeShowFacets = newShowFacets => {
        setAttributes({ show_facets: newShowFacets });
      };
      const onChangeWithCarousel = newWithCarousel => {
        setAttributes({ with_carousel: newWithCarousel });
      };
      const onChangeSquaredThumbs = newSquaredThumbs => {
        setAttributes({ squared_thumbs: newSquaredThumbs });
      };
      return (
        <div>
          <React.Fragment>
            <h4>Wordlift Faceted Search block</h4>
            <hr />
            <pre style={{ fontSize: "10px" }}>{JSON.stringify(attributes, null, 2)}</pre>
          </React.Fragment>
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={onChangeTitle} />
              <TextControl label="Limit" value={limit} type="number" onChange={onChangeLimit} />
              <CheckboxControl label="Show Facets" checked={show_facets} onChange={onChangeShowFacets} />
              <CheckboxControl label="With Carousel" checked={with_carousel} onChange={onChangeWithCarousel} />
              <CheckboxControl label="Squared Thumbnails" checked={squared_thumbs} onChange={onChangeSquaredThumbs} />
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
    title: "Navigator",
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
    edit: ({ attributes, setAttributes, className, isSelected }) => {
      const { title, with_carousel, squared_thumbs } = attributes;
      const onChangeTitle = newTitle => {
        setAttributes({ title: newTitle });
      };
      const onChangeWithCarousel = newWithCarousel => {
        setAttributes({ with_carousel: newWithCarousel });
      };
      const onChangeSquaredThumbs = newSquaredThumbs => {
        setAttributes({ squared_thumbs: newSquaredThumbs });
      };
      return (
        <div>
          <React.Fragment>
            <h4>Wordlift Navigator block</h4>
            <hr />
            <pre style={{ fontSize: "10px" }}>{JSON.stringify(attributes, null, 2)}</pre>
          </React.Fragment>
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={onChangeTitle} />
              <CheckboxControl label="With Carousel" checked={with_carousel} onChange={onChangeWithCarousel} />
              <CheckboxControl label="Squared Thumbnails" checked={squared_thumbs} onChange={onChangeSquaredThumbs} />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  }
};
