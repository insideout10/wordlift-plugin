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
import WlButton from "../wl-button";

const FaqEditButtonGroup = ({ updateHandler, deleteHandler }) => {
  return (
    <WlContainer fullWidth={true}>
      <WlColumn className={"wl-col--width-50"}>
        <WlButton text={"delete"} className={"wl-button--normal wl-button--text-bold"} onClickHandler={deleteHandler} />
      </WlColumn>
      <WlColumn className={"wl-col--width-50"}>
        <WlButton
          text={"update"}
          className={"wl-button--primary wl-button--text-bold"}
          onClickHandler={updateHandler}
        />
      </WlColumn>
    </WlContainer>
  );
};

export default FaqEditButtonGroup;
