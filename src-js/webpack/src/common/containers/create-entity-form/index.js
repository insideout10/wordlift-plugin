/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import CreateEntityForm from "../../components/create-entity-form";
import { addEntityRequest, createEntityForm } from "../../../classic-editor/components/AddEntity/actions";

const mapStateToProps = ({ createEntityForm }) => createEntityForm;

const mapDispatchToProps = dispatch => ({
  onCancel: () => dispatch(createEntityForm(false)),
  onSubmit: value => dispatch(addEntityRequest(value))
});

export default connect(mapStateToProps, mapDispatchToProps)(CreateEntityForm);
