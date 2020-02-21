/**
 * FaqModal shows the apply list.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */
/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";
/**
 * Internal dependencies.
 */
import { requestGetFaqItems, updateFaqModalVisibility, updateNotificationArea } from "../../actions";
import "./index.scss";
import { WlModal } from "../../blocks/wl-modal";
import { WlModalHeader } from "../../blocks/wl-modal/wl-modal-header";
import { WlModalBody } from "../../blocks/wl-modal/wl-modal-body";
import FaqApplyList from "../faq-apply-list";
import { WlBgModal } from "../../blocks/wl-bg-modal";
import WlNotificationArea from "../../blocks/wl-notification-area";

class FaqModal extends React.Component {
  componentDidMount() {
    this.props.dispatch(requestGetFaqItems());
  }
  /**
   * Run this listener once the close button is clicked
   */
  removeNotificationListener() {
    const action = updateNotificationArea();
    action.payload = {
      notificationMessage: "",
      notificationType: ""
    };
    this.props.dispatch(action);
  }
  render() {
    return (
      <React.Fragment>
        <WlNotificationArea
          notificationMessage={this.props.notificationMessage}
          notificationType={this.props.notificationType}
          notificationCloseButtonClickedListener={this.removeNotificationListener}
          autoHide={true}
        />
        <WlBgModal shouldOpenModal={this.props.isModalOpened}>
          <WlModal shouldOpenModal={this.props.isModalOpened}>
            <WlModalHeader
              title={"WordLift FAQ"}
              description={"Apply this answer to a question"}
              modalCloseClickedListener={() => {
                const action = updateFaqModalVisibility();
                action.payload = false;
                this.props.dispatch(action);
              }}
            />
            <WlModalBody>
              <FaqApplyList />
            </WlModalBody>
          </WlModal>
        </WlBgModal>
      </React.Fragment>
    );
  }
}
export default connect(state => ({
  isModalOpened: state.faqModalOptions.isModalOpened,
  notificationMessage: state.faqNotificationArea.notificationMessage,
  notificationType: state.faqNotificationArea.notificationType
}))(FaqModal);
