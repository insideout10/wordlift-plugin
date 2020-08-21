/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import CreateEntityForm from "../../components/create-entity-form";
import { addEntityRequest } from "../../../Edit/components/AddEntity/actions";
import { createEntityClose } from "./actions";

const mapStateToProps = ({ createEntityForm }) => createEntityForm;

const mapDispatchToProps = dispatch => ({
  onCancel: () => dispatch(createEntityClose()),
  onSubmit: value => dispatch(addEntityRequest(value))
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(CreateEntityForm);
