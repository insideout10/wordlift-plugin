/**
 * This file provides a TinyMCE plugin for integration with WordLift.
 *
 * TinyMCE is loaded in different places within WordPress. We're specifically
 * targeting TinyMCE used as editor in Gutenberg's `classic` block.
 *
 * We're aiming to send an `action` every time the text selection changes. The
 * action should be caught by other components in page to update the UI (namely
 * the `Add ...` button in the classification box.
 *
 * The plugin name `wl_tinymce_2` is also defined in
 * src/includes/class-wordlift-tinymce-adapter.php and *must* match.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * WordPress dependencies
 */
import { doAction } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import { ANNOTATION_CHANGED, SELECTION_CHANGED } from "../common/constants";
import { isAnnotationElement } from "../common/helpers";

import "./index.scss";

const tinymce = global["tinymce"];
tinymce.PluginManager.add("wl_tinymce_2", function(ed) {
  // Capture `NodeChange` events and broadcast the selected text.
  ed.on("NodeChange", e => {
    doAction(SELECTION_CHANGED, { selection: ed.selection.getContent({ format: "text" }) });

    // Fire the annotation change.
    const payload =
      "undefined" !== typeof e && isAnnotationElement(e.element)
        ? // Set the payload to `{ annotationId }` if it's an annotation otherwise to null.
          e.element.id
        : undefined;
    doAction(ANNOTATION_CHANGED, payload);
  });
});
