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
import { WlColumn } from "../../blocks/wl-column";
import {WlContainer} from "../../blocks/wl-container";

/**
 * Internal dependencies.
 */

export const RuleGroupText = ({ ruleGroupList, index }) => {
  return (
    <React.Fragment>
      {// dont show extra `or` text if there
      // is no rule group below
      index !== ruleGroupList.length - 1 && (
        <WlContainer>
          <WlColumn>
            <b>Or</b>
          </WlColumn>
        </WlContainer>
      )}
    </React.Fragment>
  );
};
