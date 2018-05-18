import React from "react";

import Wrapper from "./Wrapper";

const List = ({ open, children }) => (
  <Wrapper open={open}>
    {children.map((item, index) => <li key={index}>{item}</li>)}
  </Wrapper>
);

export default List;
