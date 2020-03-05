/**
 * WlPostExcerptButtonGroup shows footer buttons for the metabox.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from 'react'

/**
 * Internal dependencies.
 */
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import WlActionButton from "../../../faq/components/wl-action-button";

export const WlPostExcerptButtonGroup = ({ updateHandler, deleteHandler }) => {
    return (
        <WlContainer fullWidth={true}>
            <WlColumn className={"wl-col--width-20 wl-col--low-padding"}>
                <WlActionButton
                    text={"Use"}
                    className={"wl-action-button--delete wl-action-button--normal"}
                    onClickHandler={deleteHandler}
                />
            </WlColumn>
            <WlColumn className={"wl-col--width-30 wl-col--low-padding"}>
                <WlActionButton
                    text={"Refresh"}
                    className={"wl-action-button--update wl-action-button--primary"}
                    onClickHandler={updateHandler}
                />
            </WlColumn>
        </WlContainer>
    );
};
