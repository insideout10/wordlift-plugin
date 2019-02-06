/* globals wp */
/*
 * External dependencies.
 */
import React from "react";
import { Provider } from "react-redux";

/*
 * Internal dependencies.
 */
import store from "./store";
import WordLiftIcon from "../../../../src/images/svg/wl-logo-icon.svg";
import * as Constants from "./constants";
import ContentClassificationContainer from "./components/ContentClassificationPanel";

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;

window.store1 = store;

// TODO: Move these to components folder
const PanelArticleMetadata = () => (
  <Panel>
    <PanelBody title="Article metadata" initialOpen={false}>
      <PanelRow>Article metadata Inputs and Labels</PanelRow>
    </PanelBody>
  </Panel>
);

const PanelSuggestedImages = () => (
  <Panel>
    <PanelBody title="Suggested images" initialOpen={false}>
      <PanelRow>Suggested images Inputs and Labels</PanelRow>
    </PanelBody>
  </Panel>
);

const PanelRelatedPosts = () => (
  <Panel>
    <PanelBody title="Related posts" initialOpen={false}>
      <PanelRow>Related posts Inputs and Labels</PanelRow>
    </PanelBody>
  </Panel>
);

const WordLiftSidebar = () => (
  <Fragment>
    <PluginSidebarMoreMenuItem target="wordlift-sidebar" icon={<WordLiftIcon />}>
      WordLift
    </PluginSidebarMoreMenuItem>
    <PluginSidebar name="wordlift-sidebar" title="WordLift">
      <Provider store={store}>
        <Fragment>
          <ContentClassificationContainer />
          <PanelArticleMetadata />
          <PanelSuggestedImages />
          <PanelRelatedPosts />
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
