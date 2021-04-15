/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import "./index.scss"
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import WlActionButton from "../../../faq/components/wl-action-button";

class Video extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        const {video} = this.props
        return (
            <React.Fragment>
                <WlContainer>
                    <WlColumn>
                        {video.name}
                    </WlColumn>
                    <WlColumn>
                        <WlActionButton>
                            Edit
                        </WlActionButton>
                    </WlColumn>
                </WlContainer>

            </React.Fragment>
        )
    }


}


export default connect()(Video);