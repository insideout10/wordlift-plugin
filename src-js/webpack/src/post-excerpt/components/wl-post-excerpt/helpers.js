/**
 * Helper functions for the post excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */


/**
 * External dependencies
 */
import he from 'he'

/**
 * Filter the html entities and retain spaces
 * @since 3.27.8
 */
export function filterPostContent( content ) {
  /**
   * Note: we cant rely on innerText or textContent property as
   * that would remove new lines, so we remove the tags manually,
   * and use a html entity decoder to remove the html entities
   */
 return he.decode( content.replace(/<\/li>/gi, '\n').replace(/<[^>]+>/gi, "") );
}

/**
 * Return post content depending on the text editor.
 * @return {string} Text content of the post
 */
function getPostContent() {
  const { wp, tinymce } = global;

  const bodyEls = document.getElementsByTagName("body");
  if (0 < bodyEls.length && bodyEls[0].classList.contains("block-editor-page")) {
    // Block editor is active, return the post content.
    return filterPostContent( wp.data
      .select("core/editor")
      .getEditedPostAttribute("content") );

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
