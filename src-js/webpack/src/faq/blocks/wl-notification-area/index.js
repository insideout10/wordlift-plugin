/**
 * WlNotificationArea : it shows the notification on the sidebar.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.26.0
 */

/**
 * External dependencies.
 */
import React from "react";
import PropTypes from "prop-types";
/**
 * Internal dependencies.
 */
import "./index.scss";
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";

class WlNotificationArea extends React.Component {
  constructor(props) {
    super(props);
    this.state = { ...this.props };
    console.log(this.state)

    this.hideNotificationIfTimeoutEnabled()
  }
  hideNotificationIfTimeoutEnabled() {
    this.state.timeout = this.props.timeout || 2000;
    this.state.autoHide = this.props.autoHide || false;
    if ( this.state.autoHide ) {
      setTimeout( () => {
        this.state.notificationMessage = ""
      }, this.state.timeout)
    }
  }
  render() {
    const { notificationMessage, notificationType, notificationCloseButtonClickedListener } = this.state;
    return (
      <React.Fragment>
        {"" !== notificationMessage && (
          <div className={"wl-notification-area"}>
            <div className={"notice notice-" + notificationType + " is-dismissble"}>
              <WlContainer>
                <WlColumn className={"wl-col--width-90"}>
                  <p>{notificationMessage}</p>
                </WlColumn>
                <WlColumn className={"wl-col--width-10"}>
                  <span
                    className="dashicons dashicons-dismiss wl-notification-area__close-button"
                    onClick={() => {
                      notificationCloseButtonClickedListener();
                    }}
                  />
                </WlColumn>
              </WlContainer>
            </div>
          </div>
        )}
      </React.Fragment>
    );
  }
}
export default WlNotificationArea;
