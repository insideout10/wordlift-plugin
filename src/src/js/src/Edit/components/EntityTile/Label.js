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
	max-width: 180px;
	margin-top: 4px;
	min-height: 16px;
	line-height: 16px;
	font-weight: 600;
	font-size: 12px;
	user-select: none;
	hyphens: auto;
	color: ${ props => 0 < props.entity.occurrences.length ? '#2e92ff' : '#666' };
`;

// Finally export the `Label`.
export default Label;
