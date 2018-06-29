import React, { Component } from "react";
import ReactDOM from "react-dom";
import { createStore } from "redux";
import { Provider } from "react-redux";

import AddEntity from "./screens/Admin/screens/Edit/components/AddEntity";
import Treemap from "./screens/Admin/screens/Keywords/components/Treemap";
import renderTile from "./screens/Admin/screens/Keywords/renderTile";

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
        <div style={{ width: "250px" }}>
          <AddEntity />
        </div>
      </Provider>
    );
  }
}

ReactDOM.render(<App />, document.getElementById("button"));

// Treemap.
ReactDOM.render(
  <Treemap
    url="complete.json"
    width="1350"
    height="500"
    minTileWidth="150"
    minTileHeight="100"
    tileRenderCallback={renderTile}
  />,
  document.getElementById("treemap")
);
