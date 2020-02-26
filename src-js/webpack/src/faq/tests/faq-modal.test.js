import { configure, mount } from "enzyme";
import { Provider } from "react-redux";
import FaqScreen from "../components/faq-screen";
import React from "react";
import FaqModal from "../components/faq-modal";
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import { faqReducer } from "../reducers";
import { FAQ_INITIAL_STATE } from "../store";
import rootSaga from "../sagas";
import Adapter from "enzyme-adapter-react-16";
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
  fetch.resetMocks();
  const sagaMiddleware = createSagaMiddleware();
  testStore = createStore(faqReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
});

it("when only one question present then the modal should not open on " + "the text selection event", () => {
  // FAQ modal usually send a GET request on mounted, so provide a mock response.
  fetch.mockResponseOnce(
    JSON.stringify([
      {
        question: "this is a question without an answer?",
        answer: "",
        id: 1582622863
      }
    ])
  );
  const wrapper = mount(
    <Provider store={testStore}>
      <FaqModal />
    </Provider>
  );
});
