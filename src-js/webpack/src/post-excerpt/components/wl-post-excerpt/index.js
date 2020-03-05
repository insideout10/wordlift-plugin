/**
 * WlPostExcerpt shows the text area for the wordlift excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";
import "./index.scss";
import { WlPostExcerptButtonGroup } from "../wl-post-excerpt-button-group";
import WlPostExcerptLoadingScreen from "../wl-post-excerpt-loading-screen";

class WlPostExcerpt extends React.Component {
  renderConditionally() {
    if (this.props.isRequestInProgress) {
      return <WlPostExcerptLoadingScreen />;
    } else {
      return (
        <React.Fragment>
          <WlContainer>
            <WlColumn className={"wl-col--low-padding"}>
              <p>{this.props.orText}</p>
            </WlColumn>
          </WlContainer>
          <WlContainer fullWidth={true}>
            <textarea rows={3} className={"wl-post-excerpt--textarea"} />
          </WlContainer>
          <WlContainer fullWidth={true}>
            <WlPostExcerptButtonGroup />
          </WlContainer>
        </React.Fragment>
      );
    }
  }
  render() {
    return this.renderConditionally()
  }
}
// Define all the props used by this component.
WlPostExcerpt.propTypes = {
  orText: PropTypes.string
};

export default connect(state => ({
  isRequestInProgress: state.isRequestInProgress
}))(WlPostExcerpt);
