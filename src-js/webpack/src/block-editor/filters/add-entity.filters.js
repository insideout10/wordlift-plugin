/**
 * This file contains filters' hooks for the {@link AddEntity} component.
 *
 * The first is `wordlift.AddEntity.preWrapperContainer` to display notices.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import React from "react";
import { Provider } from "react-redux";

/**
 * WordPress dependencies
 */
import { addFilter } from "@wordpress/hooks";

/**
 * Internal dependencies
 */
import Notice from "./add-entity-notice-container";
import CreateEntityFormContainer from "../../common/containers/create-entity-form";

export default store => {
  // Hook to `wordlift.AddEntity.preWrapperContainer` in order to display notices.
  addFilter("wordlift.AddEntity.beforeWrapperContainer", "wordlift", values => values.concat(<Notice />));

  // Hook to `wordlift.AddEntity.preWrapperContainer` in order to display notices.
  addFilter("wordlift.AddEntity.afterWrapperContainer", "wordlift", values =>
    values.concat(
      <Provider store={store}>
        <CreateEntityFormContainer />
      </Provider>
    )
  );
};
