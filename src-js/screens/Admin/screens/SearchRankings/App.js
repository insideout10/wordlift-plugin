// External dependencies.
import React from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";

// Internal depedencies.
import renderTile from "./renderTile";
import Treemap from "./components/Treemap";
import reducer, { clickTile } from "./actions";
import rootSaga from "./sagas";

const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));
sagaMiddleware.run(rootSaga);

const tileClick = tile => store.dispatch(clickTile(tile));

const App = () => (
  <Provider store={store}>
    <div>
      <Treemap
        url="complete.json"
        width="1350"
        height="500"
        minTileWidth="150"
        minTileHeight="100"
        tileRenderCallback={renderTile({ click: tileClick })}
      />
      <h2>Entity</h2>
      <table>
        <thead>
          <tr>
            <th scope="col">Keyword</th>
            <th scope="col">Rank</th>
            <th scope="col">Ranking Page</th>
          </tr>
        </thead>
      </table>
    </div>
  </Provider>
);

export default App;
