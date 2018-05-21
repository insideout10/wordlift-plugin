import styled, { css } from "styled-components";
import { darken } from "polished";

const Wrapper = styled.div`
    white-space: initial;
    line-height: 14px;
    color: white;
    padding: 10px;
    margin-bottom: 10px;
    font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        
    > div {
        display: inline-block;
        
        &:last-child {
            float: right;
        }
    }

    ${props =>
      props.enabled &&
      css`
        background-color: #007aff;
        cursor: pointer;

        &:hover {
          box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
          transform: scale(1.01);
          transition: all 200ms ease-out;
        }

        &:active {
          background-color: ${darken(0.02, "#007aff")};
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
          transform: scale(1);
        }
      `}
    
    ${props =>
      !props.enabled &&
      css`
        background-color: #cbcbcb;
        cursor: initial;
      `}
       
}
`;

export default Wrapper;
