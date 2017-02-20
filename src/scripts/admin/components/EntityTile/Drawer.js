/**
 * Created by david on 20/02/2017.
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
	height: 32px;
	padding: 8px;
	color: #626162;
	transition: left 200ms ease;
	left: ${ props => props.open ? 0 : '248px' };
`;

// Finally export the `Drawer`.
export default Drawer;
