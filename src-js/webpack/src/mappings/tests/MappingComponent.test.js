/**
 * @since 3.24.0
 *
 * Tests for Edit Component
 */

import React from "react";
import { Provider } from "react-redux";
import MappingComponent from "../components/mapping-component";
import store from "../store/index";
import { mount, configure } from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import MappingComponentHelper from "../components/mapping-component/mapping-component-helper";

configure({ adapter: new Adapter() });
test("can render edit component", () => {
  global.MockHttpServer.enqueueResponse([{}]);
  mount(
    <Provider store={store}>
      <MappingComponent />
    </Provider>
  );
});

test("when given mapping items ui works correctly", () => {
  const mockResponse = MappingComponentHelper.applyApiFilters([
    {
      mapping_id: "11",
      mapping_title: "item 1",
      mapping_status: "active"
    },
    {
      mapping_id: "12",
      mapping_title: "item 2",
      mapping_status: "active"
    },
    {
      mapping_id: "13",
      mapping_title: "item 3",
      mapping_status: "trash"
    }
  ]);
  global.MockHttpServer.enqueueResponse([mockResponse]);
    // Now when rendering the component our ui will get this data by the mocked fetch method.
    const component = mount(
        <Provider store={store}>
            <MappingComponent />
        </Provider>
    );
    console.log(store.getState())

});
