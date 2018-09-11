import React from "react";

import sortByNameCaseInsensitive from "./sortByNameCaseInsensitive";

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
        roots: [],
        // The open items.
        open: []
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

export default withLoader;
