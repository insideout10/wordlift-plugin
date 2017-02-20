/**
 * Components: Label.
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
	box-sizing: border-box;
	max-width: 200px;
	height: 32px;
	line-height: 32px;
	font-weight: 600;
	font-size: 12px;
	user-select: none;
	color: ${ props => 0 < props.entity.occurrences.length ? '#2e92ff' : '#c7c7c7' };
`;

// Finally export the `Label`.
export default Label;
