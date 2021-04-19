/**
 * @since 3.30.0
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

/**
 *
 * @param props
 * @returns {*}
 * @constructor
 */

export const ModalField = (props) => {

    const {
        title,
        description="",
        placeholder,
        defaultValue = "",
        isRequired = true,
        type = "text",
    } = props

    const titleClasses = classExtractor({
        'wl-modal-field__title--required': isRequired
    })

    return (
        <div className={"wl-modal-field"}>
            <p className={"wl-modal-field__title " + titleClasses}>{title}</p>
            {description !== "" && <p className={"wl-modal-field__description "}>{description}</p>}
            <input type={type} placeholder={placeholder} className={"wl-modal-field__input"}
                   defaultValue={defaultValue}/>
        </div>

    )


}