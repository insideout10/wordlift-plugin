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
import { loadItemsRequest, reducer } from "./actions";
import saga from "./sagas";
import SelectItem from "./Item/SelectItem";
import CreateItem from "./Item/CreateItem";
import Input from "./Input";
import EditPostWidgetController from "../../angular/EditPostWidgetController";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware));

// Run the saga.
sagaMiddleware.run(saga);

class AutoCompleteEntitySelect extends Component {
  constructor(props) {
    super(props);

    this.setFilter = this.setFilter.bind(this);
  }

  loadItems(filter) {
    store.dispatch(loadItemsRequest(filter));
  }

  setFilter(value) {
    this.loadItems(value);
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
        <div style={{ width: "250px" }}>
          <Input
            type="text"
            defaultValue={this.props.filter}
            onChange={e => this.setFilter(e.target.value)}
          />
          <SelectContainer Item={SelectItem}>
            <CreateItem
              label={`Create ${this.props.filter}...`}
              onClick={() =>
                EditPostWidgetController().$apply(
                  EditPostWidgetController().setCurrentEntity(undefined, undefined, this.props.filter)
                )
              }
            />
          </SelectContainer>
        </div>
      </Provider>
    );
  }
}

export default AutoCompleteEntitySelect;
