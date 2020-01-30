/**
 * MappingNoActiveItemMessage: Show empty message if the item is not present.
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
import { ACTIVE_CATEGORY } from "../category-component";

export const MappingNoActiveItemMessage = ({ mappingItems, chosenCategory }) => {
  return (
    0 === mappingItems.filter(el => el.mappingStatus === ACTIVE_CATEGORY).length &&
    chosenCategory === ACTIVE_CATEGORY && (
      <tr>
        <td colSpan={3}>
          <div className="wl-container text-center">
            No Mapping items found, click on
            <b>&nbsp; Add New </b>
          </div>
        </td>
      </tr>
    )
  );
};
