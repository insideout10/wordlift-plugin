/**
 * Components: Wrapper.
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
const Wrapper = styled.li`
	display: block;
	position: relative;
	box-sizing: border-box;
	overflow: hidden;
	border-radius: 2px;
	margin: 8px auto;
	width: 248px;
	min-height: 32px;
	padding: 4px 0;
	background-color: #f5f5f5;
	box-shadow: 0 4px 4px -3px rgba(0,0,0,.25), 0 8px 8px -6px rgba(0,0,0,.25);
	transition: all 100ms linear;
	// Prevent dotted outline in FF
	outline: 0;

	&:hover {
		transform: scale( ${ props => 0 < props.entity.occurrences.length ? 1 : 1.01 } ); 
		background-color: ${ props => 0 < props.entity.occurrences.length ? '#f5f5f5' : '#fafafa' }
		// Prevent dotted outline in FF
		outline: 0;
	};

	&:active {
		transform: scale(0.99)
		background-color: #f5f5f5;
		// Prevent dotted outline in FF
		outline: 0;
	};

	&:focus {
		// Prevent dotted outline in FF
		outline: 0;
	}

`;

// Finally export the `Wrapper`.
export default Wrapper;
