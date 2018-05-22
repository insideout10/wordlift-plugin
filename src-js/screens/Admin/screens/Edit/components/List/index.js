import React from "react";

import Wrapper from "./Wrapper";

const List = ({ open, children }) => (
  <Wrapper open={open}>
    {0 < children.length &&
      children.map((item, index) => <li key={index}>{item}</li>)}
    {0 === children.length && (
      <li style={{ padding: "8px", cursor: "initial" }} key={0}>
        No results.
      </li>
    )}
  </Wrapper>
);

export default List;
