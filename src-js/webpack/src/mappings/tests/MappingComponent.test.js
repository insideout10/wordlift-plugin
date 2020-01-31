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
const mockHttpServer = global.MockHttpServer;
configure({ adapter: new Adapter() });

test("when component rendered, check if it asks the server for mapping items", () => {
  // Supply no mapping items when the  component requests for data.
  global.MockHttpServer.enqueueResponse([]);
  const mockStore = store;
  // Now when rendering the component our ui will get this data by the mocked fetch method.
  const component = mount(
    <Provider store={mockStore}>
      <MappingComponent />
    </Provider>
  );
  expect(mockHttpServer.getLastCapturedRequest()).not.toBe(null);
  // that request should have a route for get_mappings
    expect(mockHttpServer.getLastCapturedRequest().url).toEqual(
        expect.stringContaining("wordlift/v1/mappings")
    )
});


