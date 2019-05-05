/* globals wp */
/*
 * External dependencies.
 */
import React from "react";
import { Provider } from "react-redux";

/*
 * Internal dependencies.
 */
import Store1 from "./stores/Store1";
import WordLiftIcon from "./svg/wl-logo-big.svg";
import * as Constants from "./constants";
import ContentClassificationPanel from "./components/ContentClassificationPanel";
import RelatedPostsPanel from "./components/RelatedPostsPanel";
import SuggestedImagesPanel from "./components/SuggestedImagesPanel";
import ArticleMetadataPanel from "./components/ArticleMetadataPanel";
import AddEntityPanel from "./components/AddEntityPanel";
import "./index.scss";

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor; //Block inspector wrapper
const { PanelBody, TextControl, CheckboxControl, ServerSideRender } = wp.components; //Block inspector wrapper

const WordLiftSidebar = () => (
  <Fragment>
    <PluginSidebarMoreMenuItem target="wordlift-sidebar" icon={<WordLiftIcon />}>
      WordLift
    </PluginSidebarMoreMenuItem>
    <PluginSidebar name="wordlift-sidebar" title="WordLift">
      <Provider store={Store1}>
        <Fragment>
          <AddEntityPanel />
          <ContentClassificationPanel />
          <ArticleMetadataPanel />
          <SuggestedImagesPanel />
          <RelatedPostsPanel />
        </Fragment>
      </Provider>
    </PluginSidebar>
  </Fragment>
);

/**
 * Register the sidebar plugin
 * by rendering WordLiftSidebar component
 */
wp.plugins.registerPlugin(Constants.PLUGIN_NAMESPACE, {
  render: WordLiftSidebar,
  icon: <WordLiftIcon />
});

registerBlockType(`${Constants.PLUGIN_NAMESPACE}/faceted-search`, {
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
    // Simplify access to attributes
    const { title, show_facets, with_carousel, squared_thumbs, limit } = attributes;
    const onChangeTitle = newTitle => {
      setAttributes({ content: newTitle });
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
        <ServerSideRender block={`${Constants.PLUGIN_NAMESPACE}/faceted-search`} attributes={attributes} />
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
});
