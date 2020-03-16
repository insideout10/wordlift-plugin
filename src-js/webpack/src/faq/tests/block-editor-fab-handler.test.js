/**
 * This test is to test the fab handler which shows/hides the button
 * depends on the state of store and the text selected.
 */

/**
 * External dependencies
 */
import {off, on, trigger} from "backbone";
/**
 * Internal depdendencies.
 */
import BlockEditorFabHandler from "../hooks/block-editor/block-editor-fab-handler";
import BlockEditorFabButtonRegister, {
  FAB_ID,
  FAB_WRAPPER_ID
} from "../hooks/block-editor/block-editor-fab-button-register";
import {SELECTION_CHANGED} from "../../common/constants";
import {blockEditorWithNoSelectedBlocks, blockEditorWithSelectedBlocks} from "./mock-dependencies/block-editor";
import {FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_ITEMS_CHANGED} from "../constants/faq-hook-constants";

let selectedNode = null;

beforeEach(() => {
  global["_wlFaqSettings"] = {
    addQuestionText: "Add Question",
    addAnswerText: "Add Answer"
  };
  /**
   * This mock is used to substitute the range which is not available in jest env
   * @return {{rangeCount: number, getRangeAt: (function(*): {getBoundingClientRect: (function(): {top: number, left: number, bottom: number, right: number, height: number}), cloneContents: (function(): null)}), selectedNode: null}}
   */
  window.getSelection = () => ({
    selectedNode: selectedNode,
    rangeCount: 1,
    getRangeAt: index => {
      return {
        cloneContents: () => {
          return selectedNode;
        },
        getBoundingClientRect: () => {
          return {
            right: 10,
            bottom: 10,
            height: 20,
            left: 40,
            top: 40
          };
        }
      };
    }
  });
  /**
   * Turn off all event listeners before the start of test.
   */
  off(SELECTION_CHANGED);
  off(FAQ_ITEMS_CHANGED);
  off(FAQ_EVENT_HANDLER_SELECTION_CHANGED);
});

afterEach(() => {
  window.getSelection = null;
});

it("when the text is selected is a question then the fab should be displayed with add question text", () => {
  // Before calling the fab handler, register the button
  const registry = new BlockEditorFabButtonRegister();
  registry.registerFabButton();
  const handler = new BlockEditorFabHandler();

  /**
   * step 1 :  We are going to create a paragraph element with question
   * and set selection, since selection is not supported in jest, we are
   * mocking its functionality
   */
  window.getSelection();
  global["wp"] = blockEditorWithNoSelectedBlocks;
  selectedNode = document.createElement("p");
  selectedNode.textContent = "this is a question?";
  /**
   * step 2: emit a event to make the floating action button to display
   * with add question text.
   */
  trigger(SELECTION_CHANGED, { selection: "this is a question?" });
  /**
   * step 3: check if fab is displayed, if the text is correctly set based on the
   * type
   */
  expect(document.getElementById(FAB_WRAPPER_ID).style.display).toEqual("block");
  expect(document.getElementById(FAB_ID).innerText).toEqual("Add Question");
});

it("when the text is selected is a answer then the fab should be displayed with add answer text", () => {
  // Before calling the fab handler, register the button
  const registry = new BlockEditorFabButtonRegister();
  registry.registerFabButton();
  const handler = new BlockEditorFabHandler();

  /**
   * step 1 :  We are going to create a paragraph element with question
   * and set selection, since selection is not supported in jest, we are
   * mocking its functionality
   */
  window.getSelection();
  global["wp"] = blockEditorWithNoSelectedBlocks;
  selectedNode = document.createElement("p");
  selectedNode.textContent = "this is a answer";
  /**
   * Note: Floating action button would be only displayed if there is
   * unanswered question in the store. lets add the questions.
   */
  trigger(FAQ_ITEMS_CHANGED, [
    {
      question: "foo question?",
      answer: ""
    }
  ]);
  /**
   * step 2: emit a event to make the floating action button to display
   * with add question text.
   */
  trigger(SELECTION_CHANGED, { selection: "this is a answer" });
  /**
   * step 3: check if fab is displayed, if the text is correctly set based on the
   * type
   */
  expect(document.getElementById(FAB_WRAPPER_ID).style.display).toEqual("block");
  expect(document.getElementById(FAB_ID).innerText).toEqual("Add Answer");
});

it("when the fab is clicked then it should emit a correct event with all the correct keys", () => {
  // Before calling the fab handler, register the button
  const registry = new BlockEditorFabButtonRegister();
  registry.registerFabButton();
  const handler = new BlockEditorFabHandler();

  /**
   * step 1 :  We are going to create a paragraph element with question
   * and set selection, since selection is not supported in jest, we are
   * mocking its functionality
   */
  window.getSelection();
  global["wp"] = blockEditorWithSelectedBlocks;
  selectedNode = null;
  /**
   * Note: Floating action button would be only displayed if there is
   * unanswered question in the store. lets add the questions.
   */
  trigger(FAQ_ITEMS_CHANGED, [
    {
      question: "foo question?",
      answer: ""
    }
  ]);
  /**
   * step 2: emit a event to make the floating action button to display
   * with add question text.
   */
  trigger(SELECTION_CHANGED, { selection: "this is a answer" });
  /**
   * step 3: check if fab is displayed, if the text is correctly set based on the
   * type
   */
  expect(document.getElementById(FAB_WRAPPER_ID).style.display).toEqual("block");
  expect(document.getElementById(FAB_ID).innerText).toEqual("Add Answer");

  /**
   * Listen for the FAQ_EVENT_HANDLER_SELECTION_CHANGED from the handler
   */
  let selectedObject = null;
  on(FAQ_EVENT_HANDLER_SELECTION_CHANGED, obj => {
    selectedObject = obj;
  });
  /**
   * step 4: lets make a click on the floating action button on the button
   */
  document.getElementById(FAB_ID).click();
  expect(selectedObject.selectedText).not.toEqual(undefined);
  expect(selectedObject.selectedHTML).not.toEqual(undefined);
});
