/**
 * Components: Bullet.
 *
 * The {@link Switch} bullet.
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
const Bullet = styled.div`
	cursor: pointer;
	display: inline-block;
	position: absolute;
	top: 2px;
	left: ${ props => props.selected ? 10 : 2 }px;
	transition: left 150ms ease;
	width: 12px;
	height: 12px;
	background: #FFFFFF;
	border-radius: 50%;
`;

// Finally export the `Bullet`.
export default Bullet;
