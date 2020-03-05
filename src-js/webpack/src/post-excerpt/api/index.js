/**
 * This file provides the functions to make API calls for Post excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

/**
 * Internal dependencies.
 */

function getPostExcerpt(postBody) {
  const { restUrl, nonce, postId } = global["_wlPostExcerptSettings"];
  return fetch(restUrl + "/" + postId, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": nonce
    },
    body: JSON.stringify({
      post_id: postId,
      post_body: postBody
    })
  })
    .then(response => response.json())
    .then(json => json);
}


export default getPostExcerpt