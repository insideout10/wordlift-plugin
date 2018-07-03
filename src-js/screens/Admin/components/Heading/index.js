/**
 * Components: Heading.
 *
 * A heading component.
 *
 * @since 3.20.0
 */

// External dependencies.
import React from "react";

const Heading = ({ title, visible }) => (
  <h2 style={{ display: visible ? "block" : "none" }}>{title}</h2>
);

export default Heading;
