/**
 * Components: Location Wrapper Component.
 *
 * @since 3.15.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';

/**
 * @inheritDoc
 */
const Wrapper = styled.div`
	width: 100%;
	padding: 8px 0;

	// Compensate accordion margin bottom.
	position: relative;
	top: -8px;

	// Size.
	max-width: 254px;

	// Fixing position in responsive.
	margin: auto;
	margin-bottom: 8px;
	
	text-align: center;
	
	// Ensure the children display one per line.
	* {
		display: block;
	}
`;

// Finally export the `Wrapper`.
export default Wrapper;
