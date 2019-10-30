/**
 * Applications: Classification Box.
 *
 * This is the main entry point for the Classification Box application published
 * from the `index.js` file inside a Redux `Provider`.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from "react";
import { ReactReduxContext } from "react-redux";

/**
 * Internal dependencies
 */
import Wrapper from "./Wrapper";
import Header from "../Header";
import VisibleEntityList from "../../containers/VisibleEntityList";
import Accordion from "../Accordion";
import AddEntity from "../../components/AddEntity";

const wlSettings = global["wlSettings"];
const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const App = (
  <Wrapper>
    <AddEntity showCreate={canCreateEntities} selectEntity={entity => console.debug(entity)} />
    <Accordion open={true} label="Content classification">
      <Header />
      <VisibleEntityList />
    </Accordion>
  </Wrapper>
);

// Finally export the `App`.
export default App;
