import React from "react";

import Input from "../Input";
import List from "../List";

const Select = ({ open, value, onCancel, onInputChange, children }) => (
  <div>
    <Input onCancel={onCancel} onInputChange={onInputChange} value={value} />
    <List open={open}>
      {children}
    </List>
  </div>
);

export default Select;
