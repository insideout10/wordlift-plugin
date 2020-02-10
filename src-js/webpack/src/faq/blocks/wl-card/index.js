/**
 * WlCard : shows a card for containing elements
 * @since 3.26.0
 * @author Naveen Muthusamy
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies.
 */
import "./index.scss";

export const WlCard = ({ children, onClickHandler = null }) => {
  return (
    <div
      className={"wl-card"}
      onClick={() => {
        onClickHandler !== null ? onClickHandler() : null;
      }}
    >
      {children}
    </div>
  );
};
