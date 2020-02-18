/**
 * FaqScreen for showing the list of questions.
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
import FaqList from "../faq-list";
import "./index.scss";
import FaqEditItem, { faqEditItemType } from "../faq-edit-item";
import FaqEditItemCloseButton from "../faq-edit-item-close-button";
import { updateFaqModalVisibility, updateNotificationArea } from "../../actions";
import WlNotificationArea from "../../blocks/wl-notification-area";

class FaqScreen extends React.Component {
  constructor(props) {
    super(props);
    this.removeNotificationListener = this.removeNotificationListener.bind(this);
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
  /**
   * If the user chose a question then display it
   * in the edit mode, or show the faq list.
   */
  renderComponentBasedOnState() {
    if (this.props.selectedFaqId !== null) {
      const selectedFaqIndex = this.props.faqItems.map(e => e.id).indexOf(this.props.selectedFaqId);
      const selectedFaqItem = this.props.faqItems[selectedFaqIndex];
      return (
        <React.Fragment>
          <FaqEditItemCloseButton />
          <FaqEditItem
            title={"Question"}
            value={selectedFaqItem.question}
            id={this.props.selectedFaqId}
            type={faqEditItemType.QUESTION}
          />
          <br />
          <FaqEditItem
            title={"Answer"}
            value={selectedFaqItem.answer}
            id={this.props.selectedFaqId}
            type={faqEditItemType.ANSWER}
          />
        </React.Fragment>
      );
    } else {
      return (
        <React.Fragment>
          <FaqList />
        </React.Fragment>
      );
    }
  }
  render() {
    return (
      <React.Fragment>
        <WlNotificationArea
          notificationMessage={this.props.notificationMessage}
          notificationType={this.props.notificationType}
          notificationCloseButtonClickedListener={this.removeNotificationListener}
          autoHide = {true}
        />
        {this.renderComponentBasedOnState()}
      </React.Fragment>
    );
  }
}

export default connect(state => ({
  selectedFaqId: state.faqListOptions.selectedFaqId,
  faqItems: state.faqListOptions.faqItems,
  notificationMessage: state.faqNotificationArea.notificationMessage,
  notificationType: state.faqNotificationArea.notificationType
}))(FaqScreen);
