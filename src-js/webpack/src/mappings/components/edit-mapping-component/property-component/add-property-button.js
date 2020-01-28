/**
 * AddPropertyButton : it handles adding the new property
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux"

/**
 * Internal dependencies.
 */
import {ADD_MAPPING_ACTION} from "../../../actions/actions";

class _AddPropertyButton extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <tr className="wl-text-right">
                <td colSpan="3">
                    <br />
                    <button
                        className="button action bg-primary text-white wl-add-mapping"
                        style={{ margin: "auto" }}
                        onClick={() => {this.props.dispatch(ADD_MAPPING_ACTION)}}
                    >
                        Add Mapping
                    </button>{" "}
                    <br />
                </td>
            </tr>
        )
    }
}

export const AddPropertyButton = connect()(_AddPropertyButton);