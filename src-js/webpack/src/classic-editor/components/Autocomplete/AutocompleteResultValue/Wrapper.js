import styled from "@emotion/styled";

export default styled.div`
  // Allow elements to line up horizontally.
  display: inline-block;

  // Keep the elements aligned top since the height may vary according
  // to the size of the labels.
  vertical-align: top;

  // Contain child element positioned using absolute.
  position: relative;

  box-sizing: border-box;

  width: 248px;
  min-height: 32px;
  margin: 4px;
  padding: 4px 0;

  overflow: hidden;
  border-radius: 2px;
  background-color: #f5f5f5;
  box-shadow: 0 4px 4px -3px rgba(0, 0, 0, 0.25), 0 8px 8px -6px rgba(0, 0, 0, 0.25);
  outline: 0;

  color: #666;

  * {
    display: inline-block;
    vertical-align: top;
  }
`;
