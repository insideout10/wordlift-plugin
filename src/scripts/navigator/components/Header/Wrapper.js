/**
 * Components: Header Wrapper.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';
import { ellipsis, parseToHsl } from 'polished';

// Define the colors.
const RELATIONS = {
	what: {
		background: '#2e92ff',
		color: 0.5 < parseToHsl( '#2e92ff' ).lightness ? '#fff' : '#000'
	},
	who: {
		background: '#bd10e0',
		color: 0.5 < parseToHsl( '#bd10e0' ).lightness ? '#fff' : '#000'
	},
	where: {
		background: '#7ed321',
		color: 0.5 < parseToHsl( '#7ed321' ).lightness ? '#fff' : '#000'
	},
	when: {
		background: '#f7941d',
		color: 0.5 < parseToHsl( '#f7941d' ).lightness ? '#fff' : '#000'
	},
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
    font-size: 12px;
    font-weight: 700;
    
    // Coloring the header.
   	color: ${ props => RELATIONS[ props.relation ].color };
    background-color: ${ props => RELATIONS[ props.relation ].background };
   
    a {
    	color: ${ props => RELATIONS[ props.relation ].color };
    	text-decoration: none;
    	// Some style define the underline using border-bottom.
    	border-bottom: 0;
    }  
    
    ${ ellipsis() }
`;

// Finally export the `Wrapper`.
export default Wrapper;
