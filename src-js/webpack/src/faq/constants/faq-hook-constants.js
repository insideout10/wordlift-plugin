/**
 * Constants for the FAQ hooks.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Event name when the text selection changed in any of text editor, emitted
 * from the hooks.
 * @type {string}
 */
export const FAQ_REQUEST_ADD_NEW_QUESTION = "FAQ_REQUEST_ADD_NEW_QUESTION";

/**
 * Event emitted by hook when the text selection is changed.
 * @type {string}
 */
export const FAQ_EVENT_HANDLER_SELECTION_CHANGED = "FAQ_EVENT_HANDLER_SELECTION_CHANGED";

/**
 * Event emitted by the store when the faq items are changed
 * @type {string}
 */
export const FAQ_ITEMS_CHANGED = "FAQ_ITEMS_CHANGED";

/**
 * Event emitted by the store when a question or answer
 * is added by ui, asking the editor to highlight the text.
 */
export const FAQ_HIGHLIGHT_TEXT = "FAQ_HIGHLIGHT_TEXT";


/**
 * Event emitted by the hook to event handler when the faq is selected by the user.
 */
export const FAQ_ITEM_SELECTED_ON_TEXT_EDITOR = "FAQ_ITEM_SELECTED_ON_TEXT_EDITOR";
