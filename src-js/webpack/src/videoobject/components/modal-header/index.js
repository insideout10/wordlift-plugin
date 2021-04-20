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
import {closeModal, closeModalAndRefresh, nextVideo, previousVideo} from "../../actions";


export const addDisabledClass = (isDisabled) => {
    if (isDisabled) {
        return " wl-video-modal__menu_button--disabled"
    }
    return "";
}

export const ModalHeader = ({isPreviousEnabled, isNextEnabled, dispatch}) => {
    return <WlContainer className={"wl-video-modal__header"}>
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
                                    + addDisabledClass(!isPreviousEnabled)}
                                    onClick={() => dispatch(previousVideo())}
                                    disabled={!isPreviousEnabled}/>
                </WlColumn>
                <WlColumn>
                                <span
                                    className={"dashicons dashicons-arrow-right-alt2 wl-video-modal__menu_button "
                                    + addDisabledClass(!isNextEnabled)}
                                    onClick={() => dispatch(nextVideo())}
                                    disabled={!isNextEnabled}/>
                </WlColumn>
                <WlColumn>
                                <span className="dashicons dashicons-no-alt wl-video-modal__menu_button"
                                      onClick={() => dispatch(closeModalAndRefresh())}/>
                </WlColumn>
            </WlContainer>
        </WlColumn>
    </WlContainer>;
}