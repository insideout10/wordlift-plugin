/**
 * WlContainer:  Container for all the WlColumns, decides how to places
 * the elements in the container
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss";
import {classExtractor} from "../helper";

export const WlContainer = ({children, className = "", fullWidth = false, rowLayout = false, shouldWrap=false}) => {
    const classes = classExtractor({
        "wl-container--full-width": fullWidth,
        "wl-container--row-layout": rowLayout,
        "wl-container--wrap" : shouldWrap
    });
    return <div className={"wl-container " + classes + " " + className}>{children}</div>;
};
