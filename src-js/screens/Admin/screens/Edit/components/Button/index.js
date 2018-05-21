/**
 * External dependencies
 */
import React from "react";

import Wrapper from "./Wrapper";

const Button = ({ children, label, ...props }) => (
  <Wrapper {...props}>
    {label}
    {children}
  </Wrapper>
);

export default Button;
