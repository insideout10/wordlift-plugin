/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies
 */
import Spinner from "./index";

export default Component => props => {
  return (props.loading && <Spinner running={true} />) || <Component {...props} />;
};
