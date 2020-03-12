import TinymceHighlightHandler from "../hooks/tinymce/tinymce-highlight-handler";
import { off, trigger } from "backbone";
import { FAQ_HIGHLIGHT_TEXT, FAQ_ITEM_DELETED } from "../constants/faq-hook-constants";
import { FAQ_ANSWER_TAG_NAME } from "../hooks/custom-faq-elements";
import {faqEditItemType} from "../components/faq-edit-item";

const setContentMockFn = jest.fn();
const editor = {
  selection: {
    getContent: () => {
      return "<p>a simple answer</p>";
    },
    setContent: setContentMockFn
  }
};

beforeEach(() => {
  global["customElements"] = {
    get: jest.fn(() => {
      return undefined;
    }),
    define: () => {}
  };
  // Reset mocks
  setContentMockFn.mockReset()
  // Reset event handlers before every test.
  off(FAQ_HIGHLIGHT_TEXT);
  off(FAQ_ITEM_DELETED);
});



it("when the highlight event is sent from store, should highlight the text which is currently selected", () => {

  const handler = new TinymceHighlightHandler(editor);
  /**
   * Whenever the tinymce node changes, the editor saves the selection inside the
   * instance variable by calling this method, mocking the same call with saveSelection()
   */
  handler.saveSelection();
  trigger(FAQ_HIGHLIGHT_TEXT, {
    isQuestion: false,
    id: 123
  });
  expect(setContentMockFn.mock.calls).toHaveLength(1);
  /**
   * Since it is a answer, we should answer tag in the set content.
   */
  expect(setContentMockFn.mock.calls[0][0]).toEqual(
    `<p><${FAQ_ANSWER_TAG_NAME} class="123">a simple answer</${FAQ_ANSWER_TAG_NAME}></p>`
  );
});


it("when the delete event is sent from store, should delete the tags by class from the editor", () => {

  const handler = new TinymceHighlightHandler(editor);
  /**
   * Whenever the delete event is emitted, getContent is called,
   * so we provide our highlighted html in the get content
   */
  editor.getContent = () => {
    return "<wl-faq-question class='123'>this is a simple question?</wl-faq-question><wl-faq-answer class='246'>this answer should not be removed</wl-faq-answer>"
  };
  const setContentFn = jest.fn();
  editor.setContent = setContentFn;
  handler.saveSelection();
  trigger(FAQ_ITEM_DELETED, {
    type: faqEditItemType.QUESTION,
    id: 123
  });
  expect(setContentFn.mock.calls).toHaveLength(1);
  /**
   * Since it is a answer, we should answer tag in the set content.
   */
  expect(setContentFn.mock.calls[0][0]).toEqual(
      `this is a simple question?<wl-faq-answer class="246">this answer should not be removed</wl-faq-answer>`
  );
});


