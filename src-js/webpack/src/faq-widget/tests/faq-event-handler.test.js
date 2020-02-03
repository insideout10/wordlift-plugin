/**
 * Tests the FAQ Event handler class.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FAQEventHandler from "../FAQ-event-handler";
import TinyMceFaqHook from "../hooks/tiny-mce-faq-hook";

test("check if the correct hook is initialized", () => {
    // Create tinymce in the global namespace, so the event handler
    // would initialize the tinymce hook.
    global["tinymce"] = ""
    const eventHandler = new FAQEventHandler()
    expect(eventHandler.getHook()).toBeInstanceOf(TinyMceFaqHook);
});
