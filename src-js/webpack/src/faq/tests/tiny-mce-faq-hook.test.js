/**
 * Tests the FAQ Event handler class.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
jest.mock("backbone");
import {trigger} from "backbone";
/**
 * Internal dependencies
 */
import TinyMceFaqHook from "../hooks/tiny-mce-faq-hook";
import {FAQ_EVENT_HANDLER_SELECTION_CHANGED} from "../constants/faq-hook-constants";

beforeEach(() => {
  global["tinymce"] = {};
  global["tinymce"]["activeEditor"] = {};
  global["tinymce"]["listeners"] = {};
  global["tinymce"]["activeEditor"]["on"] = (name, callback) => {
    global["tinymce"]["listeners"][name] = callback;
  };
  global["tinymce"]["activeEditor"]["emit"] = name => {
    global["tinymce"]["listeners"][name]();
  };
  // mock the selection
  global["tinymce"]["activeEditor"]["selection"] = {};
  global["tinymce"]["activeEditor"]["selection"].getContent = () => "foo";
});

test("check if the tinymce selection changed, event handler should receive text selection event", () => {
  const hook = new TinyMceFaqHook();
  hook.listenForTextSelection();

  // Emit a event from the active editor mock.
  global["tinymce"]["activeEditor"]["emit"]("NodeChange");
  // The trigger method should be called once, since we have invoked a change in editor.
  expect(trigger.mock.calls.length).toEqual(1);
  // This trigger method should have arguments event name and text foo
  const args = trigger.mock.calls[0];
  // 2 Arguments should be passed.
  expect(args.length).toEqual(2);
  // 1st argument should be event changed string
  expect(args[0]).toEqual(FAQ_EVENT_HANDLER_SELECTION_CHANGED);
  // foo is the selected text
  expect(args[1]).toEqual("foo");
});

test("when text selection is changed check if its previously emiited", () => {
  const hook = new TinyMceFaqHook();
  hook.listenForTextSelection();
  // Rest previous mocks
  trigger.mockClear();
  // Emit a event from the active editor mock.
  global["tinymce"]["activeEditor"]["emit"]("NodeChange");
  // The trigger method should be called once, since we have invoked a change in editor.
  expect(trigger.mock.calls.length).toEqual(1);
  global["tinymce"]["activeEditor"]["emit"]("NodeChange");
  // we are emitting the same text twice, the event should not fire.
  expect(trigger.mock.calls.length).toEqual(1);
});

afterEach(() => {
  global["tinymce"] = undefined;
});
