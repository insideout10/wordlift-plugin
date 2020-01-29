import React from "react";
import "./index.scss";
export const WlColumn = ({ children, className }) => {
  return <div className={"wl-col " + className}>{children}</div>;
};
