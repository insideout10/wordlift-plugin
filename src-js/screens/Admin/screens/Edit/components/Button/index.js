/**
 * External dependencies
 */
import React from "react";

import Wrapper from "./Wrapper";

const Button = ({ label, icon, disabled }) => (
  <Wrapper disabled={disabled}>
      <div>{label}</div>
      <div>{icon}</div>
  </Wrapper>
);

export default Button;
