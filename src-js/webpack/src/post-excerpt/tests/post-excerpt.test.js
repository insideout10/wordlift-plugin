import { configure, mount } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import { Provider } from "react-redux";
import React from "react";
import { POST_EXCERPT_INITIAL_STATE } from "../store/index";
import WlPostExcerpt from "../components/wl-post-excerpt";
import { POST_EXCERPT_LOCALIZATION_OBJECT_KEY } from "../constants";
import WlPostExcerptLoadingScreen from "../components/wl-post-excerpt-loading-screen";
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import rootSaga from "../../post-excerpt/sagas/index";
import { reducer } from "../actions";
import { logger } from "redux-logger";
configure({ adapter: new Adapter() });

let testStore = null;
const postExcerptSuccessResponse = {
  post_excerpt: "this is a sample excerpt",
  from_cache: true,
  status: "success"
};

/**
 * Resolve all promises before making assertions, setImmediate is a non standard feature
 * See here https://stackoverflow.com/questions/44741102/how-to-make-jest-wait-for-all-asynchronous-code-to-finish-execution-before-expec
 * @return {*}
 */
const flushPromises = () => new Promise(setImmediate);

beforeAll(() => {
  global["wp"] = {};
  global["tinymce"] = {
    activeEditor: {
      getContent: () => {
        return "<p>foo</p>";
      }
    }
  };
  global[POST_EXCERPT_LOCALIZATION_OBJECT_KEY] = {
    generatingText: "Generating Excerpt..."
  };
});

beforeEach(() => {
  fetch.resetMocks();
  const sagaMiddleware = createSagaMiddleware();
  testStore = createStore(reducer, POST_EXCERPT_INITIAL_STATE, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
});

it("when the post excerpt component is rendered, should send a http request", () => {
  fetch.mockResponseOnce(JSON.stringify(postExcerptSuccessResponse));
  const wrapper = mount(
    <Provider store={testStore}>
      <WlPostExcerpt orText={"foo"} />
    </Provider>
  );
  const method = fetch.mock.calls[0][1].method;
  expect(method).toEqual("POST");
  const postData = JSON.parse(fetch.mock.calls[0][1].body);
  // we have supplied value foo via tinymce getcontent() method, see beforeEach() method
  expect(postData.post_body).toEqual("foo");
});

it("when the post excerpt component is rendered, should display the loading screen", () => {
  fetch.mockResponseOnce(JSON.stringify(postExcerptSuccessResponse));
  const wrapper = mount(
    <Provider store={testStore}>
      <WlPostExcerpt orText={"foo"} />
    </Provider>
  );
  expect(wrapper.find(WlPostExcerptLoadingScreen).exists()).toBeTruthy()

});

it("when the user clicks on the refresh button, the http ", async () => {
  // we are creating a mock element.
  fetch.mockResponseOnce(JSON.stringify(postExcerptSuccessResponse));
  const wrapper = mount(
      <Provider store={testStore}>
        <WlPostExcerpt orText={"foo"} />
      </Provider>
  );
  await flushPromises();
  wrapper.update()
  // so we will have the ui now instead of loading screen
  // click on the refresh button
  // enqueue a fake response before clicking on the button prevent error.
  fetch.mockResponseOnce(JSON.stringify(postExcerptSuccessResponse));
  wrapper.find('.wl-action-button--refresh').at(0).simulate('click')
  const method = fetch.mock.calls[0][1].method;
  expect(method).toEqual("POST");
  const postData = JSON.parse(fetch.mock.calls[0][1].body);
  // we have supplied value foo via tinymce getcontent() method, see beforeEach() method
  expect(postData.post_body).toEqual("foo");
})
