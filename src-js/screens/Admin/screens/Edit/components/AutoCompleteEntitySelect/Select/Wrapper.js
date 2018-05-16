import styled from "styled-components";

const Wrapper = styled.ul`
  padding: 0;
  margin: 0;

  overflow-y: scroll;

  max-height: 200px;
  background: #fff;
  position: absolute;
  z-index: 1000;
  border: 1px solid rgba(0, 0, 0, 0.2);
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
`;

export default Wrapper;
