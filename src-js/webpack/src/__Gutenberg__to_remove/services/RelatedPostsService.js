/* globals wp, wordlift, wlSettings */
/**
 * Services: Related Posts Service.
 *
 * A service which fetches related posts and keeps it in Store
 *
 * @since 3.2x
 */

/*
 * Internal dependencies.
 */
import { relatedPostsUpdate } from "../actions";

/**
 * Define the `RelatedPostsService` class.
 *
 * @since 3.2x
 */
class RelatedPostsService {
  getPosts() {
    return dispatch => {
      console.log(`Requesting related posts for post_id ${wlSettings["post_id"]}...`);
      wp.apiFetch({
        url: `${wlSettings["ajax_url"]}?action=wordlift_related_posts&post_id=${wlSettings["post_id"]}`,
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(Object.keys(wordlift.entities))
      }).then(response => {
        console.log(`Related posts have been fetched for post_id ${wlSettings["post_id"]}`);
        dispatch(relatedPostsUpdate(response));
      });
    };
  }
}

export default RelatedPostsService;
