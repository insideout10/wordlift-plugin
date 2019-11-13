/**
 * This file registers the WordLift Entities block type.
 *
 * The WordLift Entities block type is used to store entity data for the current
 * post.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-registration/
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @see https://github.com/insideout10/wordlift-plugin/issues/944
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
import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";

// Registering my block with a unique name
registerBlockType("wordlift/classification", {
  title: __("WordLift Classification", "wordlift"),
  description: __("A block holding the classification data for the current post.", "wordlift"),
  category: "wordlift",
  attributes: {
    entities: {
      type: "array"
    }
  },
  supports: {
    // Do not support HTML editing.
    html: false,
    // Only support being inserted programmatically.
    inserter: false,
    // Only allow one block.
    multiple: false,
    // Do not allow reusability.
    reusable: false
  },
  edit: () => <div>WordLift Classification (edit)</div>,
  save: () => {
    console.log("saving...");
    return <div>WordLift Classification (save)</div>;
  }
});
