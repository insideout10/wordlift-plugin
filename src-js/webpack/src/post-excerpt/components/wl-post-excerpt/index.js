/**
 * WlPostExcerpt shows the text area for the wordlift excerpt.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";
import "./index.scss";
import { WlPostExcerptButtonGroup } from "../wl-post-excerpt-button-group";
import WlPostExcerptLoadingScreen from "../wl-post-excerpt-loading-screen";
import { requestPostExcerpt, setNotificationData } from "../../actions";
import { getPostContent, removeDefaultExcerptPanel } from "./helpers";
import WlNotificationArea from "../../../common/components/wl-notification-area";

const tinymce = global["tinymce"];

class WlPostExcerpt extends React.Component {
  constructor(props) {
    super(props);
    this.refreshExcerpt = this.refreshExcerpt.bind(this);
    this.useExcerpt = this.useExcerpt.bind(this);
    this.removeNotificationListener = this.removeNotificationListener.bind(this);
  }

  /**
   * Automatically fetch the post excerpt for the current
   * post body.
   */
  componentDidMount() {
    removeDefaultExcerptPanel();

    const content = getPostContent();

    if ("" !== content)
      this.props.dispatch(
        requestPostExcerpt({
          postBody: content
        })
      );
  }

  /**
   * Clear the notification message and type.
   */
  removeNotificationListener() {
    this.props.dispatch(
      setNotificationData({
        notificationMessage: "",
        notificationType: ""
      })
    );
  }

  /**
   * Refresh the excerpt by getting the new data.
   */
  refreshExcerpt() {
    const content = getPostContent();

    if ("" !== content)
      this.props.dispatch(
        requestPostExcerpt({
          postBody: getPostContent()
        })
      );
  }

  /**
   * Copy the contents of the wordlift post excerpt box to
   * wp default box.
   */
  useExcerpt() {
    // we get the wordpress text area id and set the text to it.
    const textarea = document.getElementById("excerpt");
    textarea.value = this.props.currentPostExcerpt;
  }

  renderConditionally() {
    if (this.props.isRequestInProgress) {
      return <WlPostExcerptLoadingScreen />;
    } else {
      return (
        <React.Fragment>
          <WlNotificationArea
            notificationMessage={this.props.notificationMessage}
            notificationType={this.props.notificationType}
            notificationCloseButtonClickedListener={this.removeNotificationListener}
          />
          <WlContainer>
            <WlColumn className={"wl-col--low-padding"}>
              <p>{this.props.orText}</p>
            </WlColumn>
          </WlContainer>
          <WlContainer fullWidth={true}>
            <textarea
              rows={3}
              className={"wl-post-excerpt--textarea"}
              value={this.props.currentPostExcerpt}
              onChange={() => {}}
            />
          </WlContainer>
          <WlContainer fullWidth={true}>
            <WlPostExcerptButtonGroup
              refreshHandler={() => this.refreshExcerpt()}
              useExcerptHandler={() => this.useExcerpt()}
            />
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
  currentPostExcerpt: state.currentPostExcerpt,
  notificationMessage: state.notificationMessage,
  notificationType: state.notificationType
}))(WlPostExcerpt);
