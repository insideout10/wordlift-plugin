/*
 * External dependencies.
 */
import React from "react";

/*
 * Internal dependencies.
 */
import store from "./store";
import WordLiftIcon from "../../../../src/images/svg/wl-logo-icon.svg";
import { ClassificationBox, ReceiveAnalysisResultsEvent, AnnotateSelected } from "./index.classification-box";

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { registerPlugin } = wp.plugins;

const { __ } = wp.i18n;
const { registerFormatType } = wp.richText;

window.store1 = store;

const PLUGIN_NAMESPACE = "wordlift";
const PLUGIN_FORMAT_NAMESPACE = "wordlift/select-text";

class PanelContentClassification extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      entities: null
    };

    let JSONData = {
      contentLanguage: "en",
      contentType: "text/html",
      scope: "all",
      version: "1.0.0"
    };

    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        let currentBlock = wp.data.select("core/editor").getBlocks()[blockIndex];
        if (block.attributes && block.attributes.content) {
          JSONData.content = block.attributes.content;
          console.log(`Requesting analysis for block ${currentBlock.clientId}...`);
          store.dispatch(ReceiveAnalysisResultsEvent(JSONData, currentBlock.clientId));
        } else {
          console.log(`No content found in block ${currentBlock.clientId}`);
        }
      });

    registerFormatType(PLUGIN_FORMAT_NAMESPACE, {
      name: PLUGIN_FORMAT_NAMESPACE,
      title: PLUGIN_NAMESPACE,
      tagName: "span",
      className: null,
      edit({ isActive, value, onChange }) {
        AnnotateSelected(value.start, value.end);
        return <Fragment />;
      }
    });
  }

  componentDidMount() {
    this.unsubscribe = store.subscribe(() => {
      this.setState({
        entities: store.getState().entities
      });
    });
  }

  componentWillUnmount() {
    this.unsubscribe();
  }

  render() {
    return (
      <Panel>
        <PanelBody title="Content classification" initialOpen={true}>
          <PanelRow>
            {this.state.entities && this.state.entities.size > 0 ? <ClassificationBox /> : "Analyzing content..."}
          </PanelRow>
        </PanelBody>
      </Panel>
    );
  }
}

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
      <PanelContentClassification />
      <PanelArticleMetadata />
      <PanelSuggestedImages />
      <PanelRelatedPosts />
    </PluginSidebar>
  </Fragment>
);

registerPlugin(PLUGIN_NAMESPACE, {
  render: WordLiftSidebar,
  icon: <WordLiftIcon />
});
