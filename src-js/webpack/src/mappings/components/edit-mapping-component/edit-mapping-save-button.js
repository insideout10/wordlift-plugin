/**
 * MappingBulkAction : it displays the save button for the edit mapping screen.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
import {EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION} from "../../actions/actions";
/**
 * Internal dependencies.
 */

class _EditMappingSaveButton extends React.Component {
    constructor(props) {
        super(props);
        this.saveMappingItem = this.saveMappingItem.bind(this)
    }
    saveMappingItem() {
        EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION.payload = {
            mappingData: this.props.mappingData
        };
        this.props.dispatch( EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION )
    }
    render() {
        return (
            <div className="wl-col wl-align-right">
                <button className="button action" onClick={this.saveMappingItem} disabled={this.props.title === ""}>
                    Save
                </button>
            </div>
        )
    }
}

export const EditMappingSaveButton = connect( state => ({
    mappingData: state,
    title: state.title,
}))(_EditMappingSaveButton)