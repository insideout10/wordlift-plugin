/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 * This file defines the selectors.
 */

/**
 * Get an entity given its item id.
 */
export const getEntity = (state, id) => state.entities.get(id);

/**
 * Get all entities with occurrences.
 */
export const getAllSelectedEntities = (state) => state.entities
    .filter( e => e.occurrences && e.occurrences.length > 0)