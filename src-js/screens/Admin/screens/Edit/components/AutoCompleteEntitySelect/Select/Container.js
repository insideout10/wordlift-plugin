/**
 * External dependencies.
 */
import { connect } from "react-redux";
import { List } from "immutable";

/**
 * Internal dependencies.
 */
import Select from "./index";

/**
 * @inheritDoc
 */
const mapStateToProps = state => {
  return {
    items: List(state.items)
  };
};

/**
 * Connect the `Select` with the `Container`.
 */
const Container = connect(mapStateToProps)(Select);

export default Container;
