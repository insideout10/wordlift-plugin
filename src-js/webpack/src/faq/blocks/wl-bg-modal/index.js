/**
 * WlBgModal : it shows the modal with a transparent black background.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.26.0
 */

/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies.
 */
import { classExtractor } from "../../../mappings/blocks/helper";
import "./index.scss";

export const WlBgModal = ({ shouldOpenModal, children }) => {
  const classes = classExtractor({
    "wl-bg-modal": true,
    "wl-bg-modal--open": shouldOpenModal,
    "wl-bg-modal--closed": !shouldOpenModal
  });
  return <div className={classes}>{children}</div>;
};
