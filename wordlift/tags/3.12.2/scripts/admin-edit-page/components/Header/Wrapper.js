/**
 * Components: Wrapper component.
 *
 * The Wrapper component applies styling to its children in order to make them
 * event.
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
const Wrapper = styled.div`
	position: relative;
	margin: auto;
	max-width: 248px;
	border-radius: 2px;
	border: 1px solid #666;
	overflow: hidden;

	* {
		box-sizing: border-box;
		display: inline-block;
		width: 20%;
		border-right: 1px solid #666;
		color: #666;
		text-align: center;
		text-decoration: none;
		
		&:hover {
			color: #a0a0a0;
		}

		&:focus {
			// Overrides WordPress' styles.
			box-shadow: none
		}
	}

	*:last-child {
		border-right: none;

		// Fix annoying pixel gap in responsive.
		&.wl-active {
			box-shadow: 3px 0 0 0 #666;
		}
	}
	
	*.wl-active {
		background: #666;
		color: #fff;
	}
`;

// Finally export the `Wrapper`.
export default Wrapper;
