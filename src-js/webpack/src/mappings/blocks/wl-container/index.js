/**
 * WlContainer:  Container for all the WlColumns, decides how to places
 * the elements in the container
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss";


export const WlContainer = ({ children, className = "", fullWidth = false }) => {
    if ( fullWidth ) {
        className += " wl-container--full-width "
    }
  return <div className={"wl-container " + className}>{children}</div>;
};
