/**
 * Applications: Classification Box.
 *
 * This is the main entry point for the Classification Box application published
 * from the `index.js` file inside a Redux `Provider`.
 *
 * @since 3.11.0
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
import Header from '../Header';
import VisibleEntityList from '../../containers/VisibleEntityList';

/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const App = () => (
	<Wrapper className={ style[ 'wl-classification-box-wrapper' ] }>
		<Header />
		<VisibleEntityList />
	</Wrapper>
);

// Finally export the `App`.
export default App;
