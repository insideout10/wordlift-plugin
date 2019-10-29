const tinymce = global["tinymce"];

tinymce.PluginManager.add("example", function(editor, url) {
  editor.on("selectionchange", () => {
    if (editor.hasFocus())
      console.log({ focus: editor.hasFocus(), selection: editor.selection.getContent({ format: "text" }) });
  });
});
