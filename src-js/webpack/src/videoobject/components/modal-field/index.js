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


export const ModalFieldLabel = ({title, description, isRequired = true, children}) => {
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

        /* Below properties are used for label */
        title,
        description,
        value,

        onChange,

        // The identifier of the video object js object, for example thumbnail_urls
        identifier,


    } = props

    return (<ModalFieldLabel title={title} description={description}>
        <ModalInput type={type} value={value} onChange={onChange} identifier={identifier}/>
    </ModalFieldLabel>)

}