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
import FaqList from "../faq-list";
import FaqEditItem from "../faq-item-edit-item";

class FaqScreen extends React.Component {
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
          <FaqEditItem title={"Question"} value={selectedFaqItem.question}/>
          <br />
          <FaqEditItem title={"Answer"} value={selectedFaqItem.answer} />
        </React.Fragment>
      );
    } else {
      return <FaqList />;
    }
  }
  render() {
    return <React.Fragment>{this.renderComponentBasedOnState()}</React.Fragment>;
  }
}

export default connect(state => ({
  selectedFaqId: state.selectedFaqId,
  faqItems: state.faqItems
}))(FaqScreen);
