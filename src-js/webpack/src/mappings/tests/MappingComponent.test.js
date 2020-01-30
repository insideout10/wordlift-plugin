/**
 * @since 3.24.0
 *
 * Tests for Edit Component
 */

import React from "react";
import { Provider } from "react-redux";
import MappingComponent from "../components/mapping-component";
import store from "../store/index";
import { mount, configure } from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';

configure({ adapter: new Adapter() });
test("can render edit component", () => {
    global.MockHttpServer.enqueueResponse([{}])
  mount(
    <Provider store={store}>
      <MappingComponent />
    </Provider>
  );
});
