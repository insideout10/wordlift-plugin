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

const syncWithWordPressTaxonomyMetabox = Component =>
  class extends React.Component {
    constructor(props) {
      super(props);

      this.onData = this.onData.bind(this);
      this.handleSelected = this.handleSelected.bind(this);

      this.state = {
        selected: props.selected || []
      };
    }
    componentDidMount() {
      document
        .querySelectorAll(
          "#wl_entity_typechecklist, #wl_entity_typechecklist-pop"
        )
        .forEach(element =>
          element.addEventListener("click", () =>
            this.onData(
              Array.from(
                element.querySelectorAll("input[type='checkbox']:checked")
              ).map(item => parseInt(item.value))
            )
          )
        );
    }
    onData(selected) {
      this.setState({ selected });
    }
    handleSelected(item, selected) {
      console.log("handleSelected", { item, selected });
      document
        // Query WordPress' own checkboxes.
        .querySelectorAll(
          `#in-wl_entity_type-${item.id}, #in-popular-wl_entity_type-${item.id}`
        )
        // Set them un/checked accordingly.
        .forEach(element => (element.checked = selected));
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

  // Set a reference to the WordLift's settings stored in the window instance.
  const settings = window["wlSettings"] || {};

  // /**
  //  * The Leaf Decorator syncs the checkbox selection with WordPress own
  //  * checkboxes.
  //  *
  //  * @since 3.20.0
  //  * @param Component The original component.
  //  * @returns {function(*): *} A decorated component.
  //  */
  // const leafDecorator = Component => initialProps => {
  //   const { checked, item, onClick, ...props } = initialProps;
  //   // The new click handler.
  //   const handleClick = callback => () => {
  //     document
  //       // Query WordPress' own checkboxes.
  //       .querySelectorAll(
  //         `#in-wl_entity_type-${item.id}, #in-popular-wl_entity_type-${item.id}`
  //       )
  //       // Set them un/checked accordingly.
  //       .forEach(element => (element.checked = checked));
  //
  //     // Finally call the original callback.
  //     callback();
  //   };
  //   return (
  //     <React.Fragment>
  //       <Component
  //         checked={checked}
  //         item={item}
  //         onClick={handleClick(onClick)}
  //         {...props}
  //       />
  //     </React.Fragment>
  //   );
  // };

  // Render the Schema Class Tree.
  ReactDOM.render(
    <DecoratedTree selected={settings["entity_types"]} />,
    element
  );
});
