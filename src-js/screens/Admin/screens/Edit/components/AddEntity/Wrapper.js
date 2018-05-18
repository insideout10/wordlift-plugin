import styled from "styled-components";

const Wrapper = styled.div`
  width: 250px;
  white-space: nowrap;
  // overflow: hidden;

  > * {
    display: inline-block;
    box-sizing: border-box;

    // Slighly smaller to accomodate the hover effect.
    width: 248px;
    margin: 1px;
  }
  
  > *:last-child {
    position: absolute;
  }
`;

export default Wrapper;
