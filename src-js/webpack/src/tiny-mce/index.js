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
 * External dependencies
 */
import React from 'react';
import { trigger } from 'backbone';
/**
 * Internal dependencies
 */
import { ANNOTATION_CHANGED, SELECTION_CHANGED } from '../common/constants';
import { isAnnotationElement } from '../common/helpers';

import './index.scss';

const tinymce = global['tinymce'];
tinymce.PluginManager.add('wl_tinymce_2', function (ed) {
	// Capture `NodeChange` events and broadcast the selected text.
	ed.on('NodeChange', (e) => {
		trigger(SELECTION_CHANGED, {
			selection: ed.selection.getContent({ format: 'text' }),
			selectionHtml: ed.selection.getContent({ format: 'html' }),
			editor: ed,
			editorType: 'tinymce',
			rect: calcRect(ed),
		});

		// Fire the annotation change.
		const payload =
			'undefined' !== typeof e && isAnnotationElement(e.element)
				? // Set the payload to `{ annotationId }` if it's an annotation otherwise to null.
				  e.element.id
				: undefined;
		trigger(ANNOTATION_CHANGED, payload);
	});
});

const calcRect = (editor) => {
	// Get the selection. Bail out is the selection is collapsed (is just a caret).
	const selection = editor.selection;
	if ('' === selection.getContent({ format: 'text' })) return null;

	// Get the selection range and bail out if it's null.
	const range = selection.getRng();
	if (null == range) return null;

	// Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.
	const editorRect = range.getBoundingClientRect();

	// Get TinyMCE's iframe element's bounding rect.
	const iframe = editor.iframeElement;
	const iframeRect = iframe
		? iframe.getBoundingClientRect()
		: { top: 0, right: 0, bottom: 0, left: 0 };

	// Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.
	return {
		top: iframeRect.top + editorRect.top + window.scrollY,
		right: iframeRect.left + editorRect.right + window.scrollX,
		bottom: iframeRect.top + editorRect.bottom + window.scrollY,
		left: iframeRect.left + editorRect.left + window.scrollX,
	};
};
