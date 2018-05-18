/**
 * External dependencies
 */
import React from "react";

import Wrapper from "./Wrapper";

const Button = ({ children, ...props }) => (
  <Wrapper {...props}>
    {children}
  </Wrapper>
);

export default Button;
