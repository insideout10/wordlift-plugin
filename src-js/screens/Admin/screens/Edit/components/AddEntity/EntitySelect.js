import React from "react";

import Select from "../Select";
import CreateItem from "./Item/CreateItem";
import SelectItem from "./Item/SelectItem";

const EntitySelect = ({
  createEntity,
  selectEntity,
  items,
  value,
  ...props
}) => (
  <Select {...props}>
    {[<CreateItem key={1000} label={value} onClick={() => createEntity(value)} />].concat(
      items.map((item, index) => (
        <SelectItem key={index} item={item} onClick={() => selectEntity(item)} />
      ))
    )}
  </Select>
);

export default EntitySelect;
