import React from "react";

import Wrapper from "./Wrapper";
import Label from "./Label";
import Cloud from "../../EntityTile/Cloud";

const SelectItem = ({ item, key }) => (
  <Wrapper key={key}>
    <Label>{item.label}</Label>
    <Cloud className="fa fa-cloud" local={"local" === item.scope} />
  </Wrapper>
);

export default SelectItem;
