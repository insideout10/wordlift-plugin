/**
 * WlPostExcerpt shows the text area for the wordlift excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from 'react'

/**
 * Internal dependencies.
 */
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";

class WlPostExcerpt extends React.Component {
    render() {
        return (
            <WlContainer>
                <WlColumn>
                    Or
                </WlColumn>
            </WlContainer>
        )
    }
}

export default WlPostExcerpt