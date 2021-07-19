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
import {openModal} from "../../actions";
import {jwPlayerIcon, vimeoIcon, youtubeIcon} from "./icons";


class Video extends React.Component {

    constructor(props) {
        super(props);
        this.setIcon = this.setIcon.bind(this)
    }

    setIcon(video) {

        if (video.content_url === undefined) {
            return youtubeIcon({height: 24, width: 24})
        }
        if (video.content_url.includes("youtube.com")) {
            return youtubeIcon({height: 24, width: 24})
        } else if (video.content_url.includes("vimeo.com")) {
            return vimeoIcon({height: 24, width: 24})
        } else if (video.content_url.includes("jwplayer.com")) {
            return jwPlayerIcon({height: 24, width: 24})
        }

    }

    render() {
        const {video} = this.props
        return (
            <React.Fragment>
                <WlContainer className={"wl-single-video"}>
                    <WlColumn className={"wl-col--width-20"}>
                        {this.setIcon(video)}
                    </WlColumn>
                    <WlColumn className={"wl-col--width-50 wl-single-video__name"}>
                        {video.name}
                    </WlColumn>
                    <WlColumn className={"wl-col--width-30"}>
                        <WlActionButton text={"Edit"} className={"wl-action-button--primary"} onClickHandler={() => {
                            this.props.dispatch(openModal({videoIndex: this.props.videoIndex}))
                        }}/>
                    </WlColumn>
                </WlContainer>

            </React.Fragment>
        )
    }


}


export default connect()(Video);