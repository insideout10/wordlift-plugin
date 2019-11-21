/* globals wp, wordlift */
/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/*
 * Packages via WordPress global
 */
const { Panel, PanelBody, PanelRow } = wp.components;

const RelatedImage = styled.img`
  max-width: 100%;
  cursor: move;
`;

const Images = () => {
  let images = [];
  for (var wlEntities in wordlift.entities) {
    images = images.concat(wordlift.entities[wlEntities].images);
  }
  return [...new Set(images)];
};

const SuggestedImagesPanel = () => (
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

export default SuggestedImagesPanel;
