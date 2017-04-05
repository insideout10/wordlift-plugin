/**
 * Components: Navigator Widget App.
 *
 * This is the main entry point for the Navigator Widget.
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
import Tile from '../Tile';

/**
 * Define the {@link App}.
 *
 * @since 3.12.0
 * @return {Function} The `render` function.
 */
const App = ( { data } ) => (
	<Wrapper>
		{ data.map( item => <Tile entity={ item.entity }
								  post={ item.post } /> )}
	</Wrapper>
);

// Finally export the `App`.
export default App;
