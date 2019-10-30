/**
 * This file provides helper functions.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * Check whether the provided HTMLElement is an annotation.
 *
 * An {@link HTMLElement} is considered an annotation if it satisfies the following
 * requirements:
 *  - it has a `span` tagName.
 *  - it has an `id` attribute.
 *  - it has a `textannotation` class name.
 *
 * @since 3.23.0
 * @param {HTMLElement} el The {@link HTMLElement}.
 * @returns {boolean} True if it's annotation span otherwise false.
 */
export const isAnnotationElement = el => {
  return (
    "undefined" !== typeof el &&
    "undefined" !== typeof el.tagName &&
    "undefined" !== typeof el.id &&
    "undefined" !== typeof el.classList &&
    "SPAN" === el.tagName &&
    el.classList.contains("textannotation")
  );
};
