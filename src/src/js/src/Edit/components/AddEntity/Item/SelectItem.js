import React from "react";

import Wrapper from "./Wrapper";
import Label from "./Label";
import Cloud from "../../EntityTile/Cloud";
import Description from "./Description";
import DisplayTypes from "./DisplayTypes";

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
const SelectItem = ({ item, ...props }) => (
  <Wrapper {...props}>
    <Label title={item.label}>{item.label}</Label>
    <Cloud className="fa fa-cloud" local={"local" === item.scope ? 1 : 0} />
    {0 < item.descriptions.length && <Description title={item.descriptions[0]}>{item.descriptions[0]}</Description>}
    <DisplayTypes>{item.displayTypes}</DisplayTypes>
  </Wrapper>
);

export default SelectItem;
