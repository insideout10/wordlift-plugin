import styled from "styled-components";

const X = styled.div`
  font-family: sans-serif;
  background-color: #007aff;
  color: white;
  width: 20px;
  height: 20px;
  display: inline-block;
  text-align: center;
  line-height: 20px;
  cursor: pointer;
  border-radius: 100%;
  transform: rotate(45deg);

  &::after {
    content: "+";
    font-size: 20px;
    width: 100%;
    height: 100%;
  }
`;

export default X;
