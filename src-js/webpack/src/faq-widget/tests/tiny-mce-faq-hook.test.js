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
import {FAQ_EVENT_HANDLER_SELECTION_CHANGED} from "../constants/faq-hook-constants";

test("check if the tinymce selection changed, event handler should receive text selection event", () => {
  // global["tinymce"] = "";
  // const tinyMceFaqHook = new TinyMceFaqHook();
  // // we have tiny mce hook now, lets emit a mock event from tiny mce
  //   let isEventEmittedToEventHandler = false
  //   on( FAQ_EVENT_HANDLER_SELECTION_CHANGED, ({selectedText})=> isEventEmittedToEventHandler = true)
  //   expect(isEventEmittedToEventHandler).toEqual(true)
});
