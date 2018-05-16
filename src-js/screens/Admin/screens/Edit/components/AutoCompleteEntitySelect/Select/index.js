import React from "react";

import Wrapper from "./Wrapper";

const Select = ({ items, Item, children }) => (
  <Wrapper>
    {children}
    {items.map((item, key) => <Item item={item} key={key} />)}
  </Wrapper>
);

export default Select;
