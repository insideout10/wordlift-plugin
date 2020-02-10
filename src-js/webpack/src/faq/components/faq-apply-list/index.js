/**
 * FaqApplyList shows a list of questions without answer.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";
import {WlCard} from "../../blocks/wl-card";
import Question from "../question";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import WlActionButton from "../wl-action-button";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {updateFaqItem} from "../../actions";
import {faqEditItemType} from "../faq-edit-item";

class FaqApplyList extends React.Component {
  constructor(props) {
    super(props);
    this.applyAnswerToQuestion = this.applyAnswerToQuestion.bind(this);
  }

  /**
   * Apply a answer to a question
   * @param id The id of the FAQ item
   */
  applyAnswerToQuestion(id) {
    const action = updateFaqItem();
    action.payload = {
      id: id,
      type: faqEditItemType.ANSWER,
      value: this.props.selectedAnswer
    };
    this.props.dispatch(action);
  }
  render() {
    return this.props.faqItems.filter(e => e.answer.length === 0).map(e => {
      return (
        <WlCard>
          <WlContainer>
            <WlColumn className={"wl-col--width-90"}>
              <Question question={e.question} />
            </WlColumn>
            <WlColumn className={"wl-col--width-10"}>
              <WlActionButton text={"apply"} className={"wl-action-button--primary"} onClickHandler={ () => {
                  this.applyAnswerToQuestion(e.id)
              }}/>
            </WlColumn>
          </WlContainer>
        </WlCard>
      );
    });
  }
}

export default connect(state => ({
  faqItems: state.faqListOptions.faqItems,
  // Mocking answer from text editor for now.
  selectedAnswer: "some answer"
}))(FaqApplyList);
