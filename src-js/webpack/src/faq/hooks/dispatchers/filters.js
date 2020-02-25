/**
 * Filters help to filter the content before dispatching it to the
 * component, it prevents the invalid tags being added to the answer.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Internal dependencies.
 */
import { ANSWER_ALLOWED_HTML_TAGS } from "../../components/faq-edit-item/helpers";

/**
 * Remove an element by tag name
 * @param el {Element}
 * @param tagName {string}
 */
function removeElementByTagName(el, tagName) {
  const occurrences = el.getElementsByTagName(tagName);
  // Remove all occurences by removing it from the parent node.
  for (let i = occurrences.length - 1; i >= 0; i--) {
    occurrences[i].parentNode.removeChild(occurrences[i]);
  }
}

/**
 * Removes the invalid tags from the html automatically before
 * dispatching it to the component.
 * @param htmlString {string} string with html where invalid tags needed to be
 * removed.
 */
export const invalidTagFilter = htmlString => {
  // we create a dummy DOM element with the string.
  const el = document.createElement("div");
  el.innerHTML = htmlString.trim();
  const tags = el.getElementsByTagName("*");
  // Keep track of all invalid tags.
  const invalidTags = [];
  // get all the invalid tags.
  for (let tag of tags) {
    // Check if the tag name is in the valid tag
    const isTagNamePresent = ANSWER_ALLOWED_HTML_TAGS.includes(tag.tagName.toLowerCase());
    if (!isTagNamePresent) {
      invalidTags.push(tag.tagName);
    }
  }
  /**
   * Remove all the invalid tags.
   */
  for (let invalidTag of invalidTags) {
    removeElementByTagName(el, invalidTag);
  }
  return el.innerHTML;
};
