/**
 * FieldTypeField : it displays the field type field in the edit mappings ui
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
import {PROPERTY_DATA_CHANGED_ACTION} from "../../../actions/actions";
import SelectComponent from "../../select-component";

class _FieldTypeField extends React.Component {
    render() {
        return(
            <tr>
                <td colSpan="2">Field Type</td>
                <td colSpan="3">
                    <SelectComponent
                        className="wl-form-select"
                        options={this.props.fieldTypeHelpTextOptions}
                        value={this.props.propData.fieldTypeHelpText}
                        onChange={event => {
                            PROPERTY_DATA_CHANGED_ACTION.payload = {
                                fieldKey: "fieldTypeHelpText",
                                value: event.target.value,
                                propertyId: this.props.propData.property_id
                            };
                            this.props.dispatch(PROPERTY_DATA_CHANGED_ACTION);
                        }}
                    />
                </td>
            </tr>
        )
    }
}

export const FieldTypeField = connect()(_FieldTypeField);