/**
 * This file tests the highlighting handler of the faq block editor hook.
 */

/**
 * External dependencies.
 */
import BlockEditorHighlightHandler from "../hooks/block-editor/block-editor-highlight-handler";
import { off, trigger } from "backbone";
import { FAQ_HIGHLIGHT_TEXT } from "../constants/faq-hook-constants";
import {FAQ_ANSWER_TAG_NAME} from "../hooks/custom-faq-elements";
import {blockEditorWithSelectedBlocks, updateBlockAttributesMethod} from "./mock-dependencies/block-editor";

beforeEach(() => {
  // Reset event handlers before every test.
  off(FAQ_HIGHLIGHT_TEXT);
});

afterEach(() => {
  global["wp"] = null;
});



it("when the event is emitted from store to highlight the multiple block selection, then it should highlight it correctly", () => {
  const handler = new BlockEditorHighlightHandler();
  handler.listenForHighlightEvent();

  /**
   * Setup dependencies used by our hook.
   */
  global["wp"] = blockEditorWithSelectedBlocks
  /**
   * step 1: we are mocking a highlight event from the faq store
   * which will cause the block editor handler to highlight the
   * text selected by the user.
   */
  trigger(FAQ_HIGHLIGHT_TEXT, {
    isQuestion: false,
    id: 123
  });
  /**
   * Our hook should made 3 calls to updateBlockAttributes method
   * with the highlighted html becuase three blocks are selected
   */
  expect(updateBlockAttributesMethod.mock.calls).toHaveLength(3)
  /**
   * each call should contain client id and attribute object
   * with content attribute.
   */
  const singleCall = updateBlockAttributesMethod.mock.calls[0]
  expect(singleCall[0]).not.toEqual(undefined)
  expect(singleCall[1].content).not.toEqual(undefined)
  /**
   * Expect a answer tag in the content since we have sent
   * the signal to highlight it as answer.
   */
  expect(singleCall[1].content.includes(FAQ_ANSWER_TAG_NAME)).toEqual(true)
});


it("when remove highlight event is emitted from store upon deleting a faq item, should remove highlighting correctly", () =>{

})