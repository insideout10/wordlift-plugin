/**
 * Components: Wrapper component.
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
	cursor: pointer;
	transition: opacity 150ms ease;
	position: absolute;
	right: 0;
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 16px;
	min-height: 32px;
	padding: 8px 4px;
	background-color: #f1f1f1;
	
	// Control the visibility of the element according to the 'show' property.
	display: ${ props => props.show ? 'block' : 'none' };
	opacity: ${ props => props.show ? 1 : 0 }
`;

// Finally export the `Wrapper`.
export default Wrapper;
