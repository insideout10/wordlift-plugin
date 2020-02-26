/**
 * External dependencies.
 */
import React from "react";
import { configure, mount } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import { Provider } from "react-redux";
/**
 * Internal dependencies.
 */
import store, { FAQ_INITIAL_STATE } from "../store";
import FaqScreen from "../components/faq-screen";
import { updateFaqItems } from "../actions";
import { transformAPIDataToUi } from "../sagas/filters";
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import { faqReducer } from "../reducers";
import rootSaga from "../sagas";

configure({ adapter: new Adapter() });

export const getFaqItemsResponse = [
  {
    question: "this is a question?e",
    answer: "this is answer.de",
    id: 1582622863
  },
  {
    question: "this is an another question?",
    answer: "this is also answer....",
    id: 1582639238
  },
  {
    question: "this is third question??",
    answer: "this is third answeer.",
    id: 1582639326
  }
];

export const createNewQuestionResponse = { status: "success", message: "Question successfully added.", id: 1582698289 };
export const updateSuccessResponse = {
  status: "success",
  message: "Faq Items updated successfully"
};

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
  fetch.resetMocks();
  const sagaMiddleware = createSagaMiddleware();
  testStore = createStore(faqReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
});

it("should render faq items when faq items given", () => {
  // Mock the faq items data
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(getFaqItemsResponse);
  testStore.dispatch(action);

  const wrapper = mount(
    <Provider store={testStore}>
      <FaqScreen />
    </Provider>
  );

  // Now we have dispatched the action, we should have 3 items in html
  expect(wrapper.find(".wl-card")).toHaveLength(3);
});

it(
  "when the faq item is clicked then the edit screen should show," +
    " on clicking close button list should be displayed",
  () => {
    // Mock the faq items data
    const action = updateFaqItems();
    action.payload = transformAPIDataToUi(getFaqItemsResponse);
    testStore.dispatch(action);

    const wrapper = mount(
      <Provider store={testStore}>
        <FaqScreen />
      </Provider>
    );
    // Now click on a faq item.
    wrapper
      .find(".wl-card")
      .at(0)
      .simulate("click");
    // check the store, selected FAQ Id should be 1582622863 ( the first item on the response)
    expect(testStore.getState().faqListOptions.selectedFaqId).toEqual("1582622863");
    // lets click close button and check the html
    wrapper
      .find(".faq-edit-item-close-button")
      .at(0)
      .simulate("click");
    // now we need to have the 3 faq items to be displayed to the user.
    expect(wrapper.find(".wl-card")).toHaveLength(3);
  }
);

it("when the user opens the edit screen, should be able " + "to update / delete the question", () => {
  // Mock the faq items data
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(getFaqItemsResponse);
  testStore.dispatch(action);

  const updateSuccessResponse = {
    status: "success",
    message: "Faq Items updated successfully"
  };
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  const wrapper = mount(
    <Provider store={testStore}>
      <FaqScreen />
    </Provider>
  );
  // Now click on a faq item.
  wrapper
    .find(".wl-card")
    .at(0)
    .simulate("click");

  // change the question and click on update, we should have a update request.
  wrapper
    .find(".wl-faq-edit-item__textarea")
    .at(0)
    .simulate("change", {
      target: {
        value: "new question value?"
      }
    });
  wrapper
    .find(".wl-action-button--update")
    .at(0)
    .simulate("click");
  const postedData = JSON.parse(fetch.mock.calls[0][1].body);
  expect(postedData.faq_items[0].question).toEqual("new question value?");
  // Clear all the mocks.
  fetch.mockClear();

  // Enqueue a successful update response
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  // Now click on delete, should set the question to empty.
  wrapper
    .find(".wl-action-button--delete")
    .at(0)
    .simulate("click");
  const postedDeleteData = JSON.parse(fetch.mock.calls[0][1].body);
  expect(postedDeleteData.faq_items[0].question).toEqual("");
});

it("when the user opens the edit screen, should be able " + "to update / delete the answer", () => {
  // Mock the faq items data
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(getFaqItemsResponse);
  testStore.dispatch(action);

  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  const wrapper = mount(
    <Provider store={testStore}>
      <FaqScreen />
    </Provider>
  );
  // Now click on a faq item.
  wrapper
    .find(".wl-card")
    .at(0)
    .simulate("click");

  // change the question and click on update, we should have a update request.
  wrapper
    .find(".wl-faq-edit-item__textarea")
    .at(1)
    .simulate("change", {
      target: {
        value: "new answer value"
      }
    });
  wrapper
    .find(".wl-action-button--update")
    .at(2)
    .simulate("click");
  const postedData = JSON.parse(fetch.mock.calls[0][1].body);
  expect(postedData.faq_items[0].answer).toEqual("new answer value");
  // Clear all the mocks.
  fetch.mockClear();

  // Enqueue a successful update response
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  // Now click on delete, should set the question to empty.
  wrapper
    .find(".wl-action-button--delete")
    .at(2)
    .simulate("click");
  const postedDeleteData = JSON.parse(fetch.mock.calls[0][1].body);
  expect(postedDeleteData.faq_items[0].answer).toEqual("");
});

it(
  "When the question is added to input and add question is clicked then" +
    " it should send a request to add a new question",
  () => {
    const wrapper = mount(
      <Provider store={testStore}>
        <FaqScreen />
      </Provider>
    );
    wrapper
      .find(".wl-question-input-box")
      .at(0)
      .simulate("change", {
        target: {
          value: "This is a new question?"
        }
      });
    fetch.mockResponseOnce(JSON.stringify(createNewQuestionResponse));
    // click on the add question button.
    wrapper
      .find(".wl-add-question-button")
      .at(0)
      .simulate("click");

    const postedData = JSON.parse(fetch.mock.calls[0][1].body);
    expect(postedData.faq_items[0].question).toEqual("This is a new question?");
  }
);
