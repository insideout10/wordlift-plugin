/**
 * Apps: Search Rankings.
 *
 * The Search Rankings application.
 *
 * @since 3.20.0
 */
//region ## IMPORTS
// External dependencies.
import React from "react";
import { connect, Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import { Seq } from "immutable";

// Internal dependencies.
import EntityHeading from "./containers/EntityHeading";
import renderTile from "./renderTile";
import Treemap from "./components/Treemap";
import reducer, { clickTile } from "./actions";
import rootSaga from "./sagas";
import TableBody from "../../components/Table/TableBody";
import RankingPanel from "./containers/RankingPanel";
import RankingTableRow from "./components/RankingTableRow";
//endregion

//region ## SAGA
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));
sagaMiddleware.run(rootSaga);
//endregion

const actionWpUrl = window.ajaxurl + '?action=wl_search_rankings';
const tileClick = tile => store.dispatch(clickTile(tile));
const tileSelect = () => store.getState();
const rankingSelector = state =>
  null !== state.node ? state.node.score.rankings : [];
const RankingTableBody = connect(state => ({
  rows: new Seq(rankingSelector(state))
}))(TableBody);

const App = () => (
  <Provider store={store}>
    <div>
      <Treemap
        url={actionWpUrl}
        width="100%"
        height="500"
        minTileWidth="150"
        minTileHeight="100"
        tileRenderCallback={renderTile({ click: tileClick, select: tileSelect })}
      />
      <RankingPanel>
        <EntityHeading />
        <table className="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th scope="col">Query</th>
              <th scope="col" style={{ width: "40px" }}>
                Rank
              </th>
              <th scope="col">Ranking Page</th>
              <th scope="col" style={{ width: "100px" }}>
                Type
              </th>
              <th scope="col" style={{ width: "50px" }}>
                Score
              </th>
            </tr>
          </thead>
          <RankingTableBody TableRow={RankingTableRow} />
        </table>
      </RankingPanel>
    </div>
  </Provider>
);

export default App;
