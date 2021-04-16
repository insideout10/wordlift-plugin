/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import {Panel, PanelBody, PanelRow} from "@wordpress/components";
import { doAction } from "@wordpress/hooks";

/**
 * Internal dependencies
 */

import Spinner from "../spinner";

export default class VideosPanel extends React.Component {

    componentDidMount() {

    }

    render() {
        return (
            <Panel>
                <PanelBody title="Videos" initialOpen={false} onToggle={() => {
                    setTimeout(() => {
                        doAction("wordlift.renderVideoList")
                    }, 500)
                }}>
                    <div id={"wl-video-list"}></div>
                </PanelBody>
            </Panel>
        );
    }
}
