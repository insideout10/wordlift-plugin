/* global wp */

import React from "react";
import { Provider } from "react-redux";

const { Fragment } = wp.element;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;

import actions from "../stores/actions";
import WordLiftIcon from "../../Gutenberg/svg/wl-logo-big.svg";
import App from "../../Edit/components/App";
import VisibleEntityList from "../../Gutenberg/components/ContentClassificationPanel/VisibleEntityList";
import store from "../stores";

class PluginSidebarContent extends React.Component {
  render() {
    return (
      <Provider store={store}>
        <App />
      </Provider>
    );
  }

  componentDidMount() {
    store.dispatch(actions.requestAnalysis());
  }
}

export default class extends React.Component {
  render() {
    return (
      <Fragment>
        <PluginSidebarMoreMenuItem target="wordlift-sidebar" icon={<WordLiftIcon />}>
          WordLift
        </PluginSidebarMoreMenuItem>
        <PluginSidebar name="wordlift-sidebar" title="WordLift">
          <PluginSidebarContent />
        </PluginSidebar>
      </Fragment>
    );
  }
}
