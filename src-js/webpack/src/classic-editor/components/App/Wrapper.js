/**
 * Components: Wrapper.
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
const Wrapper = styled.div`
  background-color: #fff;

  // Compensate accordion margin bottom.
  position: relative;

  // Size.
  max-width: 254px;

  // Fixing position in responsive.
  margin: auto;
  margin-bottom: 8px;
`;

// Finally export the `Wrapper`.
export default Wrapper;
