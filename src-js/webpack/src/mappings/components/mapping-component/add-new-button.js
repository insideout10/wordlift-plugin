/**
 * AddNewButton: Add new button on the mapping screen.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

export const AddNewButton = () => (
  <h1 className="wp-heading-inline wl-mappings-heading-text">
    Mappings &nbsp;&nbsp;
    <a href="?page=wl_edit_mapping" className="button wl-mappings-add-new">
      Add New
    </a>
  </h1>
);
