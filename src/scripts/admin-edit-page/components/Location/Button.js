/**
 * Components: Get Current Location Component.
 *
 * @since 3.15.0
 */

/**
 * External dependencies
 */
import styled from 'styled-components';

/**
 * @inheritDoc
 */
const Button = styled.button`
	margin: 0 auto;
	display: ${ props => props.geoLocation ? 'block' : 'none' };
`;

// Finally export the `Button`.
export default Button;
