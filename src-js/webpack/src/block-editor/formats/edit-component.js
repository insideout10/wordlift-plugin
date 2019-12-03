/**
 * This file registers the "wordlift/annotation" format type.
 *
 * The format type uses the {@link EditComponent} for the `edit` property. The
 * EditComponent is hooked to the {@link dispatch} of `wordlift/editor` store
 * to set the value.
 *
 * The format type also broadcasts the selection using WordPress hooks in order
 * to have the AddEntity component receive the selection. This is needed because
 * the AddEntity component has still its own store.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { withDispatch } from "@wordpress/data";
import { doAction } from "@wordpress/hooks";
import { Fragment } from "@wordpress/element";

/**
 * Internal dependencies
 */
import { ANNOTATION_CHANGED, SELECTION_CHANGED } from "../../common/constants";
import { WORDLIFT_STORE } from "../../common/constants";

// Keeps the window timeout reference to delay sending events while the user
// is performing the selection.
let delay;

/**
 * The EditComponent.
 *
 * @param {{start, end, text}} value The selection start/end.
 * @param {boolean} isActive Whether the format is active.
 * @param {{id}} activeAttributes The active attributes, i.e. the annotation id.
 * @param {Function} onSelectionChange The function to call when the selection changes.
 * @returns {Function} A stateless component.
 * @constructor
 */
const EditComponent = ({ onChange, value, isActive, activeAttributes, onSelectionChange, setFormat }) => {
  // Send the selection change event.
  if (delay) clearTimeout(delay);

  delay = setTimeout(() => {
    const selection = value.text.substring(value.start, value.end);
    onSelectionChange(selection);
    setFormat({ onChange, value });
    doAction(SELECTION_CHANGED, { selection });
  }, 200);

  // Send the annotation change event.
  const payload =
    "undefined" !== typeof isActive &&
    "undefined" !== typeof activeAttributes &&
    "undefined" !== typeof activeAttributes.id
      ? activeAttributes.id
      : undefined;
  doAction(ANNOTATION_CHANGED, payload);

  return <Fragment />;
};

/**
 * Connect the `onSelectionChange` function to the `setValue` dispatch.
 */
export default withDispatch((dispatch, ownProps) => {
  const { setValue, setFormat } = dispatch(WORDLIFT_STORE);

  return {
    onSelectionChange: setValue,
    setFormat
  };
})(EditComponent);
