/**
 * This file is the entry point for G'berg.
 *
 * This file registers the {@link Sidebar} component along with {@link WordLiftIcon}.
 */

/**
 * External dependencies
 */
import React from "react";
import { Provider } from "react-redux";
import { on } from "backbone";

/**
 * WordPress dependencies
 */
import { PluginSidebar, PluginSidebarMoreMenuItem } from "@wordpress/edit-post";
import { Fragment } from "@wordpress/element";
import { registerPlugin } from "@wordpress/plugins";
import { dispatch } from "@wordpress/data";
import { createBlock } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
import Sidebar from "./containers/sidebar";
import withDidMountCallback from "../common/components/with-did-mount-callback";
import { requestAnalysis } from "./stores/actions";
import store from "./stores";
import WordLiftIcon from "./wl-logo-big.svg";
import "./index.scss";
import "./formats/register-format-type-wordlift-annotation";
import "./blocks/register-block-type-wordlift-classification";
import { ANNOTATION_CHANGED } from "../common/constants";
import { setCurrentAnnotation } from "../Edit/actions";
import { getClassificationBlock } from "./stores/selectors";
import { EDITOR_STORE, PLUGIN_NAMESPACE } from "../common/constants";
import registerFilters from "./filters/add-entity.filters";
import ArticleMetadataPanel from "../common/components/article-metadata-panel";
import SuggestedImagesPanel from "../common/components/suggested-images-panel";
import FaqPanel from "../common/components/faq-panel";
import SynonymsPanel from "../common/components/synonyms-panel";
import RelatedPostsPanel from "../common/containers/related-posts";
import "./blocks";

const wlSettings = global["wlSettings"];
const canAddSynonyms = wlSettings && wlSettings["can_add_synonyms"] ? wlSettings["can_add_synonyms"] == 1 : false;

// Register our filters to display additional elements in the CreateEntityForm. Pass our store to connect them to
// our state.
registerFilters(store);

/**
 * Hook WordPress' action `ANNOTATION_CHANGED` to dispatching the annotation
 * to the store.
 */
on(ANNOTATION_CHANGED, payload => store.dispatch(setCurrentAnnotation(payload)));

/**
 * Connect the Sidebar to the analysis to be run as soon as the component is
 * mounted.
 */
const SidebarWithDidMountCallback = withDidMountCallback(Sidebar, () => {
  // Request the analysis.
  store.dispatch(requestAnalysis());

  // Add the WordLift Classification block is not yet available.
  if ("undefined" === typeof getClassificationBlock())
    dispatch(EDITOR_STORE).insertBlock(createBlock("wordlift/classification", {}));
});

/**
 * Register the sidebar plugin.
 *
 * We assign the `wl-sidebar` class name to the {@link PluginSidebar} to allow
 * custom styling (specifically to increase the top padding, see index.scss).
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/plugin-sidebar-0/plugin-sidebar-1-up-and-running/
 */
registerPlugin(PLUGIN_NAMESPACE, {
  render: () => (
    <Fragment>
      <PluginSidebarMoreMenuItem target="wordlift-sidebar" icon={<WordLiftIcon />}>
        WordLift
      </PluginSidebarMoreMenuItem>
      <PluginSidebar name="wordlift-sidebar" title="WordLift" className="wl-sidebar">
        <Provider store={store}>
          <Fragment>
            <SidebarWithDidMountCallback />
            {canAddSynonyms && <SynonymsPanel />}
            <ArticleMetadataPanel />
            <SuggestedImagesPanel />
            <RelatedPostsPanel />
            {/*<FaqPanel />*/}
          </Fragment>
        </Provider>
      </PluginSidebar>
    </Fragment>
  ),
  icon: <WordLiftIcon />
});
