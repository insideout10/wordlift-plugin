/**
 * This file defines constants used across different files and components.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * WordPress' action hook to signal that a selection has changed.
 *
 * @since 3.23.0
 * @type {string}
 */
export const SELECTION_CHANGED = "wordlift.selectionChanged";

/**
 * WordPress' action hook to signal that an annotation has changed. The action
 * provides the annotation id as `{ annotationId }`. The annotation id usually
 * matches the element id that caused the action to be fired.
 *
 * @since 3.23.0
 * @type {string}
 */
export const ANNOTATION_CHANGED = "wordlift.annotationChanged";
