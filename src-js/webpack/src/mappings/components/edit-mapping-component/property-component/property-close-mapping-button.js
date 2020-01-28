/**
 * PropertyCloseMappingButton : Display the close button for the property if it is opened
 * by the user.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";

export const PropertyCloseMappingButton = ({ propData, switchState }) => {
  return (
    <tr>
      <td colSpan="2" />
      <td>
        <button
          disabled={propData.propertyHelpText.length <= 0}
          className="wl-close-mapping button action bg-primary text-white"
          onClick={() => switchState(propData.property_id)}
        >
          Close Mapping
        </button>
      </td>
    </tr>
  );
};
