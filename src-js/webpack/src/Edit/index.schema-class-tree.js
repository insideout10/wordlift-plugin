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
import SchemaClassTree from "wordlift-for-schemaorg/src/SchemaClassTree";

/**
 * Sort the provided data structure by name case insensitive.
 *
 * @since 3.20.0
 * @param {{name}[]} data An array of objects with the name field.
 * @returns {Object[]} The sorted array.
 */
const sortByNameCaseInsensitive = data =>
  data.sort((a, b) => {
    const nameA = a.name.toUpperCase();
    const nameB = b.name.toUpperCase();

    if (nameA < nameB) return -1;

    if (nameA > nameB) return 1;

    return 0;
  });

/**
 * A HOC component which loads the tree data from WordPress.
 *
 * @since 3.20.0
 * @param {Object} Component The target component.
 * @returns {Object} The HOC component.
 */
const withLoader = Component =>
  class extends React.Component {
    constructor(props) {
      super(props);

      // Bind the onData.
      this.onData = this.onData.bind(this);

      // Set the initial state.
      this.state = {
        // All the items in the tree.
        items: [],
        // The root items.
        roots: []
      };
    }

    /**
     * As soon as the component is mounted, load the data from WordPress.
     *
     * @since 3.20.0
     */
    componentDidMount() {
      wp.ajax
        .post("wl_schemaorg_class", {})
        .then(json => json["schemaClasses"])
        .then(sortByNameCaseInsensitive)
        .then(this.onData);
    }

    /**
     * Receive the list of tree items.
     *
     * @since 3.20.0
     * @param {{id, name, dashname, description, children}[]} items An array of tree item.
     */
    onData(items) {
      // Select `thing` as root.
      const roots = items.filter(item => "thing" === item.dashname);

      this.setState({
        // Set the items to the data.
        items,
        // Select `thing` as the root item.
        roots,
        // Map the roots to their id.
        open: roots.map(item => item.id)
      });
    }

    /**
     * @inheritDoc
     */
    render() {
      const { items, roots, open } = this.state;

      return (
        <Component {...this.props} items={items} roots={roots} open={open} />
      );
    }
  };

const DecoratedTree = withLoader(SchemaClassTree);

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
      onOpen={item => {}}
      onClose={item => {}}
      onSelect={item => {}}
      onDeselect={item => {}}
      leafDecorator={leafDecorator}
    />,
    element
  );
});
