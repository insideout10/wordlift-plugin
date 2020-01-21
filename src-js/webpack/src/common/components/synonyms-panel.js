/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/**
 * WordPress dependencies
 */
import { Panel, PanelBody, PanelRow, TextControl } from "@wordpress/components";

const wordlift = global["wordlift"];

const SynonymsPanel = () => (
  <Panel>
    <PanelBody title="Synomyms" initialOpen={false}>
      <PanelRow>
        <TextControl value={wordlift["currentUser"]} label="abcd" />
      </PanelRow>
    </PanelBody>
  </Panel>
);

export default SynonymsPanel;
