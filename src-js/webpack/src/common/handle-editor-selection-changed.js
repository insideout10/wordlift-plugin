/**
 * External dependencies
 */
import { Button, createPopover } from "@wordlift/design";
import React from "react";
import { call, delay } from "redux-saga/effects";

let popover;

export function* handleEditorSelectionChanged({ payload }) {
  console.log("handleEditorSelectionChanged", payload);
  yield delay(300);

  const editor = payload.editor;

  // Get the selection. Bail out is the selection is collapsed (is just a caret).
  const selection = editor.selection;
  if (selection.isCollapsed() || "" === selection.getContent({ format: "text" })) {
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
  const iframeRect = iframe ? iframe.getBoundingClientRect() : { top: 0, right: 0, bottom: 0, left: 0 };

  // Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.
  const rect = {
    top: iframeRect.top + editorRect.top + window.scrollY,
    right: iframeRect.left + editorRect.right + window.scrollX,
    bottom: iframeRect.top + editorRect.bottom + window.scrollY,
    left: iframeRect.left + editorRect.left + window.scrollX,
  };

  // const container = document.createElement("span");
  // container.style.position = "absolute";
  // container.style.top = "0";
  // container.style.left = "0";
  //
  // const span = document.createElement("span");
  // span.style.width = rect.right - rect.left + "px";
  // span.style.height = rect.bottom - rect.top + "px";
  // span.style.top = rect.top + "px";
  // span.style.left = rect.left + "px";
  // span.style.background = "transparent";
  // span.style.border = "1px solid red";
  // span.style.position = "absolute";
  //
  // container.appendChild(span);
  // document.body.appendChild(container);

  // Finally create the popover.
  popover = yield call(
    createPopover,
    <div>
      <Button>Hello WordPress!</Button>
    </div>,
    { ...rect, positions: ["right", "left", "bottom", "top"] }
  );
}
