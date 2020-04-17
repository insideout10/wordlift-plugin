import { css } from "@emotion/core";
import styled from "@emotion/styled";

const Wrapper = styled.div`
    white-space: initial;
    line-height: 16px;
    color: white;
    padding: 11px 8px 6px;
    min-height: 40px;
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

    ${(props) =>
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
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
          transform: scale(1);
        }
      `}
    
    ${(props) =>
      !props.enabled &&
      css`
        background-color: #cbcbcb;
        cursor: initial;
      `}
       
}
`;

export default Wrapper;
