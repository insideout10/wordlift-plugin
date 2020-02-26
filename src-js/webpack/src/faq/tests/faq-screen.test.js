/**
 * External dependencies.
 */
import React from "react";
import { shallow, mount, render, configure } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import { Provider } from "react-redux";
/**
 * Internal dependencies.
 */
import store from "../store";
import FaqScreen from "../components/faq-screen";
import {WlCard} from "../../common/components/wl-card";
import {updateFaqItems} from "../actions";
import {transformAPIDataToUi} from "../sagas/filters";
import FaqList from "../components/faq-list";
configure({ adapter: new Adapter() });

const getFaqItemsResponse = [
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

beforeEach(() => {
  fetch.resetMocks();
});

it("should render faq items when faq items given", () => {
  // Mock the faq items data
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(getFaqItemsResponse);
  store.dispatch(action)

  const wrapper = mount(
      <Provider store={store}>
        <FaqScreen />
      </Provider>
  );

  // Now we have dispatched the action, we should have 3 items in html
  expect(wrapper.find('.wl-card')).toHaveLength(3);
});
