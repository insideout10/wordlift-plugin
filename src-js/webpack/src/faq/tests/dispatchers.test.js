/**
 * This file tests faq hook to store dispatcher class
 */
import FaqHookToStoreDispatcher from "../hooks/dispatchers/faq-hook-to-store-dispatcher";
import {answerSelectedByUser, requestAddNewQuestion, updateFaqItem, updateQuestionOnInputChange} from "../actions";
import {faqEditItemType} from "../components/faq-edit-item";

it("on creating a question, dispatcher should emit correct action", () => {
  const dispatchFn = jest.fn();
  const store = {
    dispatch: dispatchFn
  };
  const dispatcher = new FaqHookToStoreDispatcher(store);
  dispatcher.dispatchTextSelectedAction({
    selectedText: "this is a question?",
    selectedHTML: "<p>this is a question?</p>"
  });
  expect(dispatchFn.mock.calls).toHaveLength(2);
  expect(dispatchFn.mock.calls[0][0]).toEqual(updateQuestionOnInputChange("this is a question?"));
  expect(dispatchFn.mock.calls[1][0]).toEqual(requestAddNewQuestion());
});

it("on adding answer, dispatcher should emit correct action", () => {
  const dispatchFn = jest.fn();
  const store = {
    dispatch: dispatchFn,
    getState: () => {
      return {
        faqListOptions: {
          faqItems: [
            {
              question: "unanswered question?",
              answer: "",
              id: 123
            }
          ]
        }
      };
    }
  };
  const dispatcher = new FaqHookToStoreDispatcher(store);
  dispatcher.dispatchTextSelectedAction({
    selectedText: "this is a answer",
    selectedHTML: "<p>this is a answer</p>"
  });
  expect(dispatchFn.mock.calls[0][0]).toEqual(
    updateFaqItem({
      id: 123,
      type: faqEditItemType.ANSWER,
      value: "<p>this is a answer</p>"
    })
  );
});

it("on adding answer with multiple questions present, dispatcher should emit correct action", () => {
  const dispatchFn = jest.fn();
  const store = {
    dispatch: dispatchFn,
    getState: () => {
      return {
        faqListOptions: {
          faqItems: [
            {
              question: "unanswered question?",
              answer: "",
              id: 123
            },
            {
              question: "unanswered question 2?",
              answer: "",
              id: 124
            }
          ]
        }
      };
    }
  };
  const dispatcher = new FaqHookToStoreDispatcher(store);
  dispatcher.dispatchTextSelectedAction({
    selectedText: "this is a answer",
    selectedHTML: "<p>this is a answer</p>"
  });
  expect(dispatchFn.mock.calls[0][0]).toEqual(
    answerSelectedByUser({
      selectedAnswer: "<p>this is a answer</p>"
    })
  );
});
