/**
 * Highlight Helper class helps to highlight the given
 * html with the given inline tag and also removes them
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import { faqEditItemType } from "../../components/faq-edit-item";
import { FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME } from "../custom-faq-elements";

export default class HighlightHelper {
  /**
   *
   * @param html Source HTML string
   * @param tagName Tag name which is used for highlighting
   * @param className {string} Class Name to select the highlighting tags.
   * @return {string} HTML string with highlighting tags applied.
   */
  static highlightHTML(html, tagName, className) {
    const el = document.createElement("div");
    el.innerHTML = html;
    HighlightHelper.highlightNodes(el, tagName, className);
    return el.innerHTML;
  }

  /**
   * Remove the highlighting tags in the html by tagName and className
   * @param html Source HTML string
   * @param tagName Tag name which is used for highlighting
   * @param className Class Name to select the highlighting tags,
   * @return {string} HTML string with highlighting removed.
   */
  static removeHighlightingTagsByClassName(html, tagName, className) {
    const el = document.createElement("div");
    el.innerHTML = html;
    const highlightingTags = el.querySelectorAll(`${tagName}[class="${className}"]`);
    for (let tag of highlightingTags) {
      /**
       * There might be multiple child nodes inside, so replace all the elements.
       */
      /** Note: childNodes return a live nodelist, we convert it to array before removing it **/
      const childNodes = Array.prototype.slice.call(tag.childNodes);
      for (let node of childNodes) {
        tag.parentElement?.insertBefore(node, tag);
      }
      tag.remove();
    }
    return el.innerHTML;
  }

  static highlightNodesByRange(nodes, tagName, className, range) {
    for (let element of nodes) {
      if (element.childNodes.length === 0 && element.nodeType === Node.TEXT_NODE && element.textContent.trim() !== "") {
        if (range.intersectsNode(element)) {
          // Only highlight if the node intersects the range.
          const newChild = document.createElement(tagName);
          newChild.classList = [className];
          newChild.textContent = element.textContent;
          element.parentElement.replaceChild(newChild, element);
        }
      } else {
        // Check if the element is intersecting the range, otherwise there is no
        // point in recursion
        if (range.intersectsNode(element)) {
          HighlightHelper.highlightNodesByRange(element.childNodes, tagName, className, range);
        }
      }
    }
  }

  /**
   * This function renders the element with the source html, recursively walks the
   * DOM and replaces all the text nodes with the highlighting tags, it returns the html
   * with all text nodes replaced by our highlighting nodes.
   * @param el {HTMLElement} Html element at which the highlighting should be applied.
   * @param tagName {string} Tag name of the highlighting tag.
   * @param className {string} Class name to apply on the highlighting tag
   * @return {null}
   */
  static highlightNodes(el, tagName, className) {
    for (let element of el.childNodes) {
      if (element.childNodes.length === 0 && element.nodeType === Node.TEXT_NODE && element.textContent.trim() !== "") {
        const newChild = document.createElement(tagName);
        newChild.classList = [className];
        newChild.textContent = element.textContent;
        element.parentElement.replaceChild(newChild, element);
      } else {
        HighlightHelper.highlightNodes(element, tagName, className);
      }
    }
  }

  /**
   * Remove the highlighting based on the type, ie whether it is a question or
   * answer
   * @param id {string} Faq item id
   * @param type {string} Question or answer
   * @param html {string} string with highlighting tags
   * @return {string} Html string with highlighting tags removed.
   */
  static removeHighlightingBasedOnType(id, type, html) {
    if (type.toLowerCase() === faqEditItemType.QUESTION.toLowerCase()) {
      // If the question is deleted, then the answer is also deleted currently
      // so we need to remove the answer, question highlight tags.
      html = HighlightHelper.removeHighlightingTagsByClassName(html, FAQ_QUESTION_TAG_NAME, id);
      html = HighlightHelper.removeHighlightingTagsByClassName(html, FAQ_ANSWER_TAG_NAME, id);
    } else if (type.toLowerCase() === faqEditItemType.ANSWER.toLowerCase()) {
      // If only the answer is deleted, we dont need to remove the question tag, because
      // question would be present even if the answer is not present.
      html = HighlightHelper.removeHighlightingTagsByClassName(html, FAQ_ANSWER_TAG_NAME, id);
    }
    return html;
  }
}
