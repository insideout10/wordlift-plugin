import styled from "styled-components";
import { darken, rgb } from "polished";

const Wrapper = styled.div`
  list-style: none;
  padding: 4px 8px;
  box-sizing: border-box;
  position: relative;
  margin: 1px auto;
  width: 248px;
  min-height: 32px;
  background-color: rgb(245, 245, 245);
  color: rgb(102, 102, 102);

  cursor: pointer;

  &:hover {
    background-color: ${darken(0.02, rgb(245, 245, 245))};
  }
`;

export default Wrapper;
