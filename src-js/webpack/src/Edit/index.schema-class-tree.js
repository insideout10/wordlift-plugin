/**
 * Schema Class Tree start-up.
 *
 * Load the {@link SchemaClassTree} on window load if the target element is
 * present on page.
 *
 * @since 3.20.0
 */
/*
 * External dependencies.
 */
import React from "react";
import ReactDOM from "react-dom";
import { SchemaClassTree } from "wordlift-for-schemaorg";

import withLoader from "./SchemaClassTree/withLoader";
import withProps from "./SchemaClassTree/withProps";

/*
 * `withLoader` provides us with the `open` property therefore goes after
 * `withProps`.
 */
const DecoratedTree = withLoader(
  withProps("open", "selected")(SchemaClassTree)
);

/**
 * Add the SchemaClassTree.
 *
 * @since 3.20.0
 */
window.addEventListener("load", () => {
  // Get the target element.
  const element = document.querySelector(
    "#taxonomy-wl_entity_type #wl-schema-class-tree"
  );

  // Bail out if the target element isn't found.
  if (null === element) {
    return;
  }

  // Set a reference to the WordLift's settings stored in the window instance.
  const settings = window["wlSettings"] || {};

  document
    .querySelectorAll("#wl_entity_typechecklist, #wl_entity_typechecklist-pop")
    .forEach(element =>
      element.addEventListener("click", e => {
        const target = e.target;
        console.log({
          id: e.target.id,
          checked: e.target.checked,
          value: e.target.value
        });
      })
    );

  /**
   * The Leaf Decorator syncs the checkbox selection with WordPress own
   * checkboxes.
   *
   * @since 3.20.0
   * @param Component The original component.
   * @returns {function(*): *} A decorated component.
   */
  const leafDecorator = Component => initialProps => {
    const { checked, item, onClick, ...props } = initialProps;
    // The new click handler.
    const handleClick = callback => () => {
      document
        // Query WordPress' own checkboxes.
        .querySelectorAll(
          `#in-wl_entity_type-${item.id}, #in-popular-wl_entity_type-${item.id}`
        )
        // Set them un/checked accordingly.
        .forEach(element => (element.checked = checked));

      // Finally call the original callback.
      callback();
    };
    return (
      <React.Fragment>
        <Component
          checked={checked}
          item={item}
          onClick={handleClick(onClick)}
          {...props}
        />
      </React.Fragment>
    );
  };

  // Render the Schema Class Tree.
  ReactDOM.render(
    <DecoratedTree
      selected={settings["entity_types"]}
      leafDecorator={leafDecorator}
    />,
    element
  );
});
