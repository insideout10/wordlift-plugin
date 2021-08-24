import styled, {css} from "styled-components";

const Arrow = styled.div`
  width: 0;
  height: 0;

  ${props => css`
    border-top: ${props.height} solid transparent;
    border-bottom: ${props.height} solid transparent;
    border-left: ${props.height} solid ${props.color};
  `};
`;

export default Arrow;
