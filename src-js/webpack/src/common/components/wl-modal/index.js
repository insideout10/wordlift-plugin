/**
 * WlModal : it shows the modal component block
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

export const WlModal = ({ shouldOpenModal, children, className="" }) => {
  const classes = classExtractor({
    "wl-modal": true,
    "wl-modal--open": shouldOpenModal,
    "wl-modal--closed": !shouldOpenModal
  });
  return <div className={classes + " " + className}>{children}</div>;
};
