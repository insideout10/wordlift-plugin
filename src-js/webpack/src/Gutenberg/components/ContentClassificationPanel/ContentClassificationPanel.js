/* globals wp, wlSettings */
/**
 * Components: Content Classification Panel component.
 *
 * @since 3.2x
 */
/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies
 */
import { AnnotateSelected, ReceiveAnalysisResultsEvent } from "../../index.classification-box";
import * as Constants from "../../constants";
import AddEntity from "../../../Edit/components/AddEntity";
import Header from "../../../Edit/components/Header";
import VisibleEntityList from "../../../Edit/containers/VisibleEntityList";
import Wrapper from "../../../Edit/components/App/Wrapper";

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

class ContentClassificationPanel extends React.Component {
  constructor(props) {
    super(props);
    this.asyncBlockAnalysis();
  }

  asyncBlockAnalysis() {
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
          this.props.dispatch(ReceiveAnalysisResultsEvent(JSONData, currentBlock.clientId));
        } else {
          console.log(`No content found in block ${currentBlock.clientId}`);
        }
      });
  }

  componentDidMount() {
    wp.richText.registerFormatType(Constants.PLUGIN_FORMAT_NAMESPACE, {
      name: Constants.PLUGIN_FORMAT_NAMESPACE,
      title: Constants.PLUGIN_NAMESPACE,
      tagName: "span",
      className: null,
      edit({ isActive, value, onChange }) {
        AnnotateSelected(value.start, value.end);
        return <Fragment />;
      }
    });
  }

  componentWillUnmount() {
    wp.richText.registerFormatType(Constants.PLUGIN_FORMAT_NAMESPACE);
  }

  render() {
    return (
      <Panel>
        <PanelBody title="Content classification" initialOpen={true}>
          <PanelRow>
            <Wrapper>
              {this.props.entities && this.props.entities.size > 0 ? (
                <Fragment>
                  <AddEntity showCreate={canCreateEntities} />
                  <Header />
                  <VisibleEntityList />
                </Fragment>
              ) : (
                "Analyzing content..."
              )}
            </Wrapper>
          </PanelRow>
        </PanelBody>
      </Panel>
    );
  }
}

export default ContentClassificationPanel;
