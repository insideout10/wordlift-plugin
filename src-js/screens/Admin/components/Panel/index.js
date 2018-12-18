/**
 * Components: Panel.
 *
 * A panel that can be hidden.
 *
 * @since 3.20.0
 */
import React from "react";

const Panel = ({ display, children }) => (
  <div style={{ display: display }}>{children}</div>
);

export default Panel;
