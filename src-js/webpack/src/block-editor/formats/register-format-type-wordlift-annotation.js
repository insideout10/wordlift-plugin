/**
 * This file register WordLift's `wordlift/annotation` format type.
 *
 * The `wordlift/annotation` format type is used to receive selection changes
 * event from `paragraph` blocks and broadcasts them as WordPress' action.
 *
 * Its use is similar to the `tiny-mce` adapter which listens to selection changes
 * in `classic` blocks.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { registerFormatType } from "@wordpress/rich-text";
import { Fragment } from "@wordpress/element";
import { doAction } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import EditComponent from "./edit-component";

/**
 * @see https://developer.wordpress.org/block-editor/tutorials/format-api/1-register-format/
 */

registerFormatType("wordlift/annotation", {
  /*
   * The `attributes` property is undocumented as basically the `WPFormat` class.
   *
   * Run this in the Developer Tools > Console to see what other formats return
   * as WPFormat:
   *  wp.data.select( 'core/rich-text' ).getFormatTypes();
   */
  attributes: { id: "id", class: "class", itemid: "itemid" },
  tagName: "span",
  className: "textannotation",
  title: "Annotation",
  edit: EditComponent
});
