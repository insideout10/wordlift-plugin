import React from "react";
import styled from "styled-components";

const VerticalContainer = styled.div`
  position: relative;
  overflow-y: visible;
  width: calc(100% + 6px);
  margin-top: 10px;
  margin-bottom: 8px;
  margin-left: -3px;
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
