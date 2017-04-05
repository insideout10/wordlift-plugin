/**
 * Components: Cover Image.
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
const CoverImage = styled.div`
	width: ${ props => props.width };
	height: ${ props => props.height };
	background-image: url( ${ props => props.source } );
    background-size: cover;
    background-position: center;
`;

// Finally export the `CoverImage`.
export default CoverImage;
