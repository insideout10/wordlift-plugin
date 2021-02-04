/**
 * This file provides the api methods for the edit mappings screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * Internal dependencies
 */
import EditComponentFilters from "../filters/edit-component-filters";

const { wl_edit_mapping_rest_nonce, rest_url } = global["wl_edit_mappings_config"];

function getMappingItemByMappingId(mappingId) {
  const url = rest_url + "/" + mappingId;
  return fetch(url, {
    method: "GET",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_edit_mapping_rest_nonce
    }
  }).then(response => response.json().then(data => data));
}

function saveMappingItem(mappingData) {
  const postObject = EditComponentFilters.mapStoreKeysToAPI(mappingData);
  return fetch(rest_url, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_edit_mapping_rest_nonce
    },
    body: JSON.stringify(postObject)
  }).then(response => response.json().then(json => json));
}

function getTermsFromAPI(taxonomy) {
  const postObject = {
    taxonomy: taxonomy
  };
  return fetch(rest_url + "/get_terms", {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_edit_mapping_rest_nonce
    },
    body: JSON.stringify(postObject)
  }).then(response => response.json().then(data => data));
}


function getTaxonomyTermsFromAPI(taxonomy) {
  const postObject = {
    taxonomy: taxonomy
  };
  return fetch(rest_url + "/get_taxonomy_terms", {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": wl_edit_mapping_rest_nonce
    },
    body: JSON.stringify(postObject)
  }).then(response => response.json().then(data => data));
}

  export default { getMappingItemByMappingId, saveMappingItem, getTermsFromAPI, getTaxonomyTermsFromAPI };
