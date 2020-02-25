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

beforeEach(() => {
  fetch.resetMocks();
});

it("should render without throwing error", () => {
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
  fetch.mockResponseOnce(JSON.stringify(getFaqItemsResponse));
  const wrapper = mount(
      <Provider store={store}>
        <FaqScreen />
      </Provider>
  );
  expect(wrapper.find('div.some-class')).to.have.lengthOf(3);
});
