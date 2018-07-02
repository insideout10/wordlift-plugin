import React from "react";
import renderTile from "./renderTile";
import Treemap from "./components/Treemap";


const App = () => (
  <div>
    <Treemap
      url="complete.json"
      width="1350"
      height="500"
      minTileWidth="150"
      minTileHeight="100"
      tileRenderCallback={renderTile}
    />
    <h2>Entity {{}}</h2>
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
);

export default App;
