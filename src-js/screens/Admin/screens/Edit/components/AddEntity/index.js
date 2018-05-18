import React from "react";

import Wrapper from "./Wrapper";
import Button from "../Button";
import Arrow from "../Arrow";
import Select from "../Select";

const AddEntity = () => (
  <Wrapper>
    <Button>
      Add ...
      <Arrow height="8px" color="white" />
    </Button>
    <Select />
  </Wrapper>
);

export default AddEntity;
