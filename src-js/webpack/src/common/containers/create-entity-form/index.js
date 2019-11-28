/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import CreateEntityForm from "../../components/create-entity-form";
import { createEntityForm } from "../../../__Gutenberg__to_remove/components/AddEntityPanel/AddEntity/actions";
import { addEntityRequest } from "../../../Edit/components/AddEntity/actions";

const mapStateToProps = ({ createEntityForm }) => createEntityForm;

const mapDispatchToProps = dispatch => ({
  onCancel: () => dispatch(createEntityForm(false)),
  onSubmit: value => dispatch(addEntityRequest(value))
});

export default connect(mapStateToProps, mapDispatchToProps)(CreateEntityForm);
