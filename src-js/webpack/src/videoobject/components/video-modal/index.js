/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import {WlModal} from "../../../common/components/wl-modal";
import WordLiftIcon from "../../../block-editor/wl-logo-big.svg"
import "./index.scss"
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {
    PanelBody,
    TextControl,
    TextareaControl,
    CheckboxControl,
    RangeControl,
    ColorPicker,
    RadioControl,
    SelectControl
} from "@wordpress/components";
import {nextVideo, previousVideo, closeModal} from "../../actions";
import {WlBgModal} from "../../../common/components/wl-bg-modal";


class VideoModal extends React.Component {

    addDisabledClass(isDisabled) {
        if (isDisabled) {
            return " wl-video-modal__menu_button--disabled"
        }
        return "";
    }

    renderIfVideoExists() {
        if (!this.props.video) {
            return (<React.Fragment/>)
        }
        const video = this.props.video
        return (
            <React.Fragment>
                <WlContainer className={"wl-video-modal__header"}>
                    <WlColumn className={"wl-col--width-90"}>
                        <WlContainer>
                            <WlColumn>
                                <WordLiftIcon/>
                            </WlColumn>
                            <WlColumn>
                                Edit video
                            </WlColumn>
                        </WlContainer>
                    </WlColumn>
                    <WlColumn className={"wl-col--width-10"}>
                        <WlContainer>
                            <WlColumn>
                                <span
                                    className={"dashicons dashicons-arrow-left-alt2 wl-video-modal__menu_button"
                                    + this.addDisabledClass(!this.props.isPreviousEnabled)}
                                    onClick={() => this.props.dispatch(previousVideo())}
                                    disabled={!this.props.isPreviousEnabled}/>
                            </WlColumn>
                            <WlColumn>
                                <span
                                    className={"dashicons dashicons-arrow-right-alt2 wl-video-modal__menu_button "
                                    + this.addDisabledClass(!this.props.isNextEnabled)}
                                    onClick={() => this.props.dispatch(nextVideo())}
                                    disabled={!this.props.isNextEnabled}/>
                            </WlColumn>
                            <WlColumn>
                                <span className="dashicons dashicons-no-alt wl-video-modal__menu_button"
                                      onClick={() => this.props.dispatch(closeModal())}/>
                            </WlColumn>
                        </WlContainer>
                    </WlColumn>
                </WlContainer>
                <WlContainer>
                    <WlColumn className={"wl-col--width-70 wl-col--align-center"}>
                        <img src={video.thumbnail_urls[video.thumbnail_urls.length - 1]} height={700} width={1000}/>
                    </WlColumn>
                    <WlColumn className={"wl-col--width-30 "}>
                        <TextControl label={"NAME"} help={"The title of the video"} value={video.name}/>
                        <TextControl label={"DESCRIPTION"}
                                     help={"The description of the video, HTML Tags are ignored"}
                                     value={video.description}/>
                        {video.thumbnail_urls.length > 0 && video.thumbnail_urls.map((thumbnail_url) => {
                            return (<TextControl value={thumbnail_url} label={"THUMBNAIL URL"}/>)
                        })}
                        <TextControl label={"UPLOAD DATE"}
                                     help={"The date the video was published in IS8601 format"}
                                     value={video.upload_date}/>
                        <TextControl label={"CONTENT URL"}
                                     value={video.content_url}/>
                        <TextControl label={"DURATION"}
                                     value={video.duration}/>
                        <TextControl label={"EMBED URL"}
                                     value={video.embed_url}/>

                    </WlColumn>
                </WlContainer>

            </React.Fragment>)
    }


    render() {
        return (
            <WlBgModal shouldOpenModal={this.props.isModalOpened}>
                <WlModal shouldOpenModal={this.props.isModalOpened} className={"wl-video-modal--full-width"}>
                    {this.renderIfVideoExists()}
                </WlModal>
            </WlBgModal>
        )
    }

}

const mapStateToProps = (state) => {
    return {
        isModalOpened: state.isModalOpened,
        videoIndex: state.videoIndex,
        video: state.videos[state.videoIndex],
        isNextEnabled: state.videoIndex + 1 < state.videos.length,
        isPreviousEnabled: state.videoIndex - 1 >= 0

    }
}

export default connect(
    mapStateToProps
)(VideoModal);