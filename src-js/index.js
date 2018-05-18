import React, { Component } from "react";
import ReactDOM from "react-dom";
import { createStore } from "redux";
import { Provider } from "react-redux";

import "./screens/Admin/screens/Keywords";
import Button from "./screens/Admin/screens/Edit/components/Button";
import AddEntitySelect from "./screens/Admin/screens/Edit/containers/AddEntitySelect";
import AddEntity from "./screens/Admin/screens/Edit/components/AddEntity";

class App extends Component {
  constructor(props) {
    super(props);

    this.state = { editor: { selection: "Word" } };
    this.store = createStore((state, action) => {
      switch (action.type) {
        case "TEXT_CHANGE":
          return Object.assign({}, state, {
            editor: { selection: action.payload }
          });
        default:
          return state;
      }
    }, this.state);
  }

  render() {
    return (
      <Provider store={this.store}>
        <AddEntity />
      </Provider>
    );
  }
}

ReactDOM.render(<App />, document.getElementById("button"));
