/**
 * TinyMceHighlightHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

const QUESTION_HIGHLIGHT_COLOR = "#00ff00";

class TinymceHighlightHandler {
  /**
   * Construct highlight handler instance.
   * @param editor The Tinymce editor instance.
   * @param store Redux store.
   */
  constructor(editor, store) {
    this.editor = editor;
    this.store = store;
  }

  highlightSelectedText(selectedText) {
    const editor = this.editor;
    const highlightClassName = global["_wlFaqSettings"]["faqHighlightClass"];
    editor.selection.setContent(
      "<span class='" + highlightClassName + "' style='background-color: #ffff00;'>" + selectedText + "</span>"
    );
  }
}

export default TinymceHighlightHandler;
