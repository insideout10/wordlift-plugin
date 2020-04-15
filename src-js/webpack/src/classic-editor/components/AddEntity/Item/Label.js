import styled from "styled-components";

const Label = styled.div`
  display: inline-block;
  position: relative;
  box-sizing: border-box;
  max-width: 180px;
  text-overflow: ellipsis;
  overflow: hidden;
  margin-top: 4px;
  min-height: 16px;
  line-height: 16px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 600;
  font-size: 12px;
  -moz-user-select: none;
  hyphens: auto;
`;

export default Label;
