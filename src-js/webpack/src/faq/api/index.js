/**
 * This file provides the functions to make API calls for FAQ.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

/**
 * Internal dependencies.
 */
import { transformUiDataToApiFormat, transformUiDataToDeleteApiFormat } from "../sagas/filters";

function saveFAQItems(faqItems) {
  const { restUrl, nonce, postId } = global["_wlFaqSettings"];
  return fetch(restUrl, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    },
    body: JSON.stringify({
      post_id: postId,
      faq_items: faqItems
    })
  })
    .then(response => response.json())
    .then(json => json);
}

function updateFAQItems(faqItems) {
  const { restUrl, nonce, postId } = global["_wlFaqSettings"];
  return fetch(restUrl, {
    method: "PUT",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    },
    body: JSON.stringify({
      post_id: postId,
      faq_items: transformUiDataToApiFormat(faqItems)
    })
  })
    .then(response => response.json())
    .then(json => json);
}

function getFAQItems() {
  const { restUrl, nonce, postId } = global["_wlFaqSettings"];
  return fetch(restUrl + "/" + postId, {
    method: "GET",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    }
  })
    .then(response => response.json())
    .then(json => json);
}

/**
 * Delete the faq items.
 */
function deleteFaqItems(faqItems) {
  const { restUrl, nonce, postId } = global["_wlFaqSettings"];
  return fetch(restUrl, {
    method: "DELETE",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    },
    body: JSON.stringify({
      post_id: postId,
      faq_items: transformUiDataToDeleteApiFormat(faqItems)
    })
  })
    .then(response => response.json())
    .then(json => json);
}
export default { saveFAQItems, getFAQItems, updateFAQItems, deleteFaqItems };
