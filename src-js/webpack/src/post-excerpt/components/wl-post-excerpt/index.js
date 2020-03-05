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
import PropTypes from "prop-types";

class WlPostExcerpt extends React.Component {
    render() {
        return (
            <WlContainer>
                <WlColumn>
                    {this.props.orText}
                </WlColumn>
            </WlContainer>
        )
    }
}
// Define all the props used by this component.
WlPostExcerpt.propTypes = {
    orText: PropTypes.string
};

export default WlPostExcerpt