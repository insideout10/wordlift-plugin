import EditComponentMapping from "../mappings/edit-component-mapping";
import MappingComponentHelper from "../components/mapping-component/mapping-component-helper";

/**
 * This file provides the api methods for the edit mappings screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

const { wl_edit_mapping_rest_nonce,  rest_url } = global["wl_edit_mappings_config"];

function getMappingItemByMappingId( mappingId ) {
    const url = rest_url + "/" + mappingId;
    return fetch(url, {
        method: "GET",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": wl_edit_mapping_rest_nonce
        }
    }).then(response => response.json().then(data => data ))
}

function saveMappingItem() {
    const postObject = EditComponentMapping.mapStoreKeysToAPI(this.props.stateObject);
    return fetch(rest_url, {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": wl_edit_mapping_rest_nonce
        },
        body: JSON.stringify(postObject)
    }).then(response => response.json().then(json => json))
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
    }).then(response =>
        response.json().then(data => data))
}

export default { getMappingItemByMappingId, saveMappingItem, getTermsFromAPI }