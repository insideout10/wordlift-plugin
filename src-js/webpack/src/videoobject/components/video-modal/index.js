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
import {closeModal, modalFieldChanged, saveVideoDataRequest} from "../../actions";

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
                    <WlColumn className={"wl-col--width-30 wl-col--height-90"}>
                        <ModalField title={"NAME"}
                                    description={"The title of the video"}
                                    placeholder={"Name of file"}
                                    onChange={onChangeListener}
                                    value={video.name}
                                    identifier={"name"}
                        />
                        <ModalField title={"DESCRIPTION"}
                                    description={"The description of the video, HTML Tags are ignored"}
                                    value={video.description}
                                    onChange={onChangeListener}
                                    identifier={"description"}/>

                        <ThumbnailField thumbnails={video.thumbnail_urls} videoIndex={this.props.videoIndex}/>
                        <ModalField title={"UPLOAD DATE"}
                                    description={"The date the video was published in IS8601 format"}
                                    value={video.upload_date}
                                    onChange={onChangeListener}
                                    identifier={"upload_date"}/>
                        <ModalField title={"CONTENT URL"}
                                    value={video.content_url}
                                    onChange={onChangeListener}
                                    identifier={"content_url"}
                        />
                        <ModalField title={"DURATION"}
                                    value={video.duration}
                                    onChange={onChangeListener}
                                    identifier={"duration"}/>
                        <ModalField title={"EMBED URL"}
                                    value={video.embed_url}
                                    onChange={onChangeListener}
                                    identifier={"embed_url"}/>
                    </WlColumn>
                </WlContainer>


            </React.Fragment>)
    }


    render() {
        return (
            <WlBgModal shouldOpenModal={this.props.isModalOpened}>
                <WlModal shouldOpenModal={this.props.isModalOpened} className={"wl-video-modal--full-width"}>
                    {this.renderIfVideoExists()}
                    <WlContainer fullWidth={true}>
                        <WlColumn className={"wl-col--width-90"}>
                        </WlColumn>
                        <WlColumn className={"wl-col--width-10"}>
                            <WlActionButton className={"wl-action-button--primary"}
                                            text={__("Save", "wordlift")}
                            onClickHandler={ () => {
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