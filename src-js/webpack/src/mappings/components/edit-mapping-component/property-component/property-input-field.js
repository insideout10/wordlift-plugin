/**
 * PropertyInputField : it shows the input field for the property item.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

export const PropertyInputField = ({ propData, handleChangeForPropertyField, inputKeyName }) => {
  return (
    <React.Fragment>
      <input
        type="text"
        className="wl-form-control"
        defaultValue={propData[inputKeyName]}
        onChange={event => {
          handleChangeForPropertyField(inputKeyName, event);
        }}
      />
    </React.Fragment>
  );
};
