/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/**
 * WordPress dependencies
 */
import { Panel, PanelBody, PanelRow } from "@wordpress/components";

const RelatedImage = styled.img`
  max-width: 100%;
  cursor: move;
`;

const wordlift = global["wordlift"];

const Images = () => {
  let images = [];
  for (const wlEntities in wordlift["entities"]) {
    images = images.concat(wordlift["entities"][wlEntities].images);
  }
  return [...new Set(images)];
};

export default () => (
  <Panel>
    <PanelBody title="Suggested images" initialOpen={false}>
      <h4>Drag & Drop in editor</h4>
      {Images().map(item => (
        <PanelRow>
          <RelatedImage src={item} />
        </PanelRow>
      ))}
    </PanelBody>
  </Panel>
);
