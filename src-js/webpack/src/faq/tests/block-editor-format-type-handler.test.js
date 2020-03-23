/**
 * This file tests the format type handler for the faq block editor hook.
 */

import BlockEditorFormatTypeHandler from "../hooks/block-editor/block-editor-format-type-handler";
import {FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME} from "../hooks/custom-faq-elements";
import {registerFormatType} from "@wordpress/rich-text";

const customElementDefineFunction = jest.fn();
jest.mock("@wordpress/rich-text");

beforeEach(() => {
  global["customElements"] = {
    get: jest.fn(() => {
      return undefined;
    }),
    define: customElementDefineFunction
  };
});

it("when format type handler is registering, should register correct formats", () => {
  const handler = new BlockEditorFormatTypeHandler();
  handler.registerAllFormatTypes();
  // When registering format types we also register those custom elements.
  expect(customElementDefineFunction.mock.calls).toHaveLength(2);
  // we should have question and answer tags in the args.
  expect(customElementDefineFunction.mock.calls[0][0]).toEqual(FAQ_QUESTION_TAG_NAME);
  expect(customElementDefineFunction.mock.calls[1][0]).toEqual(FAQ_ANSWER_TAG_NAME);
  // we expect the register format type to be called twice.
  expect(registerFormatType.mock.calls).toHaveLength(2);
  /**
   * We should have registered question and answered tag in the
   * block editor format types.
   */
  expect(registerFormatType.mock.calls[0][0]).toEqual("wordlift/faq-answer");
  expect(registerFormatType.mock.calls[1][0]).toEqual("wordlift/faq-question");
});
