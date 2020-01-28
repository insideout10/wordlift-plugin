/**
 * EditMappingComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import PropertyListComponent from "./property-component/property-list-component";
import {
    EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION
} from "../../actions/actions";

import {
  EditComponentNotificationArea,
  RuleGroupWrapper,
} from "./edit-sub-components";
import {EditMappingSaveButton} from "./edit-mapping-save-button";
import {EditMappingPropertyBulkAction} from "./edit-mapping-property-bulk-action";
import {EditMappingTitleSection} from "./edit-mapping-title-section";

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wl_edit_mappings_config"] || {};

class EditMappingComponent extends React.Component {
  constructor(props) {
    super(props);
  }
  componentDidMount() {
    if (editMappingSettings.wl_edit_mapping_id !== undefined) {
      this.getMappingItemByMappingId();
    }
  }

  /**
   * Get edit mapping item if the mapping id is supplied
   * via the url
   */
  getMappingItemByMappingId() {
      const mappingId = editMappingSettings.wl_edit_mapping_id;
      EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION.payload = {
          mappingId: mappingId
      };
      this.props.dispatch( EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION )
  }

  render() {
    return (
      <React.Fragment>
        <EditComponentNotificationArea notificationData={this.props.notificationData} />
        <EditMappingTitleSection />
        <RuleGroupWrapper />
        <PropertyListComponent />
        <br />
        <div className="wl-container wl-container-full">
          <EditMappingPropertyBulkAction/>
          <EditMappingSaveButton/>
        </div>
      </React.Fragment>
    );
  }
}

const mapStateToProps = function(state) {
  return {
    notificationData: state.NotificationData,
  };
};

export default connect(mapStateToProps)(EditMappingComponent);
