/* globals wp, wordlift */
/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/*
 * Internal dependencies.
 */
import WlAuthorIcon from "../../../../../../src/images/svg/wl-author-icon.svg";
import WlCalenderIcon from "../../../../../../src/images/svg/wl-calendar-icon.svg";

const StyledWlAuthorIcon = styled(WlAuthorIcon)`
  width: 18px;
  margin-right: 5px;
`;

const StyledWlCalenderIcon = styled(WlCalenderIcon)`
  width: 18px;
  margin-right: 5px;
`;

/*
 * Packages via WordPress global
 */
const { Panel, PanelBody, PanelRow } = wp.components;

const ArticleMetadataPanel = () => (
  <Panel>
    <PanelBody title="Article metadata" initialOpen={false}>
      <PanelRow>
        <div>
          <StyledWlAuthorIcon />
          {wordlift.currentUser}
        </div>
        <div>
          <StyledWlCalenderIcon />
          {wordlift.publishedDate}
        </div>
      </PanelRow>
    </PanelBody>
  </Panel>
);

export default ArticleMetadataPanel;
