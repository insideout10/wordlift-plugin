import React from "react";
import styled from "styled-components";

const VerticalContainer = styled.div`
  width: 100%;
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
      width: calc(100% - 2px);
      margin: 0 1px;

      margin-left: ${props => (props.open ? "-100%" : "1px")};
      transition: all 200ms ease-out;
    }

    &:last-child {
      width: 100%;
    }
  }
`;

const Wrapper = ({ children, ...props }) => (
  <VerticalContainer {...props}>
    <HorizontalContainer {...props}>{children}</HorizontalContainer>
  </VerticalContainer>
);

export default Wrapper;
