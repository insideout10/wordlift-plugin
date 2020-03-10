import { configure, mount } from "enzyme";
import { Provider } from "react-redux";
import React from "react";
import FaqModal from "../components/faq-modal";
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import { faqReducer } from "../reducers";
import { FAQ_INITIAL_STATE } from "../store";
import rootSaga from "../sagas";
import Adapter from "enzyme-adapter-react-16";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_ITEM_SELECTED_ON_TEXT_EDITOR } from "../constants/faq-hook-constants";
import { off, trigger } from "backbone";
import FaqEventHandler from "../hooks/faq-event-handler";
import { updateFaqItems } from "../actions";
import { transformAPIDataToUi } from "../sagas/filters";
import { updateSuccessResponse } from "./faq-screen.test";

configure({ adapter: new Adapter() });

beforeAll(() => {
  global["_wlFaqSettings"] = {
    restUrl: "https://wordlift.localhost/index.php?rest_route=/wordlift/v1/faq",
    listBoxId: "wl-faq-meta-list-box",
    addQuestionText: "Add",
    nonce: "101a671e3d",
    postId: "436",
    invalidTagMessage: "Invalid tags {INVALID_TAGS} is present in answer",
    invalidWordCountMessage: "Answer word count must not exceed {ANSWER_WORD_COUNT_WARNING_LIMIT} words"
  };
});

afterAll(() => {
  global["_wlFaqSettings"] = null;
});

let testStore = null;
beforeEach(() => {
  // Reset all event handlers.
  off(FAQ_EVENT_HANDLER_SELECTION_CHANGED);
  off(FAQ_ITEM_SELECTED_ON_TEXT_EDITOR);
  fetch.resetMocks();
  fetch.mockClear();
  const sagaMiddleware = createSagaMiddleware();
  testStore = createStore(faqReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
});


it("when only one question present then the modal should not open on " + "the text selection event", () => {
  const faqItems = [
    {
      question: "this is a question without an answer?",
      answer: "",
      id: 1582622863
    }
  ];
  // FAQ modal usually send a GET request on mounted, so provide a mock response.
  fetch.mockResponseOnce(JSON.stringify(faqItems));

  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(faqItems);
  testStore.dispatch(action);

  const wrapper = mount(
    <Provider store={testStore}>
      <FaqModal />
    </Provider>
  );
  new FaqEventHandler(testStore);
  // Upon triggering this is going to make a update to API, so deliever success response
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  // clear all the mocks before
  fetch.mockClear();
  // emit a event from hook that text was selected.
  trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
    selectedText: "sample answer",
    selectedHTML: "sample answer html"
  });

  // Should not open modal when only one question is present
  expect(testStore.getState().faqModalOptions.isModalOpened).toEqual(false);
  // Also expect an API call to update the answer.
  const postedData = JSON.parse(fetch.mock.calls[0][1].body);
  expect(postedData.faq_items[0].answer).toEqual("sample answer html");
});

it(
  "when multiple unanswered questions present, then the modal should be displayed, and user can apply" +
    "the answer to the question",
  () => {
    const faqItems = [
      {
        question: "this is a question without an answer?",
        answer: "",
        id: 1582622863
      },
      {
        question: "this is a question without an answer too?",
        answer: "",
        id: 1582622864
      }
    ];
    // FAQ modal usually send a GET request on mounted, so provide a mock response.
    fetch.mockResponseOnce(JSON.stringify(faqItems));

    const action = updateFaqItems();
    action.payload = transformAPIDataToUi(faqItems);
    testStore.dispatch(action);
    const wrapper = mount(
      <Provider store={testStore}>
        <FaqModal />
      </Provider>
    );
    new FaqEventHandler(testStore);
    // Upon triggering this is going to make a update to API, so deliever success response
    fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
    // clear all the mocks before
    fetch.mockClear();
    // emit a event from hook that text was selected.
    trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
      selectedText: "sample answer",
      selectedHTML: "sample answer html"
    });
    // Should show the modal because mutiple questions present
    expect(testStore.getState().faqModalOptions.isModalOpened).toEqual(true);
    // Click on apply button, now we should receive the API request.
    wrapper
      .find(".wl-faq-apply-button")
      .at(0)
      .simulate("click");
    // Also expect an API call to update the answer.
    const postedData = JSON.parse(fetch.mock.calls[0][1].body);
    expect(postedData.faq_items[0].answer).toEqual("sample answer html");
  }
);

