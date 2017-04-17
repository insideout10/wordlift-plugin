/**
 * Components: Navigator Widget App.
 *
 * This is the main entry point for the Navigator Widget.
 *
 * @since 3.12.0
 */

/**
 * Styles.
 *
 * The following style must be loaded as first in order to give precedence to
 * styled-components (which are loaded after).
 */
import * as style from './style.scss';

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
	<Wrapper className={ style[ 'wl-navigator-wrapper' ] }>
		{ data.map( item => <Tile entity={ item.entity }
								  post={ item.post } /> )}
	</Wrapper>
);

// Finally export the `App`.
export default App;
