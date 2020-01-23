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

export default { getMappings };
