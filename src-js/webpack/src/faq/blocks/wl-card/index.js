/**
 * WlCard : shows a card for containing elements
 * @since 3.26.0
 * @author Naveen Muthusamy
 */

/**
 * External dependencies.
 */
import React from "react"

/**
 * Internal dependencies.
 */
import "./index.scss"

export const WlCard = ({children}) => {
    return (
        <div className={'wl-card'}>
            {children}
        </div>
    )
}