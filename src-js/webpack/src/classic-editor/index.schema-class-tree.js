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
import { SchemaClassTree } from "@wordlift/wordlift-for-schemaorg";

import withLoader from "./SchemaClassTree/withLoader";
import withProps from "./SchemaClassTree/withProps";

const syncWithWordPressTaxonomyMetabox = Component =>
  class extends React.Component {
    constructor(props) {
      super(props);

      this.getSelected = this.getSelected.bind(this);
      this.onData = this.onData.bind(this);
      this.handleSelected = this.handleSelected.bind(this);

      this.state = {
        selected: document.getElementById("wl_entity_typechecklist")
          ? // Get the selection from WordPress' own checklist.
            this.getSelected(document.getElementById("wl_entity_typechecklist"))
          : []
      };
    }
    componentDidMount() {
      document
        .querySelectorAll(
          "#wl_entity_typechecklist, #wl_entity_typechecklist-pop"
        )
        .forEach(element =>
          element.addEventListener("click", () =>
            this.onData(this.getSelected(element))
          )
        );
    }

    /**
     * Return an {@link Array} of selected items.
     *
     * @since 3.20.0
     * @returns {number[]} The Array of selected items' ids.
     */
    getSelected(element) {
      return Array.from(
        element.querySelectorAll("input[type='checkbox']:checked")
      ).map(item => parseInt(item.value));
    }
    onData(selected) {
      this.setState({ selected });
    }
    handleSelected(item, selected) {
      document
        // Query WordPress' own checkboxes.
        .querySelectorAll(
          `#in-wl_entity_type-${item.id}, #in-popular-wl_entity_type-${item.id}`
        )
        // Set them un/checked accordingly.
        .forEach(element => (element.checked = selected));

      this.setState(prevState => ({
        selected: selected
          ? prevState.selected.concat([item.id])
          : prevState.selected.filter(value => value !== item.id)
      }));
    }
    componentDidUpdate() {
      window.postMessage(
        {
          type: "syncWithWordPressTaxonomyMetabox.selected",
          payload: { selected: this.state.selected }
        },
        document.location.href
      );
    }
    render() {
      // Take out `selected` from the props.
      const { selected, ...props } = this.props;
      return (
        <Component
          {...props}
          selected={this.state.selected}
          onSelected={this.handleSelected}
        />
      );
    }
  };

/*
 * `withLoader` provides us with the `open` property therefore goes after
 * `withProps`.
 */
const DecoratedTree = withLoader(
  syncWithWordPressTaxonomyMetabox(
    withProps("open", "selected")(SchemaClassTree)
  )
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

  // // Set a reference to the WordLift's settings stored in the window instance.
  // const settings = window["wlSettings"] || {};

  // Render the Schema Class Tree.
  ReactDOM.render(<DecoratedTree />, element);
});
