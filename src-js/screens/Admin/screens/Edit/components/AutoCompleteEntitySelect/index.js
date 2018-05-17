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

const getMainType = types => {
  for (let i = 0; i < window.wordlift.types.length; i++) {
    const type = window.wordlift.types[i];

    if (-1 < types.indexOf(type.uri)) return type.slug;
  }
  return "thing";
};

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
    onClick={() => {
      const item = props.item;
      const ctrl = EditPostWidgetController();
      ctrl.$apply(() => {
        ctrl.setCurrentEntity();
        ctrl.currentEntity.description = item.descriptions[0];
        ctrl.currentEntity.id = item.id;
        ctrl.currentEntity.images = item.images;
        ctrl.currentEntity.label = item.label;
        ctrl.currentEntity.mainType = getMainType(item.types);
        ctrl.currentEntity.types = item.types;
        ctrl.currentEntity.sameAs = item.sameAss;
        ctrl.storeCurrentEntity();
      });
    }}
  />
);

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
        <div
          style={{
            width: "250px",
            display: this.props.visible ? "block" : "none"
          }}
        >
          <Input
            type="text"
            defaultValue={this.props.filter}
            onChange={e => this.setFilter(e.target.value)}
          />
          <SelectContainer Item={ClickableSelectItem}>
            <CreateItem
              label={`Create ${this.props.filter}...`}
              onClick={() =>
                EditPostWidgetController().$apply(
                  EditPostWidgetController().setCurrentEntity(
                    undefined,
                    undefined,
                    this.props.filter
                  )
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
