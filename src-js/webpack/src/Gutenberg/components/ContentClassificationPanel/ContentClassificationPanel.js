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
import { setValue } from "../../components/AddEntityPanel/AddEntity/actions";
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
  constructor (props) {
    super(props);
  }

  asyncBlockAnalysis () {

    /**
     *
     * @param {array} collector
     * @param {{name, innerBlocks}[]} blocks
     */
    const collectBlocks = (collector, blocks) => {
      blocks.forEach((block) => {
        if ("core/paragraph" === block.name || "core/freeform" === block.name) {
          collector.push(block);
        }

        collectBlocks(collector, block.innerBlocks);
      });

      return collector;
    };

    class BlockOps {

      constructor (blocks) {
        this._blocks = blocks;
        this._mappings = [];
        this._blockSeparatorLength = this.blockSeparator.length;
      }

      get blockSeparator () {
        return ".\n";
      }

      get mappings () {
        return this._mappings;
      }

      getHtml () {

        let cursor = 0;

        return (this.html ? this.html : this.html = this._blocks
          .map((block) => {
            const content = block.attributes.content;
            const start = cursor;
            cursor += content.length + this._blockSeparatorLength;

            this._mappings.push([start, cursor, block]);

            return content;
          })
          .join(this.blockSeparator));
      }

      insertHtml (at, fragment) {

        for (let i = 0; i < this._mappings.length; i++) {
          const  mapping = this._mappings[i];

          if (at < mapping[0] || at >= mapping[1]) {
            continue;
          }

          const localAt = at - mapping[0];
          const block = mapping[2];
          const content = block.attributes.content;

          wp.data.dispatch("core/editor").updateBlock(block.clientId, {
            attributes: {
              content: block.attributes.content =
                content.substring(0, localAt) + fragment + content.substring(localAt)
            }
          });
        }

      }
    }

    const ops = new BlockOps(collectBlocks([], wp.data.select("core/editor").getBlocks()));

    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        let annotationService = new AnnotationService(block);
        this.props.dispatch(annotationService.wordliftAnalyze());
      });
    this.props.dispatch(AnnotationService.analyseLocalEntities());
  }

  componentDidMount () {
    this.asyncBlockAnalysis();
    wp.richText.registerFormatType(Constants.PLUGIN_FORMAT_NAMESPACE, {
      name: Constants.PLUGIN_FORMAT_NAMESPACE,
      title: Constants.PLUGIN_NAMESPACE,
      tagName: "span",
      className: "textannotation",
      edit: ({ value }) => {
        if (value.start && value.end) {
          this.props.dispatch(AnnotationService.annotateSelected(value.start, value.end));
          const blockClientId = wp.data.select("core/editor").getSelectedBlockClientId();
          const selected = value.text.substring(value.start, value.end);
          let formats = [];
          for (var i = value.start; i < value.end; i++) {
            formats.push(value.formats[i]);
          }
          Store2.dispatch(
            setValue({
              value: selected,
              start: value.start,
              end: value.end,
              formats,
              blockClientId
            })
          );
        }
        return null;
      }
    });
  }

  componentWillUnmount () {
    wp.richText.unregisterFormatType(Constants.PLUGIN_FORMAT_NAMESPACE);
  }

  render () {
    return (
      <Panel>
        <PanelBody title="Content classification" initialOpen={true}>
          <Wrapper>
            <Fragment>
              <Header/>
              {this.props.processingBlocks && this.props.processingBlocks.length > 0 && <Spinner/>}
              {this.props.entities && this.props.entities.size > 0 && <VisibleEntityList/>}
            </Fragment>
          </Wrapper>
        </PanelBody>
      </Panel>
    );
  }
}

export default ContentClassificationPanel;
