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
 * External dependencies
 */
import styled from 'styled-components';

const ClassDrawer = styled.div`
	background-color: #ffffff;
	padding: 8px 0;
	margin-bottom: 8px;
	// Compensate accordion margin bottom.
	position: relative;
	top: -8px;
`;

/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const App = () => (
	<ClassDrawer>
		<Header />
		<VisibleEntityList />
	</ClassDrawer>
);

// Finally export the `App`.
export default App;
