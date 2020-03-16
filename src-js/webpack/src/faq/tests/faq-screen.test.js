/**
 * External dependencies.
 */
import React from "react";
import {configure, mount} from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import {Provider} from "react-redux";
/**
 * Internal dependencies.
 */
import {FAQ_INITIAL_STATE} from "../store";
import FaqScreen from "../components/faq-screen";
import {updateFaqItems} from "../actions";
import {transformAPIDataToUi} from "../sagas/filters";
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import {faqReducer} from "../reducers";
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
/** used for assertions in delete test **/
export const firstFaqItem = {
  question: "this is a question?e",
  answer: "this is answer.de",
  id: "1582622863"
};
export const createNewQuestionResponse = { status: "success", message: "Question successfully added.", id: 1582698289 };
export const updateSuccessResponse = {
  status: "success",
  message: "Faq Items updated successfully"
};
export const deleteResponse = {
  status: "success",
  message: "Faq Item successfully deleted."
};
const flushPromises = () => new Promise(setImmediate);

let testStore = null;
beforeEach(() => {
  fetch.resetMocks();
  const sagaMiddleware = createSagaMiddleware();
  testStore = createStore(faqReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
  global["_wlFaqSettings"] = {
    restUrl: "https://wordlift.localhost/index.php?rest_route=/wordlift/v1/faq",
    listBoxId: "wl-faq-meta-list-box",
    addQuestionText: "Add",
    nonce: "101a671e3d",
    postId: "436",
    invalidTagMessage: "Invalid tags {INVALID_TAGS} is present in answer",
    invalidWordCountMessage: "Answer word count must not exceed {ANSWER_WORD_COUNT_WARNING_LIMIT} words",
    updatingText: "updating",
    noFaqItemsText: "No faq items present, add one."
  };
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

it("test whether the user can update and delete the faq item question from the edit screen", async () => {
  /**
   * Lets add faq items to the store.
   */
  testStore.dispatch(updateFaqItems(transformAPIDataToUi(getFaqItemsResponse)));
  const wrapper = mount(
    <Provider store={testStore}>
      <FaqScreen />
    </Provider>
  );

  /**
   * step 1: click on a faq item.
   */
  wrapper
    .find(".wl-card")
    .at(0)
    .simulate("click");

  /**
   * step 2: edit the question info on the
   * text area in edit item screen.
   */
  wrapper
    .find(".wl-faq-edit-item--textarea")
    .at(0)
    .simulate("change", {
      target: {
        value: "foo question?"
      }
    });

  /**
   * step 3: click the update button, it should
   * send a request to API with the faq item info and
   * question should be foo question?, make a update response ready
   * since we should not receive fetch errors.
   */
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  fetch.mockResponseOnce(JSON.stringify(getFaqItemsResponse));
  // Reset all the mocks of the fetch call.
  wrapper
    .find(".wl-action-button--update")
    .at(0)
    .simulate("click");

  const postedData = JSON.parse(fetch.mock.calls[0][1].body);

  /**
   * step 4: when the update button is clicked then the API
   * call is made, assert the question value in the api call.
   */
  // we need to find only one faq item in the update request
  expect(postedData.faq_items.length).toEqual(1);
  /**
   * We need to have post id in the request
   */
  expect(postedData.post_id).not.toEqual(undefined);
  expect(postedData.post_id).toEqual("436");
  /**
   * We need to have these keys
   * 1.question
   * 2.answer
   * 3.previous_question_value
   * 4.previous_answer_value
   * in a single faq item.
   */
  const singleFaqItem = postedData.faq_items[0];
  expect(singleFaqItem.question).not.toEqual(undefined);
  expect(singleFaqItem.answer).not.toEqual(undefined);
  expect(singleFaqItem.previous_question_value).not.toEqual(undefined);
  expect(singleFaqItem.previous_answer_value).not.toEqual(undefined);

  /**
   * We need the updated value of question in the request, we have
   * updated the question in text area at step 2, we need to see the value
   * foo question? here.
   */
  expect(singleFaqItem.question).toEqual("foo question?");

  /**
   * Deletion of Faq item question.
   */
  /**
   * Step 1: click on the delete button in faq item question, before clicking enqueue
   * the delete response.
   */
  fetch.mockResponseOnce(JSON.stringify(deleteResponse));
  fetch.mockResponseOnce(JSON.stringify(getFaqItemsResponse));

  wrapper
    .find(".wl-action-button--delete")
    .at(0)
    .simulate("click");
  /**
   * Expect the request to contain the delete method, and the faq item to be deleted.
   */
  const deleteDataMethod = fetch.mock.calls[1][1].method;
  const deleteData = JSON.parse(fetch.mock.calls[1][1].body);
  /**
   * Method should be `DELETE`
   */
  expect(deleteDataMethod).toEqual("DELETE");
  // we need to find only one faq item in the delete request
  expect(deleteData.faq_items.length).toEqual(1);
  /**
   * We need to have these keys
   * 1.question
   * 2.answer
   * 3.field_to_be_deleted
   * */
  const deletedFaqItem = deleteData.faq_items[0];
  expect(deletedFaqItem.question).not.toEqual(undefined);
  expect(deletedFaqItem.answer).not.toEqual(undefined);
  expect(deletedFaqItem.field_to_be_deleted).not.toEqual(undefined);
  /**
   * Also expect the field_to_be_deleted should be question, because delete
   * is clicked on question.
   */
  expect(deletedFaqItem.field_to_be_deleted).toEqual("question");
});

it("test whether the user can update and delete the faq item answer from the edit screen", async () => {
  /**
   * Lets add faq items to the store.
   */
  testStore.dispatch(updateFaqItems(transformAPIDataToUi(getFaqItemsResponse)));
  const wrapper = mount(
    <Provider store={testStore}>
      <FaqScreen />
    </Provider>
  );

  /**
   * step 1: click on a faq item.
   */
  wrapper
    .find(".wl-card")
    .at(0)
    .simulate("click");

  /**
   * step 2: edit the answer info on the
   * text area in edit item screen.
   */
  wrapper
    .find(".wl-faq-edit-item--textarea")
    .at(1)
    .simulate("change", {
      target: {
        value: "foo answer"
      }
    });

  /**
   * step 3: click the update button, it should
   * send a request to API with the faq item info and
   * question should be foo question?, make a update response ready
   * since we should not receive fetch errors.
   */
  fetch.mockResponseOnce(JSON.stringify(updateSuccessResponse));
  fetch.mockResponseOnce(JSON.stringify(getFaqItemsResponse));
  // Reset all the mocks of the fetch call.
  wrapper
    .find(".wl-action-button--update")
    .at(2)
    .simulate("click");

  const postedData = JSON.parse(fetch.mock.calls[0][1].body);

  /**
   * step 4: when the update button is clicked then the API
   * call is made, assert the question value in the api call.
   */
  // we need to find only one faq item in the update request
  expect(postedData.faq_items.length).toEqual(1);

  /**
   * We need to have post id in the request
   */
  expect(postedData.post_id).not.toEqual(undefined);
  expect(postedData.post_id).toEqual("436");
  /**
   * We need to have these keys
   * 1.question
   * 2.answer
   * 3.previous_question_value
   * 4.previous_answer_value
   * in a single faq item.
   */
  const singleFaqItem = postedData.faq_items[0];
  expect(singleFaqItem.question).not.toEqual(undefined);
  expect(singleFaqItem.answer).not.toEqual(undefined);
  expect(singleFaqItem.previous_question_value).not.toEqual(undefined);
  expect(singleFaqItem.previous_answer_value).not.toEqual(undefined);

  /**
   * We need the updated value of question in the request, we have
   * updated the question in text area at step 2, we need to see the value
   * foo question? here.
   */
  expect(singleFaqItem.answer).toEqual("foo answer");

  /**
   * Deletion of Faq item question.
   */
  /**
   * Step 1: click on the delete button in faq item question, before clicking enqueue
   * the delete response.
   */
  fetch.mockResponseOnce(JSON.stringify(deleteResponse));
  fetch.mockResponseOnce(JSON.stringify(getFaqItemsResponse));

  wrapper
    .find(".wl-action-button--delete")
    .at(2)
    .simulate("click");
  /**
   * Expect the request to contain the delete method, and the faq item to be deleted.
   */
  const deleteDataMethod = fetch.mock.calls[1][1].method;
  const deleteData = JSON.parse(fetch.mock.calls[1][1].body);
  /**
   * Method should be `DELETE`
   */
  expect(deleteDataMethod).toEqual("DELETE");
  // we need to find only one faq item in the delete request
  expect(deleteData.faq_items.length).toEqual(1);
  /**
   * We need to have these keys
   * 1.question
   * 2.answer
   * 3.field_to_be_deleted
   * */
  const deletedFaqItem = deleteData.faq_items[0];
  expect(deletedFaqItem.question).not.toEqual(undefined);
  expect(deletedFaqItem.answer).not.toEqual(undefined);
  expect(deletedFaqItem.field_to_be_deleted).not.toEqual(undefined);
  /**
   * Also expect the field_to_be_deleted should be question, because delete
   * is clicked on question.
   */
  expect(deletedFaqItem.field_to_be_deleted).toEqual("answer");
});
