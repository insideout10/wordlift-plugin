/**
 * Components: Font Awesome.
 *
 * A Font Awesome element.
 *
 * @since 1.0.0
 */
import styled from "@emotion/styled";

export default styled.i`
  margin: 4px 8px;
  font-size: 14px;

  // ### ANY alignment.	
  ${(props) =>
    "right" !== props.align &&
    `
    &:hover {
      cursor: pointer;
      color: #2e92ff;
    }
  `}
  
	// ### RIGHT alignment.
	${(props) =>
    "right" === props.align &&
    `
    position: absolute;
    right: 0;
    top: 4px;
    
    // Elements on the right are indicators and aren't clickable.
    color: #cbcbcb;
  `}

`;
