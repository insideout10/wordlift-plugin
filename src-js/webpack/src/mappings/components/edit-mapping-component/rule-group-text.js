/**
 * RuleGroupText : it shows the rule group text conditionally
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * Internal dependencies.
 */

export const RuleGroupText = ({ ruleGroupList, index }) => {
  return (
    <React.Fragment>
      {// dont show extra `or` text if there
      // is no rule group below
      index !== ruleGroupList.length - 1 && (
        <div className="wl-container">
          <div className="wl-col">
            <b>Or</b>
          </div>
        </div>
      )}
    </React.Fragment>
  );
};
