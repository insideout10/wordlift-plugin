/**
 * @since 3.24.0
 *
 * Tests for Edit Component
 */

import React from "react";
import { Provider } from "react-redux";
import MappingComponent from "../components/mapping-component";
import store from "../store/index";
import { shallow, mount, configure } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import MappingComponentHelper from "../components/mapping-component/mapping-component-helper";
import { MAPPING_LIST_CHANGED_ACTION } from "../actions/actions";
import { ACTIVE_CATEGORY, TRASH_CATEGORY } from "../components/category-component";
const mockHttpServer = global.MockHttpServer;
configure({ adapter: new Adapter() });

beforeEach(() => {
  global.MockHttpServer.resetServer();
  global.MockHttpServer.enqueueResponse([]);
});

test("when component rendered, check if it asks the server for mapping items", () => {
  // Now when rendering the component our ui will get this data by the mocked fetch method.
  mount(
    <Provider store={store}>
      <MappingComponent />
    </Provider>
  );
  expect(mockHttpServer.getLastCapturedRequest()).not.toBe(null);
  // that request should have a route for get_mappings
  expect(mockHttpServer.getLastCapturedRequest().url).toEqual(expect.stringContaining("wordlift/v1/mappings"));
});

test("component should send request correctly when we want set trash category to the mapping item", () => {
  const mockStore = store;
  const mappingItems = [
    {
      mappingId: 1,
      mappingTitle: "foo",
      mappingStatus: ACTIVE_CATEGORY,
      isSelected: false
    }
  ];
  MAPPING_LIST_CHANGED_ACTION.payload = {
    value: mappingItems
  };
  mockStore.dispatch(MAPPING_LIST_CHANGED_ACTION);
  // Now when rendering the component we will have a mapping item.
  const wrapper = mount(
    <Provider store={mockStore}>
      <MappingComponent />
    </Provider>
  );
  // We enqueue a another empty response before simulating click.
  mockHttpServer.enqueueResponse([]); // to handle put event
  mockHttpServer.enqueueResponse([]); // to handle get mapping event
  wrapper.find("span.trash > a").simulate("click");
  // when this click is made, a put request should be sent to API, with out mapping item.
  const request = mockHttpServer.getLastCapturedRequest();
  expect(request.data.method).toEqual("PUT");
  const expectedMappingItemsArray = MappingComponentHelper.applyApiFilters(mappingItems);
  expectedMappingItemsArray[0].mapping_status = TRASH_CATEGORY;
  expect(request.data.body).toEqual(JSON.stringify({ mapping_items: expectedMappingItemsArray }));
});

test("component should send request correctly when we want clone the mapping item", () => {
  const mockStore = store;
  const mappingItems = [
    {
      mappingId: 1,
      mappingTitle: "foo",
      mappingStatus: ACTIVE_CATEGORY,
      isSelected: false
    }
  ];
  MAPPING_LIST_CHANGED_ACTION.payload = {
    value: mappingItems
  };
  mockStore.dispatch(MAPPING_LIST_CHANGED_ACTION);
  // Now when rendering the component we will have a mapping item.
  const wrapper = mount(
    <Provider store={mockStore}>
      <MappingComponent />
    </Provider>
  );
  // We enqueue a another empty response before simulating click.
  mockHttpServer.enqueueResponse([]); // to handle put event
  mockHttpServer.enqueueResponse([]); // to handle get mapping event
  wrapper.find("span.wl-clone > a").simulate("click");
  // when this click is made, a put request should be sent to API, with out mapping item.
  const request = mockHttpServer.getLastCapturedRequest();
  expect(request.data.method).toEqual("POST");
  expect(request.url).toEqual(expect.stringContaining("/clone"));
  const expectedMappingItemsArray = MappingComponentHelper.applyApiFilters(mappingItems);
  expect(request.data.body).toEqual(JSON.stringify({ mapping_items: expectedMappingItemsArray }));
});

test("component should send request correctly when we want set active category to the trash mapping item", () => {
  const mockStore = store;
  const mappingItems = [
    {
      mappingId: 1,
      mappingTitle: "foo",
      mappingStatus: TRASH_CATEGORY,
      isSelected: false
    }
  ];
  MAPPING_LIST_CHANGED_ACTION.payload = {
    value: mappingItems
  };
  mockStore.dispatch(MAPPING_LIST_CHANGED_ACTION);
  // Now when rendering the component we will have a mapping item.
  const wrapper = mount(
    <Provider store={mockStore}>
      <MappingComponent />
    </Provider>
  );
  // We enqueue a another empty response before simulating click.
  mockHttpServer.enqueueResponse([]); // to handle put event
  mockHttpServer.enqueueResponse([]); // to handle get mapping event
  // switch the category to the trash.
  wrapper
    .find("span.wl-category-title > a")
    .at(0)
    .simulate("click");
  wrapper.find("span.restore > a").simulate("click");
  // when this click is made, a put request should be sent to API, with out mapping item.
  const request = mockHttpServer.getLastCapturedRequest();
  expect(request.data.method).toEqual("PUT");
  const expectedMappingItemsArray = MappingComponentHelper.applyApiFilters(mappingItems);
  expectedMappingItemsArray[0].mapping_status = ACTIVE_CATEGORY;
  expect(request.data.body).toEqual(JSON.stringify({ mapping_items: expectedMappingItemsArray }));
});
