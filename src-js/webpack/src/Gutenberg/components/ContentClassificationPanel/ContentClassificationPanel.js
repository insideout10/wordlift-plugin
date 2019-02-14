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
import AnnotationService from "../../services/AnnotationService";
import * as Constants from "../../constants";
import AddEntity from "../../../Edit/components/AddEntity";
import Header from "../../../Edit/components/Header";
import VisibleEntityList from "../../../Edit/containers/VisibleEntityList";
import Wrapper from "../../../Edit/components/App/Wrapper";
import Store2 from "../../stores/Store2";
import { setValue } from "../../../Edit/components/AddEntity/actions";

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
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          console.log(`Requesting analysis for block ${block.clientId}...`);
          let annotationService = new AnnotationService(block.attributes.content, block.clientId);
          this.props.dispatch(annotationService.WordliftAnalyze());
        } else {
          console.log(`No content found in block ${block.clientId}`);
        }
      });
  }

  componentDidMount() {
    wp.richText.registerFormatType(Constants.PLUGIN_FORMAT_NAMESPACE, {
      name: Constants.PLUGIN_FORMAT_NAMESPACE,
      title: Constants.PLUGIN_NAMESPACE,
      tagName: "span",
      className: null,
      edit: ({ isActive, value, onChange }) => {
        this.props.dispatch(AnnotationService.AnnotateSelected(value.start, value.end));
        let selected = value.text.substring(value.start, value.end);
        Store2.dispatch(setValue(selected));
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
                  <AddEntity showCreate={canCreateEntities} store={Store2} />
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
