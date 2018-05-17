import React from "react";

import Wrapper from "./Wrapper";
import Label from "./Label";
import Cloud from "../../EntityTile/Cloud";

/**
 * The `local` attribute is handled as such:
 * `"local" === item.scope ? 1 : 0`
 *
 * See here for more information:
 * https://github.com/styled-components/styled-components/issues/1198
 *
 * @param item
 * @param key
 * @param props
 * @returns {*}
 * @constructor
 */
const SelectItem = ({ item, key, ...props }) => (
  <Wrapper key={key} {...props}>
    <Label>{item.label}</Label>
    <Cloud className="fa fa-cloud" local={"local" === item.scope ? 1 : 0} />
  </Wrapper>
);

export default SelectItem;
