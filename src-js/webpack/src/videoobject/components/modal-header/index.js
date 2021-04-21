/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies.
 */
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import WordLiftIcon from "../../../block-editor/wl-logo-big.svg";
import {closeModalAndRefresh, nextVideo, previousVideo} from "../../actions";


export const addDisabledClass = (isDisabled) => {
    if (isDisabled) {
        return " wl-video-modal__menu_button--disabled"
    }
    return "";
}

export const ModalHeader = ({isPreviousEnabled, isNextEnabled, dispatch}) => {
    return <WlContainer className={"wl-video-modal__header"}>
        <WlColumn className={"wl-col--width-80"} lessPadding={true}>
            <WlContainer className={"wl-video-modal__header__container"}>
                <WlColumn lessPadding={true}>
                    <WordLiftIcon/>
                </WlColumn>
                <WlColumn lessPadding={true}>
                    <h4>Edit video</h4>
                </WlColumn>
            </WlContainer>
        </WlColumn>
        <WlColumn className={"wl-col--width-20"} lessPadding={true}>
            <WlContainer className={"wl-video-modal__menu_button_container"}>
                <WlColumn lessPadding={true}>
                                <span
                                    className={"dashicons dashicons-arrow-left-alt2 wl-video-modal__menu_button"
                                    + addDisabledClass(!isPreviousEnabled)}
                                    onClick={() => dispatch(previousVideo())}
                                    disabled={!isPreviousEnabled}/>
                </WlColumn>
                <WlColumn lessPadding={true}>
                                <span
                                    className={"dashicons dashicons-arrow-right-alt2 wl-video-modal__menu_button "
                                    + addDisabledClass(!isNextEnabled)}
                                    onClick={() => dispatch(nextVideo())}
                                    disabled={!isNextEnabled}/>
                </WlColumn>
                <WlColumn lessPadding={true}>
                                <span className="dashicons dashicons-no-alt wl-video-modal__menu_button"
                                      onClick={() => dispatch(closeModalAndRefresh())}/>
                </WlColumn>
            </WlContainer>
        </WlColumn>
    </WlContainer>;
}