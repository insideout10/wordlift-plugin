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
import List from './List';
import EntityTile from '../EntityTile';

/**
 * The `EntityList` component.
 *
 * @since 3.11.0
 *
 * @param {Object} entities A map of entities, keyed by their id.
 * @param {Function} onClick The click handler.
 * @param {Function} onLinkClick Handler for the link/no link click.
 * @param {Function} onEditClick Handler for the edit click.
 * @constructor
 */
const EntityList = ( { entities, onClick, onLinkClick, onEditClick } ) => (
	<List>
		{
			// Map each entity to an `EntityTile`.
			entities.map( entity =>
							  <EntityTile entity={ entity }
										  tile={ { open: false } }
										  onClick={ onClick }
										  onEditClick={ onEditClick }
										  onLinkClick={ onLinkClick } />
			)
		}
	</List>
);

// Finally export the `EntityList`.
export default EntityList;
