/* global wp */

import React from "react";

const { registerPlugin } = wp.plugins;

import Constants from "../Gutenberg/constants";
import Sidebar from "./components/Sidebar";
import WordLiftIcon from "../Gutenberg/svg/wl-logo-big.svg";

import "../Gutenberg/index.scss";

/**
 * Register the sidebar plugin
 * by rendering WordLiftSidebar component
 */
registerPlugin(Constants.PLUGIN_NAMESPACE, {
  render: () => <Sidebar />,
  icon: <WordLiftIcon />
});
