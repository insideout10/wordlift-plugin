/**
 * Components: Thumbnail Wrapper.
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
const Wrapper = styled.div`
    margin: 0;
    width: 100%;
    display: block;
    box-sizing: border-box;
    
    // Positioning the text.
    line-height: 24px;
    padding: 0;
    
    // Styling the text.
    font-size: 14px;
    text-decoration: underline;
    font-family: 'Droid Serif', serif;
    
    // Coloring the header.
    color: #fff;    
`;

// Finally export the `Wrapper`.
export default Wrapper;
