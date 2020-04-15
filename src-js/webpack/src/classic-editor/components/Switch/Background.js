/**
 * Components: Background.
 *
 * The {@link Switch} background.
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
const Background = styled.div`
	display: inline-block;
	position: relative;
	box-sizing: border-box;
	width: 24px;
	height: 16px;
	background: ${ props => props.selected ? '#7ed321' : '#c7c7c7' };
	transition: background 200ms ease;
	border-radius: 10px;
`;

// Finally export the `Background`.
export default Background;
