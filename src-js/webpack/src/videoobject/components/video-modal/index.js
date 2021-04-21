/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import {WlModal} from "../../../common/components/wl-modal";
import "./index.scss"
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {WlBgModal} from "../../../common/components/wl-bg-modal";
import {ModalField} from "../modal-field";
import ThumbnailField from "../thumbnail-field";
import {ModalHeader} from "../modal-header";
import WlActionButton from "../../../faq/components/wl-action-button";
import {__} from "@wordpress/i18n";
import {modalFieldChanged, saveVideoDataRequest} from "../../actions";

class VideoModal extends React.Component {

    renderIfVideoExists() {

        if (!this.props.video) {
            return (<React.Fragment/>)
        }
        const video = this.props.video

        const onChangeListener = (key, value) => {

            this.props.dispatch(modalFieldChanged({
                key, value
            }))
        }


        return (
            <React.Fragment>
                <ModalHeader {...this.props} />
                <WlContainer>
                    <WlColumn className={"wl-col--width-70 wl-col--align-center"}>
                        <embed src={video.embed_url} style={{
                            "width": "100%",
                            "height": "100%"
                        }}/>
                    </WlColumn>
                    <WlColumn className={"wl-col--width-30"}>
                        <ModalField title={__("NAME", "wordlift")}
                                    description={__("The title of the video", "wordlift")}
                                    placeholder={__("Name of file", "wordlift")}
                                    onChange={onChangeListener}
                                    value={video.name}
                                    identifier={"name"}
                        />
                        <ModalField title={__("DESCRIPTION", "wordlift")}
                                    type={"textarea"}
                                    description={__("The description of the video, HTML Tags are ignored", "wordlift")}
                                    value={video.description}
                                    onChange={onChangeListener}
                                    identifier={"description"}/>

                        <ThumbnailField thumbnails={video.thumbnail_urls} videoIndex={this.props.videoIndex}/>
                        <ModalField title={__("UPLOAD DATE", "wordlift")}
                                    description={__("The date the video was published in IS8601 format", "wordlift")}
                                    value={video.upload_date}
                                    onChange={onChangeListener}
                                    identifier={"upload_date"}/>
                        <ModalField title={__("CONTENT URL", "wordlift")}
                                    description={__("A URL pointing to the actual video media file", "wordlift")}
                                    value={video.content_url}
                                    onChange={onChangeListener}
                                    identifier={"content_url"}
                        />
                        <ModalField title={__("DURATION", "wordlift")}
                                    description={__("The duration of the video in ISO 8601 format.", "wordlift")}
                                    value={video.duration}
                                    onChange={onChangeListener}
                                    identifier={"duration"}/>
                        <ModalField title={__("EMBED URL", "wordlift")}
                                    description={__("A URL pointing to a player for the specific video.", "wordlift")}
                                    value={video.embed_url}
                                    onChange={onChangeListener}
                                    identifier={"embed_url"}/>
                    </WlColumn>
                </WlContainer>


            </React.Fragment>)
    }


    render() {
        return (
            <WlBgModal shouldOpenModal={this.props.isModalOpened} key={this.props.videoIndex}>
                <WlModal shouldOpenModal={this.props.isModalOpened} className={"wl-video-modal--full-width"}>
                    {this.renderIfVideoExists()}
                    <WlContainer fullWidth={true}>
                        <WlColumn className={"wl-col--width-85"}>
                        </WlColumn>
                        <WlColumn className={"wl-col--width-15"}>
                            <WlActionButton className={"wl-action-button--primary"}
                                            text={__("Save", "wordlift")}
                                            onClickHandler={() => {
                                                this.props.dispatch(saveVideoDataRequest())
                                            }}/>
                        </WlColumn>
                    </WlContainer>
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