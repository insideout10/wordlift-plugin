import React from "react";
import { shallow, mount, render, configure } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
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

import store from "../store";
import FaqScreen from "../components/faq-screen";

it("should render without throwing error", () => {
  fetch.mockResponseOnce(JSON.stringify({ data: '12345' }))
  render(<FaqScreen store={store} />);
});

