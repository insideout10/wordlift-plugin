/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import EntityTile from '../components/EntityTile';

const EntityList = ( { entities, onClick } ) => (
	<ul>
		{
			Object.keys( entities )
				  .map( ( key ) =>
							<EntityTile entity={ entities[ key ] }
										onClick={ onClick } />
				  )
		}
	</ul>
);

export default EntityList;
