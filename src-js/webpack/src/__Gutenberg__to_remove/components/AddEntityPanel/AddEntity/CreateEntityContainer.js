/* globals wp */
/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";
/**
 * Internal dependencies.
 */
import CreateEntityForm from "./CreateEntityForm";
import { close, createEntityRequest, createEntityForm } from "./actions";

const mapStateToProps = ({ showCreate, value }) => ({ showCreate, value });

const mapDispatchToProps = dispatch => ({
  onCancel: () => dispatch(createEntityForm(false)),
  createEntity: value => dispatch(createEntityRequest(value))
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(CreateEntityForm);
