/**
 * External dependencies
 */
import React, { Fragment } from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import logger from "redux-logger";
import PropTypes from "prop-types";

/**
 * WordPress dependencies
 */
import { addAction, applyFilters } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import ButtonContainer from "./ButtonContainer";
import EntitySelectContainer from "./EntitySelectContainer";
import WrapperContainer from "./WrapperContainer";
import Arrow from "../Arrow";
import saga from "./sagas";
import { addEntitySuccess, close, reducer, setValue } from "./actions";
import { SELECTION_CHANGED } from "../../../common/constants";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware, logger));

// Run the saga.
sagaMiddleware.run(saga);

// Receives events that the selection has changed in the editor.
addAction(SELECTION_CHANGED, "wordlift", ({ selection }) => store.dispatch(setValue(selection)));

// Receives events that an entity has been successfully added.
//
// These events are raised from the Block Editor (block-editor/stores/sagas) and are required to update the AddEntity
// state.
addAction("wordlift.addEntitySuccess", "wordlift", () => store.dispatch(addEntitySuccess()));

// Temporary hack to allow 3rd parties to close the Entity Select.
addAction("unstable_wordlift.closeEntitySelect", "wordlift", () => store.dispatch(close()));

const AddEntity = ({ createEntity, selectEntity, showCreate }) => {
  return (
    <Provider store={store}>
      <Fragment>
        {// Allow 3rd parties to hook and add additional components.
        applyFilters("wordlift.AddEntity.beforeWrapperContainer", [])}
        <WrapperContainer>
          <ButtonContainer>
            <Arrow height="8px" color="white" />
          </ButtonContainer>
          <EntitySelectContainer createEntity={createEntity} selectEntity={selectEntity} showCreate={showCreate} />
        </WrapperContainer>
        {// Allow 3rd parties to hook and add additional components.
        applyFilters("wordlift.AddEntity.afterWrapperContainer", [])}
      </Fragment>
    </Provider>
  );
};

AddEntity.propTypes = {
  selectEntity: PropTypes.func.isRequired,
  showCreate: PropTypes.bool.isRequired
};

export default AddEntity;
