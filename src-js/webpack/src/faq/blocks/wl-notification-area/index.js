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
  }
  render() {
    const { notificationMessage, notificationType, notificationCloseButtonClickedListener } = this.props;
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
