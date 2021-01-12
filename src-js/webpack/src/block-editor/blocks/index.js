/**
 * External dependencies.
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { Fragment } from "@wordpress/element";
import { InspectorControls } from "@wordpress/editor";
import {
  PanelBody,
  TextControl,
  TextareaControl,
  CheckboxControl,
  RangeControl,
  ColorPicker,
  RadioControl,
  SelectControl
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
import ChordIcon from "./icons/chord.svg";
import EntitiesCloudIcon from "./icons/entities-cloud.svg";
import FacetedSearchIcon from "./icons/faceted-search.svg";
import GeomapIcon from "./icons/geomap.svg";
import NavigatorIcon from "./icons/navigator.svg";
import ProductsNavigatorIcon from "./icons/products-navigator.svg";
import TimelineIcon from "./icons/timeline.svg";
import VocabularyIcon from "./icons/vocabulary.svg";
import { PLUGIN_NAMESPACE } from "../../common/constants";
import "./blocks.scss";

const wlSettings = global["wlSettings"];

const humanize = str => {
  return str
    .replace(/^[\s_]+|[\s_]+$/g, "")
    .replace(/[_\s]+/g, " ")
    .replace(/^[a-z]/, function(m) {
      return m.toUpperCase();
    });
};

const BlockPreview = ({ title, attributes, icon }) => (
  <Fragment>
    <h4>{title}</h4>
    {attributes &&
      Object.keys(attributes).map(
        key =>
          !["preview", "preview_src"].includes(key) && (
            <div style={{ fontSize: "0.8rem" }}>
              <span style={{ width: "140px", display: "inline-block", fontWeight: "bold" }}>{humanize(key)}</span>{" "}
              {typeof attributes[key] === "boolean" ? JSON.stringify(attributes[key]) : attributes[key]}
            </div>
          )
      )}
  </Fragment>
);

const blocks = {
  [`${PLUGIN_NAMESPACE}/faceted-search`]: {
    title: __("Faceted Search", "wordlift"),
    description: __(
      "Using the Faceted Search Widget readers, selecting concepts they are interested in, can find all related articles.",
      "wordlift"
    ),
    category: "wordlift",
    icon: <FacetedSearchIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { title, template_id, post_id, uniqid, limit, preview, preview_src, post_types } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Faceted Search", "wordlift")} attributes={attributes} />
          <InspectorControls>
            <PanelBody title={__("Widget Settings", "wordlift")} className="blocks-font-size">
              <TextControl label={__("Title", "wordlift")} value={title} onChange={title => setAttributes({ title })} />
              <RangeControl
                label={__("Limit", "wordlift")}
                value={limit}
                min={2}
                max={20}
                onChange={limit => setAttributes({ limit })}
              />
              <TextControl
                label={__("Template ID", "wordlift")}
                help={__(
                  "ID of the script tag that has mustache template to be used for Faceted Search widget.",
                  "wordlift"
                )}
                value={template_id}
                onChange={template_id => setAttributes({ template_id })}
              />
              <TextControl
                label={__("Post ID", "wordlift")}
                help={__("Post ID of the post of which Faceted Search widget has to be shown.", "wordlift")}
                type="number"
                value={post_id}
                onChange={post_id => setAttributes({ post_id })}
              />
              <TextControl
                label={__("Unique ID", "wordlift")}
                value={uniqid}
                onChange={uniqid => setAttributes({ uniqid })}
              />
              <TextControl
                  label={__("Post types", "wordlift")}
                  help={__("The Post types which should be shown on faceted search results, For example  post,page", "wordlift")}
                  value={post_types}
                  onChange={post_types => setAttributes({ post_types })}
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
  [`${PLUGIN_NAMESPACE}/navigator`]: {
    title: __("Navigator", "wordlift"),
    description: __(
      "The Navigator Widget provides content recommendations by presenting relevant links to other blog posts on your website.",
      "wordlift"
    ),
    category: "wordlift",
    icon: <NavigatorIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { title, limit, template_id, post_id, offset, uniqid, order_by, preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Navigator", "wordlift")} attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={title => setAttributes({ title })} />
              <RangeControl label="Limit" value={limit} min={2} max={20} onChange={limit => setAttributes({ limit })} />
              <RangeControl
                label="Offset"
                value={offset}
                min={0}
                max={20}
                onChange={offset => setAttributes({ offset })}
              />
              <TextControl
                label="Template ID"
                help="ID of the script tag that has mustache template to be used for navigator."
                value={template_id}
                onChange={template_id => setAttributes({ template_id })}
              />
              <TextControl
                label="Post ID"
                help="Post ID of the post of which navigator has to be shown."
                type="number"
                value={post_id}
                onChange={post_id => setAttributes({ post_id })}
              />
              <TextControl label="Unique ID" value={uniqid} onChange={uniqid => setAttributes({ uniqid })} />
              <TextControl
                label="Order by"
                help="Valid SQL ‘order by’ clause"
                value={order_by}
                onChange={order_by => setAttributes({ order_by })}
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
  [`${PLUGIN_NAMESPACE}/products-navigator`]: {
    title: __("Products Navigator", "wordlift"),
    description: __(
      "The Products' Navigator Widget provides product recommendations by presenting relevant links to other products on your website.",
      "wordlift"
    ),
    category: "wordlift",
    icon: <ProductsNavigatorIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { title, limit, template_id, post_id, offset, uniqid, order_by, preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Products Navigator", "wordlift")} attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <TextControl label="Title" value={title} onChange={title => setAttributes({ title })} />
              <RangeControl label="Limit" value={limit} min={2} max={20} onChange={limit => setAttributes({ limit })} />
              <RangeControl
                label="Offset"
                value={offset}
                min={0}
                max={20}
                onChange={offset => setAttributes({ offset })}
              />
              <TextControl
                label="Template ID"
                help="ID of the script tag that has mustache template to be used for navigator."
                value={template_id}
                onChange={template_id => setAttributes({ template_id })}
              />
              <TextControl
                label="Post ID"
                help="Post ID of the post of which navigator has to be shown."
                type="number"
                value={post_id}
                onChange={post_id => setAttributes({ post_id })}
              />
              <TextControl label="Unique ID" value={uniqid} onChange={uniqid => setAttributes({ uniqid })} />
              <TextControl
                label="Order by"
                help="Valid SQL ‘order by’ clause"
                value={order_by}
                onChange={order_by => setAttributes({ order_by })}
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
  [`${PLUGIN_NAMESPACE}/chord`]: {
    title: __("Chord", "wordlift"),
    description: __("The Chord Widget visualizes the relations between entities within a given article.", "wordlift"),
    category: "wordlift",
    icon: <ChordIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { width, height, main_color, depth, global, preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Chord", "wordlift")} attributes={attributes} />
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
  [`${PLUGIN_NAMESPACE}/geomap`]: {
    title: __("Geomap", "wordlift"),
    description: __("This Widget displays entities of type “Place” mentioned in the article on a Geomap.", "wordlift"),
    category: "wordlift",
    icon: <GeomapIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { width, height, global, preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Geomap", "wordlift")} attributes={attributes} />
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
  [`${PLUGIN_NAMESPACE}/cloud`]: {
    title: __("Entities Cloud", "wordlift"),
    description: __(
      "The Entity Cloud Widget is a site-wide Widget and a shortcode that displays entities related to the current post/entity in a tag cloud.",
      "wordlift"
    ),
    category: "wordlift",
    icon: <EntitiesCloudIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Entities Cloud", "wordlift")} />
          <InspectorControls />
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${PLUGIN_NAMESPACE}/vocabulary`]: {
    title: __("Vocabulary", "wordlift"),
    description: __(
      "The Vocabulary is a site-wide Widget that displays all the entities in alphabetical order.",
      "wordlift"
    ),
    category: "wordlift",
    icon: <VocabularyIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { type, limit, orderby, order, cat, preview, preview_src } = attributes;
      const typeOptions = [{ value: "all", label: "All" }];
      const orderbyOptions = [
        { value: "post_date", label: "Date" },
        { value: "ID", label: "Post ID" },
        { value: "author", label: "Author" },
        { value: "title", label: "Title" },
        { value: "name", label: "Name (post slug)" },
        { value: "type", label: "Post type" },
        { value: "date", label: "Date" },
        { value: "modified", label: "Last modified date" },
        { value: "parent", label: "Post/page parent ID" },
        { value: "comment_count", label: "Number of comments" },
        { value: "menu_order", label: "Page Order" },
        { value: "rand", label: "Random order" },
        { value: "none", label: "None" }
      ];
      const orderOptions = [{ value: "ASC", label: "Ascending" }, { value: "DESC", label: "Descending" }];
      window["_wlEntityTypes"].forEach(item => {
        typeOptions.push({
          value: item.slug,
          label: item.label
        });
      });
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Vocabulary", "wordlift")} attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <SelectControl
                label="Type"
                value={type}
                onChange={type => setAttributes({ type })}
                options={typeOptions}
              />
              <SelectControl
                label="Order by"
                value={orderby}
                onChange={orderby => setAttributes({ orderby })}
                options={orderbyOptions}
              />
              <RadioControl
                label="Order"
                selected={order}
                options={orderOptions}
                onChange={order => setAttributes({ order })}
              />
              <RangeControl
                label="Limit"
                value={limit}
                min={-1}
                max={200}
                onChange={limit => setAttributes({ limit })}
              />
              <TextControl label="Category ID" value={cat} onChange={cat => setAttributes({ cat })} />
            </PanelBody>
          </InspectorControls>
        </div>
      );
    },
    save() {
      return null; //save has to exist. This all we need
    }
  },
  [`${PLUGIN_NAMESPACE}/timeline`]: {
    title: __("Timeline", "wordlift"),
    description: __("WordLift uses the powerful TimelineJS to create beautiful, interactive timelines.", "wordlift"),
    category: "wordlift",
    icon: <TimelineIcon />,
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({ attributes, setAttributes }) => {
      const { display_images_as, excerpt_length, global, timelinejs_options, preview, preview_src } = attributes;
      if (preview) {
        return (
          <Fragment>
            <img src={preview_src} />
          </Fragment>
        );
      }
      return (
        <div>
          <BlockPreview title={__("WordLift Timeline", "wordlift")} attributes={attributes} />
          <InspectorControls>
            <PanelBody title="Widget Settings" className="blocks-font-size">
              <RadioControl
                label="Display images as"
                selected={display_images_as}
                onChange={display_images_as => setAttributes({ display_images_as })}
                options={[{ value: "media", label: "Media" }, { value: "background", label: "Background" }]}
              />
              <RangeControl
                label="Excerpt length"
                value={excerpt_length}
                min={10}
                max={200}
                onChange={excerpt_length => setAttributes({ excerpt_length })}
              />
              <CheckboxControl label="Global" checked={global} onChange={global => setAttributes({ global })} />
              <TextareaControl
                label="Timelinejs options"
                help="Enter options as JSON string"
                value={timelinejs_options}
                rows={8}
                onChange={timelinejs_options => setAttributes({ timelinejs_options })}
              />
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

/**
 * Register all blocks (widgets)
 */
for (let block in blocks) {
  if (window.wlEnabledBlocks && window.wlEnabledBlocks.includes(block)) {
    registerBlockType(block, blocks[block]);
  }
}
