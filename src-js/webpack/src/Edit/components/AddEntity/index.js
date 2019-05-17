/**
 * External dependencies.
 */
import React from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import logger from "redux-logger";

import ButtonContainer from "./ButtonContainer";
import EntitySelectContainer from "./EntitySelectContainer";
import WrapperContainer from "./WrapperContainer";
import Arrow from "../Arrow";
import saga from "./sagas";
import { reducer } from "./actions";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware, logger));

// Run the saga.
sagaMiddleware.run(saga);

const AddEntity = ({ showCreate }) => {
  window.store2 = store;
  return (
    <Provider store={store}>
      <WrapperContainer>
        <ButtonContainer>
          <Arrow height="8px" color="white" />
        </ButtonContainer>
        <EntitySelectContainer showCreate={showCreate} />
      </WrapperContainer>
    </Provider>
  );
};

export default AddEntity;
