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

    if (props.type === "textarea") {
        return (
            <textarea className={"wl-modal-field-input"}
                      {...props} onChange={(event) => {
                props.onChange(props.identifier, event.target.value)
            }} rows={8}/>)
    }

    return (<input className={"wl-modal-field-input"} {...props} onChange={(event) => {
        props.onChange(props.identifier, event.target.value)
    }}/>)
}