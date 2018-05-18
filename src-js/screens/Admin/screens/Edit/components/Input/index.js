import React from "react";

import Wrapper from "./Wrapper";
import X from "./X";

const Input = ({ onCancel, onInputChange, ...props }) => (
  <Wrapper>
    <input onChange={e => onInputChange(e.target.value)} {...props} />
    <X onClick={onCancel} />
  </Wrapper>
);

export default Input;
