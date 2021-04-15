/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import {Panel, PanelBody, PanelRow} from "@wordpress/components";

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
                <PanelBody title="Videos" initialOpen={false}>
                    <div id={"wl-video-list"}></div>
                </PanelBody>
            </Panel>
        );
    }
}
