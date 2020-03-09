import {configure, mount} from "enzyme";
import Adapter from "enzyme-adapter-react-16";
import {Provider} from "react-redux";
import React from "react";
import store from "../store/index"
import WlPostExcerpt from "../components/wl-post-excerpt";
configure({ adapter: new Adapter() });

let testStore = null

beforeAll(() => {
    testStore = store
    global["wp"] = jest.fn()
})

it("when the post excerpt component is rendered, should send a http request", () => {

    const wrapper = mount(
        <Provider store={testStore}>
            <WlPostExcerpt orText={"foo"}/>
        </Provider>
    );

})