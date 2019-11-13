/**
 * Define the selectors.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * WordPress dependencies
 */
import { select } from "@wordpress/data";

export const getAnnotationFilter = state => state.annotationFilter;

export const getEditor = state => state.editor;

export const getEntities = state => state.entities;

export const getSelectedEntities = state =>
  getEntities(state).filter(entity => "undefined" !== typeof entity.occurrences && 0 < entity.occurrences.length);

export const getClassificationBlock = () =>
  select("core/editor")
    .getBlocks()
    .find(block => "wordlift/classification" === block.name);
