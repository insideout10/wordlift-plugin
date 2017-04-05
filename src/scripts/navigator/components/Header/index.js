/**
 * Components: Tile Header.
 *
 * The Tile Header contains the entity label and link to the entity.
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
const Header = ( { entity } ) => (
	<Wrapper relation={ entity.relation }>
		<a href={ entity.permalink }>{ entity.label }</a>
	</Wrapper>
);

// Finally export the `Header`.
export default Header;
