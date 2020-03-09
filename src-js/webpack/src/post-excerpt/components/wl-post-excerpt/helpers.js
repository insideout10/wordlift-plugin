/**
 * Helper functions for the post excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Return post content depending on the text editor.
 * @return {string} Text content of the post
 */
function getPostContent() {
  const { wp, tinymce } = global;
  let html = "";
  if (wp !== undefined && wp.data !== undefined && wp.data.select !== undefined) {
    // Block editor is active, return the post content.
    html = wp.data.select("core/editor").getCurrentPost().content;
  } else if (tinymce !== undefined && tinymce.activeEditor !== undefined) {
    html = tinymce.activeEditor.getContent();
  }
  // Render it on the dom and get the inner text
  const el = document.createElement("div");
  el.innerHTML = html;
  return el.textContent;
}

/**
 * Remove default excerpt panel in block editor, show only
 * our custom meta box.
 */
function removeDefaultExcerptPanel() {
  if (wp.data !== undefined) {
    wp.data.dispatch("core/edit-post").removeEditorPanel("post-excerpt");
  }
}
export { getPostContent, removeDefaultExcerptPanel };
