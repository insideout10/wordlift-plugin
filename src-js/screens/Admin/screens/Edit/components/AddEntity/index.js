/**
 * External dependencies.
 */
import React, { Component } from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";

import ButtonContainer from "./ButtonContainer";
import EntitySelectContainer from "./EntitySelectContainer";
import WrapperContainer from "./WrapperContainer";
import Arrow from "../Arrow";
import saga from "./sagas";
import { reducer } from "./actions";
import EditorSelectionChangedEvent from "../../angular/EditorSelectionChangedEvent";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));

// store.dispatch(EditorSelectionChangedEvent());

// Run the saga.
sagaMiddleware.run(saga);

const AddEntity = () => (
  <Provider store={store}>
    <WrapperContainer>
      <ButtonContainer>
        Add ...
        <Arrow height="8px" color="white" />
      </ButtonContainer>
      <EntitySelectContainer />
    </WrapperContainer>
  </Provider>
);

export default AddEntity;
