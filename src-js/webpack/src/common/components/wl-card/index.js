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
import { classExtractor } from "../../../mappings/blocks/helper";

export const WlCard = ({ children, alignCenter = false, onClickHandler = null }) => {
  const classes = classExtractor({
    "wl-card": true,
    "wl-card-center": alignCenter,
  });
  return (
    <div
      className={classes}
      onClick={() => {
        onClickHandler !== null ? onClickHandler() : null;
      }}
    >
      {children}
    </div>
  );
};
