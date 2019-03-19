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
import styled from "styled-components";

/**
 * Internal dependencies
 */
import AnnotationService from "../../services/AnnotationService";
import * as Constants from "../../constants";
import Header from "../../../Edit/components/Header";
import VisibleEntityList from "../../../Edit/containers/VisibleEntityList";
import Wrapper from "../../../Edit/components/App/Wrapper";
import Store2 from "../../stores/Store2";
import { setValue } from "../../../Edit/components/AddEntity/actions";
import Spinner from "../Spinner";

/*
 * Packages via WordPress global
 */
const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

const LoaderWrapper = styled.div`
  padding: 8px 0;
  color: rgb(102, 102, 102);
  min-height: 16px;
  line-height: 16px;
  font-weight: 600;
  font-size: 12px;
`;

class ContentClassificationPanel extends React.Component {
  constructor(props) {
    super(props);
  }

  asyncBlockAnalysis() {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        let annotationService = new AnnotationService(block);
        this.props.dispatch(annotationService.wordliftAnalyze());
      });
    this.props.dispatch(AnnotationService.analyseLocalEntities());
  }

  componentDidMount() {
    this.asyncBlockAnalysis();
    wp.richText.registerFormatType(Constants.PLUGIN_FORMAT_NAMESPACE, {
      name: Constants.PLUGIN_FORMAT_NAMESPACE,
      title: Constants.PLUGIN_NAMESPACE,
      tagName: "span",
      className: null,
      edit: ({ isActive, value, onChange }) => {
        this.props.dispatch(AnnotationService.annotateSelected(value.start, value.end));
        const selected = value.text.substring(value.start, value.end);
        Store2.dispatch(setValue(selected));
        return <Fragment />;
      }
    });
  }

  componentWillUnmount() {
    wp.richText.unregisterFormatType(Constants.PLUGIN_FORMAT_NAMESPACE);
  }

  render() {
    return (
      <Panel>
        <PanelBody title="Content classification" initialOpen={true}>
          <Wrapper>
            <Fragment>
              <Header />
              {this.props.entities && this.props.entities.size > 0 ? (
                <VisibleEntityList />
              ) : this.props.processingBlocks && this.props.processingBlocks.length === 0 ? (
                <LoaderWrapper>No content found</LoaderWrapper>
              ) : (
                <Spinner />
              )}
            </Fragment>
          </Wrapper>
        </PanelBody>
      </Panel>
    );
  }
}

export default ContentClassificationPanel;
