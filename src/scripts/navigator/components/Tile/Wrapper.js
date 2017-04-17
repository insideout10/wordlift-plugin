/**
 * Components: Navigator Tile Wrapper.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';

/**
 * @inheritDoc
 */
const Wrapper = styled.li`
    display: inline-block;
    list-style: none;
    vertical-align: top;
    margin: 8px 2px;
    width: 136px;
    text-align: initial;
        
	a {
		text-decoration: none;
		border-bottom: 0;
	}
`;

// Finally export the `Wrapper`.
export default Wrapper;
