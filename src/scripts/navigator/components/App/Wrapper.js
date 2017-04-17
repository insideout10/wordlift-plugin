/**
 * Components: Excerpt Wrapper.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';
import { normalize } from 'polished';

/**
 * @inheritDoc
 */
const Wrapper = styled.ul`
	${ normalize() }
	
	margin-left: 0;
	padding-left: 0;
    text-align: center;
`;

// Finally export the `Wrapper`.
export default Wrapper;
