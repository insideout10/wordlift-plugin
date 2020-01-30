/**
 * RuleGroupWrapper : It renders the rules and rule groups in a table.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies.
 */
import RuleGroupListComponent from "./rule-group-list-component";

export const RuleGroupWrapper = () => {
  return (
    <table className="wp-list-table widefat striped wl-table wl-container-full wl-rule-group-wrapper">
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
