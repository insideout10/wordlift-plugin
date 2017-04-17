/**
 * Components: Excerpt Wrapper.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';
import { ellipsis } from 'polished';

/**
 * @inheritDoc
 */
const Wrapper = styled.div`
    font-size: 10px;
    line-height: 12px;
    color: #000;
    width: 100%;
    height: 48px;
    margin: 8px 0 0;
    
    ${ ellipsis( '100%' ) }
    
    // Reset the white-space.
    white-space: normal;
`;

// Finally export the `Wrapper`.
export default Wrapper;
