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

  const bodyEls = document.getElementsByTagName("body");
  if (0 < bodyEls.length && bodyEls[0].classList.contains("block-editor-page")) {
    // Block editor is active, return the post content.
    return wp.data
      .select("core/editor")
      .getEditedPostAttribute("content")
      .replace(/<[^>]+>/gi, "");
  } else if (tinymce !== undefined && tinymce.editors["content"] !== undefined) {
    return tinymce.editors["content"].getContent({ format: "text" });
  }

  return "";
}

/**
 * Remove default excerpt panel in block editor, show only
 * our custom meta box.
 */
function removeDefaultExcerptPanel() {
  if (!document.body.classList.contains("block-editor-page")) return;

  if (wp.data && wp.data.dispatch("core/edit-post")) {
    wp.data.dispatch("core/edit-post").removeEditorPanel("post-excerpt");
  }
}

export { getPostContent, removeDefaultExcerptPanel };
