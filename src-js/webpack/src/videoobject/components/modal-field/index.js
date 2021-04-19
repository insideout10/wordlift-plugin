/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import React from "react";


/**
 * Internal dependencies
 */
import "./index.scss"
import {classExtractor} from "../../../mappings/blocks/helper";
import {ModalInput} from "../modal-input";


export const ModalFieldWrapper = ({title, description, isRequired=true, children}) => {
    const titleClasses = classExtractor({
        'wl-modal-field__title--required': isRequired
    })

    return (
        <div className={"wl-modal-field"}>
            <p className={"wl-modal-field__title " + titleClasses}>{title}</p>
            {description !== "" && <p className={"wl-modal-field__description "}>{description}</p>}
            {children}
        </div>

    )
}


/**
 *
 * @param props
 * @returns {*}
 * @constructor
 */

export const ModalField = (props) => {
    const {
        type = "text",
        defaultValue
    } = props

    return (<ModalFieldWrapper {...props}>
        <ModalInput type={type} defaultValue={defaultValue}/>
    </ModalFieldWrapper>)

}