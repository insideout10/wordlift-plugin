/**
 * This file provides the functions to make API calls for FAQ.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */
const { restUrl, nonce, postId } = global["_wlFaqSettings"];

function saveFAQItems(faqItems) {
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
  });
}

function getFAQItems() {
  return fetch(restUrl + "?post_id=" + postId, {
    method: "GET",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    }
  })
    .then(response => response.json())
    .then(json => json);
}

export default { saveFAQItems };
