/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/**
* WordPress dependencies
*/
import { Panel, PanelBody, PanelRow } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

const RelatedImage = styled.img`
  max-width: 100%;
  cursor: move;
`;

const wordlift = global["wordlift"];

const Images = () => {
  let images = [];
  let entities = window["_wlMetaBoxSettings"].settings["entities"];
  for (const wlEntities in entities) {
    images = images.concat(entities[wlEntities].images);
  }
  return [...new Set(images)];
};

export default () =>
  Images().length > 0 ? (
    <Panel>
      <PanelBody title={ __( 'Suggested images', 'wordlift' ) } initialOpen={false}>
        <h4>{ __( 'Drag & Drop in editor', 'wordlift' ) }</h4>
        {Images().map((item) => (
          <PanelRow>
            <RelatedImage src={item} />
          </PanelRow>
        ))}
      </PanelBody>
    </Panel>
  ) : (
    ""
  );
