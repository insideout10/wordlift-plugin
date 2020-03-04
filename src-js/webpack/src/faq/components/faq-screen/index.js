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
import { WlCard } from "../../../common/components/wl-card";

class FaqScreen extends React.Component {
  constructor(props) {
    super(props);
      this.updatingText = window["_wlFaqSettings"]["updatingText"]
  }
  /**
   * If the user chose a question then display it
   * in the edit mode, or show the faq list, and if a request
   * is ongoing, show the updating text.
   */
  renderComponentBasedOnState() {
    if (this.props.requestInProgress) {
      return (
        <WlCard alignCenter={true}>
          <h3>{this.updatingText}</h3>
        </WlCard>
      );
    } else if (this.props.selectedFaqId !== null) {
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
    return <React.Fragment>{this.renderComponentBasedOnState()}</React.Fragment>;
  }
}

export default connect(state => ({
  selectedFaqId: state.faqListOptions.selectedFaqId,
  faqItems: state.faqListOptions.faqItems,
  requestInProgress: state.faqListOptions.requestInProgress
}))(FaqScreen);
