/**
 * Components: Arrow.
 *
 * The `Arrow` component is an arrow on the tile right side which opens/closes
 * the Drawer when clicked.
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
const Arrow = styled.div`
	display: block;
	position: absolute;
	top: 8px;
	width: 8px;
	height: 8px;
	border-top: 8px solid transparent;
	border-bottom: 8px solid transparent;
	border-left: 8px solid #7d7d7d;
	border-radius: 16px;
	transition: transform 150ms ease;
	transform: rotate( ${ props => props.open ? 180 : 0 }deg );
	&:hover {
		border-left-color: #fccd34;
	} 
`;

// Finally export the `Arrow.
export default Arrow;
