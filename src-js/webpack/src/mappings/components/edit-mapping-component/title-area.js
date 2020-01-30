/**
 * TitleArea : it shows the title area like Add mapping | Edit mapping text.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

export const TitleArea = ({ mappingId, addMappingText, editMappingText }) => (
  <h1 className="wp-heading-inline wl-mappings-heading-text">
    {mappingId === undefined ? addMappingText : editMappingText}
  </h1>
);
