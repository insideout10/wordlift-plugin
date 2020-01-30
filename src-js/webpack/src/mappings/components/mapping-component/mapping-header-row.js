/**
 * MappingHeaderRow: shows the header row for the table.
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
import { MappingHeaderCheckbox } from "./mapping-header-checkbox";
import { MappingHeaderTitle } from "./mapping-header-title";

export const MappingHeaderRow = () => (
  <tr>
    <MappingHeaderCheckbox />
    <MappingHeaderTitle />
  </tr>
);
