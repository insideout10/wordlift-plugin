/**
 * Components: Main.
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
const Main = styled.div`
	cursor: pointer;
	display: block;
	position: absolute;
	left: ${ props => props.open ? '-248px' : 0 };
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 248px;
	height: 32px;
	transition: left 200ms ease;
`;

// Finally export `Main`.
export default Main;
