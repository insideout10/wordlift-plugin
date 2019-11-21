/* globals wp */
/**
 * External dependencies
 */
import React from "react";
import styled from "styled-components";

/*
 * Packages via WordPress global
 */
const { Spinner } = wp.components;

const SpinnerWrapper = styled.div`
  width: 100%;
  text-align: center;

  .components-spinner {
    float: inherit;
    margin: 5px 0;
  }
`;

const StyledSpinner = () => (
  <SpinnerWrapper>
    <Spinner />
  </SpinnerWrapper>
);

export default StyledSpinner;
