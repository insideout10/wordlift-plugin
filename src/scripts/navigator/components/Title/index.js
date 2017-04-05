/**
 * Components: Post Title.
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

/**
 * Define the {@link App}.
 *
 * @since 3.12.0
 * @return {Function} The `render` function.
 */
const Title = ( { link, children } ) => (
	<Wrapper>
		<a href={ link }>{ children }</a>
	</Wrapper>
);

// Finally export the `Title`.
export default Title;
