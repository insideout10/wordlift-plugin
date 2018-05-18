import React from "react";

import Wrapper from "./Wrapper";

const List = ({ children }) => (
  <Wrapper>{children.map(item => <li>{item}</li>)}</Wrapper>
);

export default List;
