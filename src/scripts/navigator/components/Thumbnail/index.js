/**
 * Components: Thumbnail.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import CoverImage from '../CoverImage';

/**
 * Define the {@link App}.
 *
 * @since 3.12.0
 * @return {Function} The `render` function.
 */
const Thumbnail = ( { source } ) => (
	<Wrapper>
		<CoverImage width="100%" height="120px" source={ source } />
	</Wrapper>
);

// Finally export the `Thumbnail`.
export default Thumbnail;
