/* global tinymce */

tinymce.PluginManager.add("@wordlift/design/tinymce", function (editor, url) {
  editor.on("NodeChange", (e) => {
    const selection = editor.selection;

    postMessage("wordlift/design/editor/selectionChange", {
      selection: {
        text: selection.getContent({ format: "text" }),
        html: selection.getContent({ format: "html" }),
        rect: calcRect(editor),
      },
      editor: { id: editor.id },
    });
  });

  editor.on("Init", () => {
    editor.contentWindow.addEventListener("scroll", function () {
      postMessage("wordlift/design/editor/scroll");
    });

    window.addEventListener("resize", function () {
      postMessage("wordlift/design/editor/resize");
    });
  });

  return {
    getMetadata: function () {
      return {
        name: "WordLift Design, TinyMCE Plugin",
        url: "https://wordlift.io",
      };
    },
  };
});

const calcRect = (editor) => {
  // Get the selection. Bail out is the selection is collapsed (is just a caret).
  const selection = editor.selection;
  if ("" === selection.getContent({ format: "text" })) return null;

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

const postMessage = (type, payload = {}) =>
  window.postMessage(
    {
      type,
      payload: { ...payload, ...{ source: "tinymce" } },
    },
    window.origin
  );
