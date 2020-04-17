import styled from "@emotion/styled";

const X = styled.div`
  font-family: sans-serif;
  background-color: #007aff;
  color: white;
  width: 12px;
  height: 12px;
  display: inline-block;
  text-align: center;
  line-height: 12px;
  font-size: 12px;
  padding: 1px;
  cursor: pointer;
  border-radius: 100%;
  transform: rotate(45deg);

  &::after {
    content: "+";
    font-size: 12px;
    width: 100%;
    height: 100%;
  }
`;

export default X;
