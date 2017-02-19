/**
 * Applications: Classification Box.
 *
 * This is the main entry point for the Classification Box application published
 * from the `index.js` file inside a Redux `Provider`.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Header from '../Header';
import VisibleEntityList from '../../containers/VisibleEntityList';

/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const App = () => (
	<div>
		<Header />
		<VisibleEntityList />
	</div>
);

// Finally export the `App`.
export default App;
