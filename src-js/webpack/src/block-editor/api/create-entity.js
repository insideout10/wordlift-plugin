/**
 * This file provides an API to create entities using the WordPress wp-json end-point.
 *
 * In order to work the API looks for the `_wlBlockEditorSettings` global object.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

// Check that our settings are present.
if ("undefined" === typeof global["_wlBlockEditorSettings"]) {
  console.warn("create-entity requires a global `_wlBlockEditorSettings` to be defined.");
}

// Create a reference to the settings.
const settings = global["_wlBlockEditorSettings"];

/**
 * Create an entity.
 *
 * @param {{title, status, excerpt}} value The entity data.
 * @returns {Promise<Response>}
 */
export default value => {
  return fetch(`${settings.root}wp/v2/entities`, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": settings.nonce
    },
    body: JSON.stringify(value)
  }).then(response => response.json());
};
