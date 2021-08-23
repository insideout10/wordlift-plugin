/**
 * Get all entities with occurrences.
 */
export const getAllSelectedEntities = (state) => state.entities
    .filter( e => e.occurrences.length > 0)