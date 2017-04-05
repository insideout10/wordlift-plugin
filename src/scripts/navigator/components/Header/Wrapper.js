/**
 * Components: Header Wrapper.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';

// Define the header backgrounds.
const BACKGROUNDS = {
	what: '#2e92ff',
	who: '#bd10e0',
	where: '#7ed321',
	when: '#f7941d',
};

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
    padding: 0 8px;
    
    // Styling the text.
    font-size: 14px;
    text-decoration: underline;
    font-family: 'Droid Serif', serif;
    
    // Coloring the header.
    color: #fff;    
    background-color: ${ props => BACKGROUNDS[ props.relation ] };
`;

// Finally export the `Wrapper`.
export default Wrapper;
