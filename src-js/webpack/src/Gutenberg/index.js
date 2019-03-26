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

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;

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
