/**
 * @since 3.24.0
 *
 * Tests for Edit Component
 */

import React from "react";
import { Provider } from "react-redux";
import MappingComponent from "../components/mapping-component";
import store from "../store/index";
import { mappingsConfig } from "./setup";

beforeAll(() => {
    global.wlMappingsConfig = mappingsConfig;
    console.log(global)
});

test("can render edit component", () => {

  mount(
    <Provider store={store}>
      <MappingComponent />
    </Provider>
  );
});
