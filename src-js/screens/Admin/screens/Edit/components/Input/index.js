import React from "react";

import Wrapper from "./Wrapper";
import X from "./X";

const Input = props => (
  <Wrapper>
    <input {...props} />
    <X />
  </Wrapper>
);

export default Input;
