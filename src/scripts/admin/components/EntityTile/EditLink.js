/**
 * Components: Edit Link.
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
const EditLink = styled.i`
	cursor: pointer;
	display: block;
	position: absolute;
	right: 20px;
	top: 9px;
	width: 16px;
	height: 16px;
	text-align: center;
	line-height: 1;
	background-color: #666;
	color: #fff;
	border-radius: 2px;
	
	&:before {
		position: absolute;
		top: 50%;
		left: 50%;
		margin-top: -7px;
		margin-left: -6px;
		font-size: 14px;
	}
	
	&:hover {
		background-color: #fccd34;
	}
`;

// Finally export the `EditLink`.
export default EditLink;
