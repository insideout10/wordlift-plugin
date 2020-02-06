/**
 * This file provides the functions to make API calls for FAQ.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */
const { restUrl, nonce } = global["_wlFaqSettings"];

function saveFAQItems() {
  fetch(restUrl, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    }
  })
    .then(response => response.json())
    .then(json => json);
}


export default { saveFAQItems }