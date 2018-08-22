import styled from "styled-components";

const Wrapper = styled.ul`
  background: white;
  // border-top: 0;
  // border-bottom: 1px solid #007aff;
  // border-left: 1px solid #007aff;
  // border-right: 1px solid #007aff;
  border: 1px solid #007aff;
  box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  max-height: 280px;
  overflow-y: scroll;
  font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu,
    Cantarell, "Helvetica Neue", sans-serif;
  line-height: 20px;
  font-size: 14px;
  position: absolute;
  // width: calc(100% - 1px);
  // margin-left: 1px;
  width: 110%;
  margin-left: calc( -5% + 1px );
  overflow-x: hidden;
  z-index: 1001;

  display: ${props => (props.open ? "block" : "none")};

  > li {
    cursor: pointer;
    margin-bottom: 0;
    padding-bottom: 4px;

    &:hover {
      background-color: rgba(46, 146, 255, 0.2);
    }
  }
`;

export default Wrapper;
