/**
 * Components: Count.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import styled from "@emotion/styled";

/**
 * @inheritDoc
 */
const Count = styled.div`
  display: inline-block;
  position: relative;
  margin: 4px 8px;
  min-width: 12px;
  height: 12px;
  border-radius: 2px;
  padding: 2px;
  text-align: center;
  vertical-align: top;
  font-weight: 500;
  font-size: 12px;
  color: #ffffff;
  letter-spacing: -1px;
  // Setting line-height to 10p (2px less than the font-size) helps keeping it in the middle.
  line-height: 10px;
  user-select: none;
  background-color: ${(props) => (0 < props.entity.occurrences.length ? "#2e92ff" : "#c7c7c7")};
`;

export default Count;
