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
import { requestPostExcerpt } from "../../actions";

class WlPostExcerpt extends React.Component {
  constructor(props) {
    super(props);
    this.refreshExcerpt = this.refreshExcerpt.bind(this);
    this.useExcerpt = this.useExcerpt.bind(this);
  }

  /**
   * Refresh the excerpt by getting the new data.
   */
  refreshExcerpt() {
    console.log(requestPostExcerpt);
  }

  /**
   * Copy the contents of the wordlift post excerpt box to
   * wp default box.
   */
  useExcerpt() {
    console.log(this.props.currentPostExcerpt);
  }

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
            <textarea rows={3} className={"wl-post-excerpt--textarea"} value={this.props.currentPostExcerpt} />
          </WlContainer>
          <WlContainer fullWidth={true}>
            <WlPostExcerptButtonGroup refreshHandler={this.refreshExcerpt} useExcerptHandler={this.useExcerpt} />
          </WlContainer>
        </React.Fragment>
      );
    }
  }
  render() {
    return this.renderConditionally();
  }
}
// Define all the props used by this component.
WlPostExcerpt.propTypes = {
  orText: PropTypes.string
};

export default connect(state => ({
  isRequestInProgress: state.isRequestInProgress,
  currentPostExcerpt: state.currentPostExcerpt
}))(WlPostExcerpt);
