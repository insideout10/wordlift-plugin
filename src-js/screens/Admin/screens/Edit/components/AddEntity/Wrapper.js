import React from "react";
import styled from "styled-components";

const VerticalContainer = styled.div`
  width: 250px;
  position: relative;
  overflow-y: visible;
  margin-bottom: 8px;
`;

const HorizontalContainer = styled.div`
  white-space: nowrap;
  overflow-x: hidden;

  > * {
    display: inline-block;
    box-sizing: border-box;

    &:first-child {
      // Slightly smaller to accommodate the hover effect.
      width: 248px;
      margin: 0 1px;

      margin-left: ${props => (props.open ? "-250px" : "1px")};
      transition: all 200ms ease-out;
    }

    &:last-child {
      width: 250px;
    }
  }
`;

const Wrapper = ({ children, ...props }) => (
  <VerticalContainer {...props}>
    <HorizontalContainer {...props}>{children}</HorizontalContainer>
  </VerticalContainer>
);

export default Wrapper;
