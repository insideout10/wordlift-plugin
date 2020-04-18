/**
 * This file provides a handle for the editor selection changed event.
 *
 * When the event is fired a Popover is displayed.
 */

/**
 * External dependencies
 */
import { Button, createPopover } from '@wordlift/design';
import React from 'react';
import { call, delay } from 'redux-saga/effects';

let popover;

const handlers = {
	tinymce: (payload) => {
		const editor = payload.editor;
		if (undefined === editor || null === editor) return;

		// Get the selection. Bail out is the selection is collapsed (is just a caret).
		const selection = editor.selection;
		if (
			selection.isCollapsed() ||
			'' === selection.getContent({ format: 'text' })
		) {
			if (popover) popover.unmount();
			return;
		}

		// Get the selection range and bail out if it's null.
		const range = selection.getRng();
		if (null == range) {
			if (popover) popover.unmount();
			return;
		}

		// Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.
		const editorRect = range.getBoundingClientRect();

		// Get TinyMCE's iframe element's bounding rect.
		const iframe = editor.iframeElement;
		const iframeRect = iframe
			? iframe.getBoundingClientRect()
			: { top: 0, right: 0, bottom: 0, left: 0 };

		// Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.
		const rect = {
			top: iframeRect.top + editorRect.top + window.scrollY,
			right: iframeRect.left + editorRect.right + window.scrollX,
			bottom: iframeRect.top + editorRect.bottom + window.scrollY,
			left: iframeRect.left + editorRect.left + window.scrollX,
		};

		const layout = document.querySelector('.edit-post-layout__content');
		if (null !== layout) {
			const unmount = () => {
				if (popover) popover.unmount();
				layout.removeEventListener('scroll', unmount);
			};
			layout.addEventListener('scroll', unmount);
		}

		return rect;
	},
	'block-editor': (payload) => {
		const selection = window.getSelection();

		if (
			'' === payload.selection ||
			null === selection ||
			selection.isCollapsed ||
			1 !== selection.rangeCount
		) {
			if (popover) popover.unmount();
			return;
		}

		// Get the selection range and bail out if it's null.
		const range = selection.getRangeAt(0);
		if (null == range) {
			if (popover) popover.unmount();
			return;
		}

		// Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.
		const caretRect = range.getBoundingClientRect();

		const editorEl = document.querySelector('.edit-post-visual-editor');

		const layoutEl = editorEl.offsetParent;
		if (null !== layoutEl) {
			const unmount = () => {
				if (popover) popover.unmount();
				layoutEl.removeEventListener('scroll', unmount);
			};
			layoutEl.addEventListener('scroll', unmount);
		}

		return {
			top: caretRect.top + editorEl.scrollTop,
			right: caretRect.right + editorEl.scrollLeft,
			bottom: caretRect.bottom + editorEl.scrollTop,
			left: caretRect.left + editorEl.scrollLeft,
		};
	},
};

export function* handleEditorSelectionChanged({ payload }) {
	yield delay(300);

	if ('' === payload.selection) return;

	const handler = handlers[payload.editorType];
	const rect = handler(payload);

	// Finally create the popover.
	popover = yield call(
		createPopover,
		<div>
			<Button>Hello WordPress!</Button>
		</div>,
		{ ...rect, positions: ['right', 'left', 'bottom', 'top'] }
	);
}
