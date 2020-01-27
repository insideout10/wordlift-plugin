/**
 * EditMappingComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import PropertyListComponent from "./property-list-component";
import {
    TITLE_CHANGED_ACTION,
    EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION
} from "../../actions/actions";
import EditComponentMapping from "../../mappings/edit-component-mapping";
import BulkActionComponent from "../bulk-action-component";
import {
  EditComponentNotificationArea,
  RuleGroupWrapper,
  EditComponentTitleArea
} from "./edit-sub-components";
import {EditMappingSaveButton} from "./edit-mapping-save-button";
import {EditMappingPropertyBulkAction} from "./edit-mapping-property-bulk-action";
import {EditMappingTitleSection} from "./edit-mapping-title-section";

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wl_edit_mappings_config"] || {};

class EditMappingComponent extends React.Component {
  constructor(props) {
    super(props);
    this.handleTitleChange = this.handleTitleChange.bind(this);
  }
  /**
   * When the title is changed, this method saves it in the redux store.
   * @param {Object} event The event which is fired when mapping title changes
   */
  handleTitleChange(event) {
    const action = TITLE_CHANGED_ACTION;
    action.payload = {
      value: event.target.value
    };
    this.props.dispatch(action);
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
        <br />
        <br />
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
    title: state.TitleSectionData.title,
    notificationData: state.NotificationData,
    stateObject: state,
    choosenCategory: state.PropertyListData.choosenPropertyCategory
  };
};

export default connect(mapStateToProps)(EditMappingComponent);
