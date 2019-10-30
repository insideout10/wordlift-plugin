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

/**
 * WordPress dependencies
 */
import { PluginSidebar, PluginSidebarMoreMenuItem } from "@wordpress/edit-post";
import { Fragment } from "@wordpress/element";
import { registerPlugin } from "@wordpress/plugins";
import { addAction } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import { PLUGIN_NAMESPACE } from "../Gutenberg/constants";
import Sidebar from "../Edit/components/App";
import withDidMountCallback from "./components/with-did-mount-callback";
import actions from "./stores/actions";
import store from "./stores";

import WordLiftIcon from "../Gutenberg/svg/wl-logo-big.svg";
import "../Gutenberg/index.scss";
import "./format-type-annotation";
import { ANNOTATION_CHANGED } from "../common/constants";
import { setCurrentAnnotation } from "../Edit/actions";

/**
 * Hook WordPress' action `ANNOTATION_CHANGED` to dispatching the annotation
 * to the store.
 */
addAction(ANNOTATION_CHANGED, "wordlift", payload => store.dispatch(setCurrentAnnotation(payload)));

/**
 * Connect the Sidebar to the analysis to be run as soon as the component is
 * mounted.
 */
const SidebarWithDidMountCallback = withDidMountCallback(Sidebar, () => {
  // Request the analysis.
  store.dispatch(actions.requestAnalysis());
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
          <SidebarWithDidMountCallback />
        </Provider>
      </PluginSidebar>
    </Fragment>
  ),
  icon: <WordLiftIcon />
});
