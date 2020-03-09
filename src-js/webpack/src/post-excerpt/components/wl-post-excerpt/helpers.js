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
  let html = "";
  if (wp.data !== undefined) {
    // Block editor is active, return the post content.
    html = wp.data.select("core/editor").getCurrentPost().content;
  } else {
    html = tinymce.activeEditor.getContent();
  }
  // Render it on the dom and get the inner text
  const el = document.createElement("div");
  el.innerHTML = html;
  return el.innerText;
}
export { getPostContent };
