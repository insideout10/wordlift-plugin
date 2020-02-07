/**
 * Answer component displaying single question
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies
 */
import "./index.scss"

const Answer = ( {answer} ) => {
    return (
        <p className={"wl-faq-answer-title"}>
            {answer}
        </p>
    )
};

export default Answer
