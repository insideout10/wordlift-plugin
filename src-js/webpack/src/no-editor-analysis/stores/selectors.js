/**
 * This file defines the selectors.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.4
 */

/**
 * Get an entity given its item id.
 */
export const getEntity = (state, id) => state.entities.get(id);
