import styled from "styled-components";
import { darken, rgb } from "polished";

const Wrapper = styled.li`
  list-style: none;

  box-sizing: border-box;
  position: relative;
  margin: 1px auto;
  width: 248px;
  min-height: 32px;
  padding: 4px 0px 4px 12px;
  background-color: rgb(245, 245, 245);
  color: rgb(102, 102, 102);

  cursor: pointer;

  &:hover {
    background-color: ${darken(0.02, rgb(245, 245, 245))};
  }
`;

export default Wrapper;
