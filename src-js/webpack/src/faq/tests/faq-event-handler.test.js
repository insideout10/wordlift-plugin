/**
 * Tests the FAQ Event handler class.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqEventHandler, {textEditors} from "../faq-event-handler";
import TinyMceFaqHook from "../hooks/tiny-mce-faq-hook";

beforeEach(() => {
  global["_wlFaqSettings"] = undefined;
});
test("check if the correct hook is initialized", () => {
  // Create tinymce in the global namespace, so the event handler
  // would initialize the tinymce hook.
  global["_wlFaqSettings"] = {};
  global["_wlFaqSettings"]["textEditor"] = textEditors.TINY_MCE;
  const eventHandler = new FaqEventHandler();
  expect(eventHandler.getHook()).toBeInstanceOf(TinyMceFaqHook);
});

test("check if no hook condition matches, null should be returned", () => {
  const eventHandler = new FaqEventHandler();
  expect(eventHandler.getHook()).toEqual(null);
});
