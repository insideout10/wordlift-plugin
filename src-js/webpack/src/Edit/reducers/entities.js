/*global wlSettings*/
/**
 * Reducers: Entities.
 *
 * Define the reducers related to entities.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import { Map } from "immutable";
/**
 * Internal dependencies
 */
import * as types from "../constants/ActionTypes";
import { TOGGLE_LINK_SUCCESS } from "../constants/ActionTypes";
import LinkService from "../services/LinkService";
import WsService from "../services/WsService";

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const entities = function(state = Map(), action) {
  switch (action.type) {
    case types.ADD_ENTITY:
      return state.merge(Map({ [action.payload.id]: action.payload }));
    // Legacy: receive analysis' results.
    case types.RECEIVE_ANALYSIS_RESULTS:
      // Calculate the labels.
      const labels = Map(action.results.entities).groupBy((v, k) => v.label);

      // Return a new map of the received entities. The legacy Angular
      // app doesn't set the `link` property on the entity, therefore we
      // preset it here according to the `occurrences` settings.
      return (
        Map(action.results.entities)
          .map(x =>
            Object.assign({}, x, {
              link: LinkService.getLink(x.occurrences),
              local: 0 === x.id.indexOf(wlSettings["datasetUri"]),
              w: WsService.getW(x),
              edit: "no" !== wlSettings["can_create_entities"],
              duplicateLabel: 1 < labels.get(x.label).count()
            })
          )
          // Sort by (1) local/not local, (2) confidence, (3) number of annotations.
          .sort((x, y) => {
            // First the local entities.
            if (x.local !== y.local) return y.local - x.local;

            // Get the delta confidence.
            const delta = y.confidence - x.confidence;

            // If the confidence is equal, sort by number of annotations.
            return 0 !== delta ? delta : y.annotations.length - x.annotations.length;
          })
          // Set the shortlist flag to true for the first 20.
          .mapEntries(([k, v], i) => {
            v.shortlist = i < 20;
            return [k, v];
          })
        //          // Then resort them by label.
        //          .sortBy( x => x.label.toLowerCase() )
      );

    case TOGGLE_LINK_SUCCESS:
      const { id, link } = action.payload;

      return state.set(
        id,
        // A new object instance with the existing props and the new
        // occurrences.
        Object.assign({}, state.get(id), { link })
      );

    // Update the entity's occurrences. This action is dispatched following
    // a legacy Angular event. The event is configured in the admin/index.js
    // app.
    case types.UPDATE_OCCURRENCES_FOR_ENTITY:
      // Update the entity.
      return state.set(
        action.entityId,
        // A new object instance with the existing props and the new
        // occurrences.
        Object.assign({}, state.get(action.entityId), {
          occurrences: action.occurrences,
          link: LinkService.getLink(action.occurrences)
        })
      );

    default:
      return state;
  }
};

// Finally export the reducer.
export default entities;
