/**
 * External dependencies.
 */
import React, { Component } from "react";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";

/**
 * Internal dependencies.
 */
import SelectContainer from "./Select/Container";
import {
  addEntityRequest,
  createEntityRequest,
  loadItemsRequest,
  reducer
} from "./actions";
import saga from "./sagas";
import SelectItem from "./Item/SelectItem";
import CreateItem from "./Item/CreateItem";
import Input from "./Input";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));

// Run the saga.
sagaMiddleware.run(saga);

/**
 * A clickable `SelectItem`.
 *
 * @since 3.18.4
 *
 * @param props
 * @returns {*}
 * @constructor
 */
const ClickableSelectItem = props => (
  <SelectItem
    {...props}
    onClick={() => store.dispatch(addEntityRequest(props.item))}
  />
);

class AutoCompleteEntitySelect extends Component {
  // constructor(props) {
  //   super(props);
  //
  //   this.setFilter = this.setFilter.bind(this);
  // }

  loadItems(filter) {
    store.dispatch(loadItemsRequest(filter));
  }

  componentDidMount() {
    this.loadItems(this.props.filter);
  }

  componentDidUpdate(prevProps, prevState) {
    if (this.props.filter !== prevProps.filter)
      this.loadItems(this.props.filter);
  }

  render() {
    return (
      <Provider store={store}>
        <div
          style={{
            width: "250px",
            display: this.props.visible ? "block" : "none"
          }}
        >
          <Input
            type="text"
            defaultValue={this.props.filter}
            onChange={e => this.loadItems(e.target.value)}
          />
          <SelectContainer Item={ClickableSelectItem}>
            <CreateItem
              label={`Create ${this.props.filter}...`}
              onClick={() =>
                store.dispatch(createEntityRequest(this.props.filter))
              }
            />
          </SelectContainer>
        </div>
      </Provider>
    );
  }
}

export default AutoCompleteEntitySelect;
