/**
 * Created by david on 19/02/2017.
 */

import styled from 'styled-components';

const Wrapper = styled.div`
    border-radius: 2px;
    border: 1px solid #c6c6c6;
    
	* {
		box-sizing: border-box;
		display: inline-block;
		width: 20%;
		border-right: 1px solid #c6c6c6;
		color: #c6c6c6;
		text-align: center;
		text-decoration: none;
		
	}
	
	*:last-child {
		border-right: none;
	}
	
	*.active {
		background: #c6c6c6;
		color: #fff;
	}
`;

export default Wrapper;
