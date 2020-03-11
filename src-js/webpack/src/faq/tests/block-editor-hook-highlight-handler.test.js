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

beforeEach(() => {
  // Reset event handlers before every test.
  off(FAQ_HIGHLIGHT_TEXT);
});

afterEach(() => {
  global["wp"] = null;
});

/**
 * A mock selected blocks object obtained from gutenberg, used
 * for testing the hook externally.
 */
const fakeParagraphBlocksData = [
  {
    clientId: "1595319b-0c37-41b3-addf-93804ded1a68",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content: "this is a answer in first paragraph",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "7f122677-7ebd-44bd-80fb-5f9ecdff11f5",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content: "this is a answer in second",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "cfe47d7b-b5cd-4af2-b4c1-8c747313a6e0",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content: "this is answer in third",
      dropCap: false
    },
    innerBlocks: []
  }
];

it("when the event is emitted from store to highlight the multiple block selection, then it should highlight it correctly", () => {
  const handler = new BlockEditorHighlightHandler();
  handler.listenForHighlightEvent();
  const updateBlockAttributesMethod = jest.fn(() => {
  });
  /**
   * Setup dependencies used by our hook.
   */
  global["wp"] = {
    data: {
      select: editorString => {
        if (editorString === "core/block-editor") {
          return {
            getMultiSelectedBlocks: () => fakeParagraphBlocksData
          };
        }
      },
      dispatch: editorString => {
        if (editorString === "core/block-editor") {
          return {
            updateBlockAttributes: updateBlockAttributesMethod
          };
        }
      }
    }
  };

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
