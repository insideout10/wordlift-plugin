/**
 * External dependencies
 */
import React from "react";
import styled from "@emotion/styled";

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

const settings = global["_wlMetaBoxSettings"].settings;

const ArticleMetadataPanel = () => (
  <Panel>
    <PanelBody title="Article metadata" initialOpen={false}>
      <PanelRow>
        <div>
          <StyledWlAuthorIcon />
          {settings["currentUser"]}
        </div>
        <div>
          <StyledWlCalenderIcon />
          {settings["publishedDate"]}
        </div>
      </PanelRow>
    </PanelBody>
  </Panel>
);

export default ArticleMetadataPanel;
