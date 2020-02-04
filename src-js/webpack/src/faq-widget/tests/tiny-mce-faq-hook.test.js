/**
 * Tests the FAQ Event handler class.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { trigger, on } from "backbone";

/**
 * Internal dependencies
 */
import TinyMceFaqHook from "../hooks/tiny-mce-faq-hook";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../constants/faq-hook-constants";
jest.mock("backbone");

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
});

test("check if the tinymce selection changed, event handler should receive text selection event", () => {});

afterEach(() => {
  global["tinymce"] = undefined;
});
