/**
 * FaqEditButtonGroup displaying single question
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss";
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";
import WlActionButton from "../wl-action-button";

const FaqEditButtonGroup = ({ updateHandler, deleteHandler }) => {
  return (
    <WlContainer fullWidth={true}>
      <WlColumn className={"wl-col--width-40 wl-col--low-padding"}>
        <WlActionButton
          text={"delete"}
          className={"wl-action-button--delete wl-action-button--normal wl-action-button--text-bold"}
          onClickHandler={() => {
            deleteHandler();
          }}
        />
      </WlColumn>
      <WlColumn className={"wl-col--width-10"} />
      <WlColumn className={"wl-col--width-40 wl-col--low-padding"}>
        <WlActionButton
          text={"update"}
          className={"wl-action-button--update wl-action-button--primary wl-action-button--text-bold"}
          onClickHandler={() => {
            updateHandler();
          }}
        />
      </WlColumn>
    </WlContainer>
  );
};

export default FaqEditButtonGroup;
