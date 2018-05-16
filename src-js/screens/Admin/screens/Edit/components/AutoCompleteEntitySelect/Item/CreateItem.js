import React from "react";

import Wrapper from "./Wrapper";
import Label from "./Label";
import Cloud from "../../EntityTile/Cloud";

const CreateItem = ({ label }) => (
  <Wrapper>
    <Label>{label}</Label>
  </Wrapper>
);

export default CreateItem;
