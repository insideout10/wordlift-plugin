/**
 * Components: Count.
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
const Count = styled.div`
	display: inline-block;
	position: relative;
	margin: 4px 8px;
	width: 16px;
	height: 16px;
	border-radius: 2px;
	padding: 2px 0;
	text-align: center;
	vertical-align: top;
	font-weight: 600;
	font-size: 12px;
	color: #FFFFFF;
	letter-spacing: -0.21px;
	line-height: 12px;
	user-select: none;
	background-color: ${ props => 0 < props.entity.occurrences.length ? '#2e92ff' : '#c7c7c7' };
`;

export default Count;
