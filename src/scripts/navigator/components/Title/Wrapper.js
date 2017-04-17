/**
 * Components: Navigator Tile Wrapper.
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
    font-size: 12px;
    font-weight: 800;
    line-height: 16px;
    text-decoration: none;
    color: #000;
    display: block;
    width: 100%;
    height: 48x;
    margin: 8px 0 0;
    
    a {
    	${ ellipsis() }
    }
`;

// Finally export the `Wrapper`.
export default Wrapper;
