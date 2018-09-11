import styled from "styled-components";

const Wrapper = styled.div`
  padding: 8px 0 6px;
  border-bottom: 2px solid #007aff;
  display: inline-block;
  width: 100%;

  > input {
    border: 0;
    outline: none;
    float: left;
    width: calc(100% - 36px) !important;
    font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu,
      Cantarell, "Helvetica Neue", sans-serif;
    line-height: 20px;
    font-size: 14px;
    padding: 0 6px;
  }
`;

export default Wrapper;
