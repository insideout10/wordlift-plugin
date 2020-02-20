/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { Panel, PanelBody, PanelRow } from "@wordpress/components";

export default () => (
    <Panel>
        <PanelBody title="FAQ" initialOpen={true}>
            <div id="wl-faq-meta-list-box"></div>
            <div id="wl-faq-modal"></div>
        </PanelBody>
    </Panel>
);
