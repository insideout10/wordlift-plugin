/**
 * NotificationArea : it shows the notification on the ui.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";

export const NotificationArea = ({ notificationData }) => (
  <React.Fragment>
    {"" !== notificationData.message && (
      <div className={"wl-notice-custom-margin notice notice-" + notificationData.type + " is-dismissble"}>
        <p>{notificationData.message}</p>
      </div>
    )}
  </React.Fragment>
);
