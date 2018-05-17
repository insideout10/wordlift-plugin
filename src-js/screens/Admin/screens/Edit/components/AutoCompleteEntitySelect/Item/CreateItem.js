import React from "react";

import Wrapper from "./Wrapper";
import Label from "./Label";

const CreateItem = ({ label, onClick }) => (
  <Wrapper onClick={onClick}>
    <Label>{label}</Label>
  </Wrapper>
);

export default CreateItem;
