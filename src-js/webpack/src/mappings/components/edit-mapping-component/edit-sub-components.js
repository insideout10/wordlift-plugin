/**
 * EditSubComponents.js : It have a list of components to be used by
 * edit component.js
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import RuleGroupListComponent from "./rule-group-list-component";


export const EditComponentNotificationArea = ({ notificationData }) => {
  return (
    <React.Fragment>
      {"" !== notificationData.message && (
        <div className={"wl-notice-custom-margin notice notice-" + notificationData.type + " is-dismissble"}>
          <p>{notificationData.message}</p>
        </div>
      )}
    </React.Fragment>
  );
};

export const EditComponentTitleArea = ({ mappingId, addMappingText, editMappingText }) => {
  return (
    <h1 className="wp-heading-inline wl-mappings-heading-text">
      {mappingId === undefined ? addMappingText : editMappingText}
    </h1>
  );
};

export const RuleGroupWrapper = () => {
  return (
    <table className="wp-list-table widefat striped wl-table wl-container-full">
      <thead>
        <tr>
          <td colSpan={2}>
            <b>Rules</b>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td className="wl-bg-light wl-description wl-col-30">Here we show the help text</td>
          <td className="wl-col-70">
            <div>
              <b>Use the mapping if</b>
              <RuleGroupListComponent />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  );
};
