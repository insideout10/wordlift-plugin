/**
 * MappingListItemTrashCategoryOptions : it returns the option available for the trash category.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";

/**
 * Internal dependencies.
 */
import {ACTIVE_CATEGORY, TRASH_CATEGORY} from "../category-component";
import {MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION} from "../../actions/actions";

class _MappingListItemTrashCategoryOptions extends React.Component {
    constructor(props) {
        super(props);
        this.updateMappingItem = this.updateMappingItem.bind(this);
    }
    // Updates or deletes the mapping items based on the request
    updateMappingItem(mappingItem, type = "PUT") {
        MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION.payload = {
            type: type,
            mappingItems: [mappingItem]
        };
        this.props.dispatch( MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION )
    }
    render() {
        return(
            <React.Fragment>
                <span className="edit wl-mappings-link">
                  <a
                      onClick={() => {
                          const mappingData = this.props.mappingData;
                          mappingData.mappingStatus = ACTIVE_CATEGORY;
                          this.updateMappingItem(this.props.mappingData);
                      }}
                  >
                    Restore
                  </a>
                  |
                </span>
                        <span className="trash wl-mappings-link">
                  <a
                      onClick={() => {
                          this.updateMappingItem(this.props.mappingData, "DELETE");
                      }}
                  >
                    Delete Permanently
                  </a>{" "}
                            |
                </span>
            </React.Fragment>
        )
    }

}

export const MappingListItemTrashCategoryOptions = connect()(_MappingListItemTrashCategoryOptions)