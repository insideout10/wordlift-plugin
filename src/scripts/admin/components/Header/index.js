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
import Wrapper from './Wrapper';
import EntityFilter from '../../containers/EntityFilter';

/**
 * Define the `Header` with the entity filters.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const Header = () => (
	<Wrapper>
		<EntityFilter filter="SHOW_WHAT">what</EntityFilter>
		<EntityFilter filter="SHOW_WHERE">where</EntityFilter>
		<EntityFilter filter="SHOW_WHEN">when</EntityFilter>
		<EntityFilter filter="SHOW_WHO">who</EntityFilter>
		<EntityFilter filter="SHOW_ALL">all</EntityFilter>
	</Wrapper>
);

// Finally export the `Header`.
export default Header;
