/**
 * @since 3.24.0
 *
 * Tests for Property List Item Component
 */

import React from "react";
import Adapter from "enzyme-adapter-react-16";
import { shallow, configure, render } from "enzyme";
configure({ adapter: new Adapter() });
import PropertyListItemComponent from "../components/PropertyListItemComponent";

test("can render property list item component", () => {
  shallow(<PropertyListItemComponent />);
});

test("given property text rendering properly", () => {
  const component = shallow(<PropertyListItemComponent propertyText="foo" />);
  expect(component.find(".wl-property-list-item-title").props().children).toEqual("foo");
});
