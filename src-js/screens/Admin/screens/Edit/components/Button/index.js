/**
 * External dependencies
 */
import React from "react";

import Wrapper from "./Wrapper";

const Button = ({ label, icon, disabled, children }) => (
  <Wrapper disabled={disabled}>
    {/*<div>{label}</div>*/}
    {/*<div>{icon}</div>*/}
    {children}
  </Wrapper>
);

export default Button;
