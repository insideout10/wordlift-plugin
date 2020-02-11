/**
 * FaqFloatingActionButton for closing the edit screen.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss"

export const FaqFloatingActionButton = ({ buttonText, buttonClickHandler }) => {
    return (
        <React.Fragment>
            <button type={"button"} onClick={()=>{buttonClickHandler()}} id={"wl-faq-fab"}>
                {buttonText}
            </button>
        </React.Fragment>
    )
};
