// External dependencies.
import React from "react";
import { connect, Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import { Seq } from "immutable";
import numeral from "numeral";

// Internal dependencies.
import EntityHeading from "./containers/EntityHeading";
import renderTile from "./renderTile";
import Treemap from "./components/Treemap";
import reducer, { clickTile } from "./actions";
import rootSaga from "./sagas";
import TableBody from "../../components/Table/TableBody";
import TableRow from "../../components/Table/TableRow";
import TableDataCell from "../../components/Table/TableDataCell";

const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));
sagaMiddleware.run(rootSaga);

const tileClick = tile => store.dispatch(clickTile(tile));

const rankingSelector = state =>
  null !== state.node ? state.node.score.rankings : [];
const RankingTableBody = connect(state => ({
  rows: new Seq(rankingSelector(state))
}))(TableBody);

const RankingTableRow = ({ row: { keyword, rank, url, type, weight } }) => (
  <TableRow>
    <TableDataCell>
      <strong>{keyword}</strong>
    </TableDataCell>
    <TableDataCell style={{ textAlign: "right" }}>{rank}</TableDataCell>
    <TableDataCell>{url}</TableDataCell>
    <TableDataCell>{type}</TableDataCell>
    <TableDataCell>{numeral(weight).format("0.000")}</TableDataCell>
  </TableRow>
);

const App = () => (
  <Provider store={store}>
    <div>
      <Treemap
        url="complete.json"
        width="100%"
        height="500"
        minTileWidth="150"
        minTileHeight="100"
        tileRenderCallback={renderTile({ click: tileClick })}
      />
      <EntityHeading />
      <table className="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th scope="col">Keyword</th>
            <th scope="col" style={{ width: "40px" }}>
              Rank
            </th>
            <th scope="col">Ranking Page</th>
            <th scope="col" style={{ width: "100px" }}>
              Type
            </th>
            <th scope="col" style={{ width: "50px" }}>
              Weight
            </th>
          </tr>
        </thead>
        <RankingTableBody TableRow={RankingTableRow} />
      </table>
    </div>
  </Provider>
);

export default App;
