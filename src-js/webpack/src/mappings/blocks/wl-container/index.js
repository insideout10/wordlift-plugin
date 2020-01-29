import React from "react";

import "./index.scss";

export const WlContainer = ({ children, className }) => {
  return <div className={"wl-container " + className}>{children}</div>;
};
