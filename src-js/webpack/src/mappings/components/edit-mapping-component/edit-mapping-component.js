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
  PROPERTY_LIST_CHANGED_ACTION,
  RULE_GROUP_LIST_CHANGED_ACTION,
  MAPPING_HEADER_CHANGED_ACTION,
  NOTIFICATION_CHANGED_ACTION,
  PROPERTY_ITEMS_BULK_ACTION,
  BULK_ACTION_SELECTION_CHANGED_ACTION,
  MAPPING_ID_CHANGED_FROM_API_ACTION
} from "../../actions/actions";
import EditComponentMapping from "../../mappings/edit-component-mapping";
import BulkActionComponent from "../bulk-action-component";
import {
  EditComponentNotificationArea,
  RuleGroupWrapper,
  EditComponentTitleArea
} from "./edit-sub-components";
import {EditMappingSaveButton} from "./edit-mapping-save-button";

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wl_edit_mappings_config"] || {};

class EditMappingComponent extends React.Component {
  constructor(props) {
    super(props);
    this.handleTitleChange = this.handleTitleChange.bind(this);
    this.bulkActionSubmitHandler = this.bulkActionSubmitHandler.bind(this);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
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
  bulkActionSubmitHandler() {
    this.props.dispatch(PROPERTY_ITEMS_BULK_ACTION);
  }

  bulkActionOptionChangedHandler(event) {
    const selectedBulkOption = event.target.value;
    const action = BULK_ACTION_SELECTION_CHANGED_ACTION;
    action.payload = {
      selectedBulkAction: selectedBulkOption
    };
    this.props.dispatch(action);
  }

  /**
   * Get edit mapping item if the mapping id is supplied
   * via the url
   */
  getMappingItemByMappingId() {
    const url = editMappingSettings.rest_url + "/" + editMappingSettings.wl_edit_mapping_id;
    fetch(url, {
      method: "GET",
      headers: {
        "content-type": "application/json",
        "X-WP-Nonce": editMappingSettings.wl_edit_mapping_rest_nonce
      }
    }).then(response =>
      response.json().then(data => {
        // Dispatch title changed
        const mapping_header_action = MAPPING_HEADER_CHANGED_ACTION;
        mapping_header_action.payload = {
          title: data.mapping_title,
          mapping_id: data.mapping_id
        };
        this.props.dispatch(mapping_header_action, data.mapping_title);

        //Dispatch property list changed after applying filters
        const property_list_action = PROPERTY_LIST_CHANGED_ACTION;
        property_list_action.payload = {
          value: EditComponentMapping.mapPropertyAPIKeysToUi(data.property_list)
        };
        this.props.dispatch(property_list_action);

        // Dispatch rule group list changed after applying filters
        const rule_group_list_action = RULE_GROUP_LIST_CHANGED_ACTION;
        rule_group_list_action.payload = {
          value: EditComponentMapping.mapRuleGroupListAPIKeysToUi(data.rule_group_list)
        };
        this.props.dispatch(rule_group_list_action);
      })
    );
  }

  render() {
    return (
      <React.Fragment>
        <EditComponentNotificationArea notificationData={this.props.notificationData} />
        <EditComponentTitleArea
          wl_edit_mapping_id={editMappingSettings.wl_edit_mapping_id}
          wl_add_mapping_text={editMappingSettings.wl_add_mapping_text}
          wl_edit_mapping_text={editMappingSettings.wl_edit_mapping_text}
        />
        <input
          type="text"
          className="wl-form-control wl-input-class"
          value={this.props.title}
          placeholder="Title"
          onChange={e => {
            this.handleTitleChange(e);
          }}
        />
        <br /> <br />
        <RuleGroupWrapper />
        <br />
        <br />
        <PropertyListComponent />
        <br />
        <div className="wl-container wl-container-full">
          <BulkActionComponent
            choosenCategory={this.props.choosenCategory}
            bulkActionSubmitHandler={this.bulkActionSubmitHandler}
            bulkActionOptionChangedHandler={this.bulkActionOptionChangedHandler}
          />
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
