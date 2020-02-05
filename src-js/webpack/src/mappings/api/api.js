/**
 * This file provides the APIs.
 *
 * @since 3.25.0
 */

/**
 * Internal dependencies
 */
import MappingComponentHelper from "../components/mapping-component/mapping-component-helper";

const { rest_url, wl_mapping_nonce } = global["wlMappingsConfig"];

/**
 * Gets the mappings and apply ui filters to it, the filters add the state keys ( like if the mapping
 * item is already selected by the user) to the mapping items.
 * @returns {Promise<{isSelected: boolean}[]>}
 */
function getMappings() {
  return fetch(rest_url, {
    method: "GET",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_mapping_nonce
    }
  })
    .then(response => response.json())
    .then(json => MappingComponentHelper.applyUiItemFilters(json));
}

/**
 * Delete or update the mapping items from the API.
 * Update includes the change in category such as active, trash etc.
 * @param {String} type Type of the request
 * @param {Array} mappingItems List of mapping items which is sent to api after applying the api filter.
 * @returns {Promise<Response>}
 */
function deleteOrUpdateMappings(type, mappingItems) {
  return fetch(rest_url, {
    method: type,
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_mapping_nonce
    },
    body: JSON.stringify({
      mapping_items: MappingComponentHelper.applyApiFilters(mappingItems)
    })
  });
}

/**
 * Clone the mapping items passed in to it
 * @param mappingItems List of mapping items which needs to be cloned.
 * @returns {Promise<unknown>}
 */
function cloneMappings(mappingItems) {
  return fetch(rest_url + "/clone", {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_mapping_nonce
    },
    body: JSON.stringify({ mapping_items: MappingComponentHelper.applyApiFilters(mappingItems) })
  });
}

export default { getMappings, deleteOrUpdateMappings, cloneMappings };
