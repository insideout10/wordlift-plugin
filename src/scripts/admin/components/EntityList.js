/**
 * Components: Entity List.
 *
 * The `EntityList` components is a list of `EntityTile`s. The `EntityList`
 * feeds each `EntityTile` with the `entity` and with the `onClick` handler.
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
import EntityTile from '../components/EntityTile';

/**
 * The `EntityList` component.
 *
 * @since 3.11.0
 * @param {object} entities A map of entities, keyed by their id.
 * @param {function} onClick The click handler.
 * @constructor
 */
const EntityList = ( { entities, onClick } ) => (
	<ul>
		{
			// Map each entity to an `EntityTile`.
			entities.map( entity =>
							  <EntityTile entity={ entity }
										  tile={ { open: false } }
										  onClick={ onClick } />
			)
		}
	</ul>
);

// Finally export the `EntityList`.
export default EntityList;
