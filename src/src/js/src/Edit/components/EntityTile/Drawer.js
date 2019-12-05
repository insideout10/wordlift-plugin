/**
 * Components: Drawer.
 *
 * A container for elements that display when the drawer is open.
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
const Drawer = styled.div`
	display: block;
	position: absolute;
	left: 248px;
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 248px;
	min-height: 24px;
	padding: 8px;
	color: #626162;
	transition: left 200ms ease;
	left: ${ props => props.open ? 0 : '248px' };
`;

// Finally export the `Drawer`.
export default Drawer;
