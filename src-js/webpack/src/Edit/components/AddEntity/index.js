/**
 * External dependencies
 */
import React from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import logger from "redux-logger";
import PropTypes from "prop-types";

/**
 * WordPress dependencies
 */
import { addAction } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import ButtonContainer from "./ButtonContainer";
import EntitySelectContainer from "./EntitySelectContainer";
import WrapperContainer from "./WrapperContainer";
import Arrow from "../Arrow";
import saga from "./sagas";
import { reducer, setValue } from "./actions";
import { SELECTION_CHANGED } from "../../../common/constants";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware, logger));

// Run the saga.
sagaMiddleware.run(saga);

addAction(SELECTION_CHANGED, "wordlift", ({ selection }) => store.dispatch(setValue(selection)));

const AddEntity = ({ selectEntity, showCreate }) => {
  return (
    <Provider store={store}>
      <WrapperContainer>
        <ButtonContainer>
          <Arrow height="8px" color="white" />
        </ButtonContainer>
        <EntitySelectContainer selectEntity={selectEntity} showCreate={showCreate} />
      </WrapperContainer>
    </Provider>
  );
};

AddEntity.propTypes = {
  selectEntity: PropTypes.func.isRequired,
  showCreate: PropTypes.bool.isRequired
};

export default AddEntity;
