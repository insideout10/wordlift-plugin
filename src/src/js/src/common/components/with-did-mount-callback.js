/**
 * This file defines the `withDidMountCallback` HOC function.
 *
 * The `withDidMountCallback` adds a callback to do something when a component
 * is first mounted.
 *
 * This is currently used in index.js in order to automatically start the analysis
 * when the Sidebar is first mounted.
 *
 * @see https://reactjs.org/docs/higher-order-components.html
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies.
 */
import React from "react";

/**
 * The `withDidMountCallback` function.
 *
 * @param WrapperComponent The wrapper component.
 * @param {Function} callback The callback function to call when the component is mounted.
 * @returns {Object} The higher-order component.
 */
const withDidMountCallback = (WrapperComponent, callback) => {
  return class extends React.Component {
    render() {
      return <WrapperComponent {...this.props} />;
    }

    componentDidMount() {
      callback();
    }
  };
};

export default withDidMountCallback;
