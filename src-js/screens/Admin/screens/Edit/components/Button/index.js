/**
 * External dependencies
 */
import React from "react";

import Wrapper from "./Wrapper";

const Button = ({ children, label, ...props }) => (
  <Wrapper {...props}>
    <div
      style={{
        width: "calc(100% - 16px)",
        overflow: "hidden",
        textOverflow: "ellipsis",
        whiteSpace: "nowrap"
      }}
    >
      {label}
    </div>
    {children}
  </Wrapper>
);

export default Button;
