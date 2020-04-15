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
const MainType = styled.div`
  display: ${ props => props.entity.duplicateLabel ? 'inline-block' : 'none' };
	margin-left: 2px;
	position: relative;
	font-weight: 300;
	font-size: 10px;
`;

// Finally export the `MainType`.
export default MainType;
