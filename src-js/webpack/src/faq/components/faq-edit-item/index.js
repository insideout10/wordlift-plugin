/**
 * FaqEditItem for the faq item.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * external dependencies
 */
import React from "react";
import { connect } from "react-redux";
/**
 * Internal dependencies.
 */
import FaqEditButtonGroup from "../faq-edit-button-group";
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";
import PropTypes from "prop-types";
import { updateFaqItem } from "../../actions";
import "./index.scss";
import { showWarningIfAnswerWordCountExceedsLimit, showWarningIfInvalidHTMLTagPresentInAnswer } from "./helpers";

export const faqEditItemType = {
  ANSWER: "ANSWER",
  QUESTION: "QUESTION"
};
// When the word count exceeds this limit a warning should be displayed for answer.
export const ANSWER_WORD_COUNT_WARNING_LIMIT = 50;

class FaqEditItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      textAreaValue: this.props.value
    };
    this.changeValueOnUserType = this.changeValueOnUserType.bind(this);
    this.updateFaqEditItem = this.updateFaqEditItem.bind(this);
    this.deleteFaqItem = this.deleteFaqItem.bind(this);
  }
  updateFaqEditItem(textAreaValue = null) {
    const action = updateFaqItem();
    action.payload = {
      id: this.props.id,
      type: this.props.type,
      value: this.state.textAreaValue
    };
    if (textAreaValue !== null) {
      action.payload.value = textAreaValue;
    }
    this.props.dispatch(action);
  }
  deleteFaqItem() {
    // Set the state, this is asynchronous, so we pass a default argument to action creator
    this.setState({ textAreaValue: "" });
    this.updateFaqEditItem("");
  }
  changeValueOnUserType(event) {
    this.setState({
      textAreaValue: event.target.value
    });
  }
  render() {
    return (
      <React.Fragment>
        <b>{this.props.title}</b>
        <br />
        <WlContainer>
          <WlColumn className={"wl-col--width-100 wl-col--less-padding"}>
            <textarea
                className={"wl-faq-edit-item__textarea"}
              rows={3}
              value={this.state.textAreaValue}
              onChange={e => {
                this.changeValueOnUserType(e);
              }}
              style={{ width: "95%" }}
            />
            {showWarningIfAnswerWordCountExceedsLimit(this.props.type, this.state.textAreaValue)}
            {showWarningIfInvalidHTMLTagPresentInAnswer(this.props.type, this.state.textAreaValue)}
          </WlColumn>
        </WlContainer>
        <FaqEditButtonGroup updateHandler={this.updateFaqEditItem} deleteHandler={this.deleteFaqItem} />
      </React.Fragment>
    );
  }
}
FaqEditItem.propTypes = {
  // Edit item type it can be question or answer.
  type: PropTypes.string,
  // Faq item id.
  id: PropTypes.string
};
export default connect()(FaqEditItem);
