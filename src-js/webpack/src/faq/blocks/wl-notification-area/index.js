/**
 * WlNotificationArea : it shows the notification on the sidebar.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
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
  render() {
    const { notificationMessage, notificationType, notificationCloseButtonClickedListener } = this.props;
    return (
      <React.Fragment>
        {"" !== notificationMessage && (
          <WlContainer>
            <WlColumn className={"wl-col--width-90"}>
              <div className={"wl-notification-area notice notice-" + notificationType + " is-dismissble"}>
                <p>{notificationMessage}</p>
              </div>
            </WlColumn>
            <WlColumn className={"wl-col--width-90"}>
              <span
                className="dashicons dashicons-dismiss wl-modal-header-icon"
                onClick={() => {
                  notificationCloseButtonClickedListener();
                }}
              />
            </WlColumn>
          </WlContainer>
        )}
      </React.Fragment>
    );
  }
}
export default WlNotificationArea;
