/**
 * Components: Label.
 *
 * The {@link Switch} label.
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
const Label = styled.div`
	display: inline-block;
	position: relative;
	top: -4px;
	line-height: 16px;
	font-size: 12px;
	margin: 0 12px 0 4px;
	transition: opacity 150ms ease;
	opacity: ${ props => props.selected ? 1 : 0.5 };
	
	&:after {
		content: ' ';
		display: inline-block;
		position: absolute;
		right: -12px;
		top: 6px;
		width: 4px;
		height: 4px;
		border-radius: 50%;
		background-color: #626162;
		margin-right: 4px;
	}
`;

// Finally export the `Label`.
export default Label;
