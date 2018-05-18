import styled from "styled-components";
import { lighten } from "polished";

const Wrapper = styled.ul`
  background: white;
  border-top: 0;
  border-bottom: 1px solid #007aff;
  border-left: 1px solid #007aff;
  border-right: 1px solid #007aff;
  box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  max-height: 200px;
  overflow-y: scroll;
  font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu,
    Cantarell, "Helvetica Neue", sans-serif;
  line-height: 20px;
  font-size: 14px;

  > li {
    padding: 8px 4px;
    cursor: pointer;

    &:hover {
      background-color: ${lighten(0.3, "#007aff")};
    }
  }
`;

export default Wrapper;
