/**
 * Components: Cloud.
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
const Cloud = styled.i`
	display: ${ props => props.local ? 'block' : 'none' };
	position: absolute;
	right: 20px;
	top: 8px;
	font-size: 14px;
	line-height: 1;
	color: #CBCBCB;
	user-select: none;
	transition: opacity 150ms ease;
`;

// Finally export the `Cloud`.
export default Cloud;
