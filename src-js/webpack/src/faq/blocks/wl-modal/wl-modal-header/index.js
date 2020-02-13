/**
 * WlModalHeader : it shows the header for modal component.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies.
 */
import { classExtractor } from "../../../../mappings/blocks/helper";
import "./index.scss";
import { WlContainer } from "../../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../../mappings/blocks/wl-column";

export const WlModalHeader = ({ title, description, children, modalCloseClickedListener }) => {
  const classes = classExtractor({
    "wl-modal-header": true
  });
  return (
    <div className={classes}>
      <WlContainer>
        <WlColumn className={"wl-col--width-95"}>
          <b> {title} </b>
          <br />
          <p>{description}</p>
        </WlColumn>
        <WlColumn className={"wl-col--width-5"}>
          <span
            className="dashicons dashicons-no-alt wl-modal-header-icon"
            onClick={() => {
              modalCloseClickedListener();
            }}
          />
        </WlColumn>
      </WlContainer>

      {children}
    </div>
  );
};
