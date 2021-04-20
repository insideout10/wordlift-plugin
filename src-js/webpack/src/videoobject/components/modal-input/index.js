/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */
import "./index.scss"

export const ModalInput = (props) => {
    return (<input className={"wl-modal-field-input"}
        {...props} onChange={ (event) => {
            props.onChange(props.identifier, event.target.value)
        }
    } />)
}