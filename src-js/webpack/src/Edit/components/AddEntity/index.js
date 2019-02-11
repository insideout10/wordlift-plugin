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

// store passed from out for Gutenberg, or used from const for editor
const AddEntity = props => {
  window.store2 = props.store ? props.store : store;
  return (
    <Provider store={props.store ? props.store : store}>
      <WrapperContainer>
        <ButtonContainer>
          <Arrow height="8px" color="white" />
        </ButtonContainer>
        <EntitySelectContainer showCreate={props.showCreate} />
      </WrapperContainer>
    </Provider>
  );
};

export default AddEntity;
