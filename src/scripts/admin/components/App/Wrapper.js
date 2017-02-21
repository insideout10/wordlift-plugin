/**
 * Components: Wrapper.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';

/**
 * @inheritDoc
 */
const Wrapper = styled.div`
	background-color: #ffffff;
	padding: 8px 0;
	margin-bottom: 8px;
	// Compensate accordion margin bottom.
	position: relative;
	top: -8px;
`;

// Finally export the `Wrapper`.
export default Wrapper;
