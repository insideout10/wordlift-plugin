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

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;

window.store1 = Store1;

// TODO: Move these to components folder
const PanelArticleMetadata = () => (
  <Panel>
    <PanelBody title="Article metadata" initialOpen={false}>
      <PanelRow>Article metadata Inputs and Labels</PanelRow>
    </PanelBody>
  </Panel>
);

const WordLiftSidebar = () => (
  <Fragment>
    <PluginSidebarMoreMenuItem target="wordlift-sidebar" icon={<WordLiftIcon />}>
      WordLift
    </PluginSidebarMoreMenuItem>
    <PluginSidebar name="wordlift-sidebar" title="WordLift">
      <Provider store={Store1}>
        <Fragment>
          <ContentClassificationPanel />
          <PanelArticleMetadata />
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
