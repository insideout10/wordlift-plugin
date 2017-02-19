/**
 * Components: the classification box header.
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
import EntityFilter from '../../containers/EntityFilter';

/**
 * Define the `Header` with the entity filters.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const Header = () => (
	<div>
		<EntityFilter filter="SHOW_WHAT">What</EntityFilter>
		<EntityFilter filter="SHOW_WHERE">Where</EntityFilter>
		<EntityFilter filter="SHOW_WHEN">When</EntityFilter>
		<EntityFilter filter="SHOW_WHO">Who</EntityFilter>
		<EntityFilter filter="SHOW_ALL">All</EntityFilter>
	</div>
);

// Finally export the `Header`.
export default Header;
