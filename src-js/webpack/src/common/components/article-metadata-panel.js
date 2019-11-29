/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/**
 * WordPress dependencies
 */
import { Panel, PanelBody, PanelRow } from "@wordpress/components";

/**
 * Internal dependencies
 */
import WlAuthorIcon from "../../../../../src/images/svg/wl-author-icon.svg";
import WlCalenderIcon from "../../../../../src/images/svg/wl-calendar-icon.svg";

const StyledWlAuthorIcon = styled(WlAuthorIcon)`
  width: 18px;
  margin-right: 5px;
`;

const StyledWlCalenderIcon = styled(WlCalenderIcon)`
  width: 18px;
  margin-right: 5px;
`;

const wordlift = global["wordlift"];

const ArticleMetadataPanel = () => (
  <Panel>
    <PanelBody title="Article metadata" initialOpen={false}>
      <PanelRow>
        <div>
          <StyledWlAuthorIcon />
          {wordlift["currentUser"]}
        </div>
        <div>
          <StyledWlCalenderIcon />
          {wordlift["publishedDate"]}
        </div>
      </PanelRow>
    </PanelBody>
  </Panel>
);

export default ArticleMetadataPanel;
