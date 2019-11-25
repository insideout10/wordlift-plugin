import React from "react";

import Select from "../Select";
import CreateItem from "./Item/CreateItem";
import SelectItem from "./Item/SelectItem";

const EntitySelect = ({ createEntity, selectEntity, items, value, showCreate, ...props }) => {
  // const theseItems = [];
  //
  // if (showCreate) theseItems.concat( <CreateItem key={1000} label={value} onClick={() => createEntity(value)}/> );

  const elements = (showCreate
    ? [<CreateItem key={1000} label={value} onClick={() => createEntity(value)} />]
    : []
  ).concat(items.map((item, index) => <SelectItem key={index} item={item} onClick={() => selectEntity(item)} />));

  return (
    <Select value={value} {...props}>
      {elements}
    </Select>
  );
};

export default EntitySelect;
