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
	
	margin: 0 0 20px 0;
	padding: 0;
    text-align: center;
`;

// Finally export the `Wrapper`.
export default Wrapper;
