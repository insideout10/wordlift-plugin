/**
 * Add Question Button for the FAQ meta box
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * external dependencies
 */
import React from "react";
import PropTypes from "prop-types";
/**
 * Internal dependencies.
 */
import "./index.scss";
import { connect } from "react-redux";
import { requestAddNewQuestion } from "../../actions";

class AddQuestionButton extends React.Component {
  render() {
    return (
      <button
        disabled={this.props.question.length === 0}
        className={"wl-add-question-button"}
        type="button"
        onClick={() => {
          this.props.dispatch(requestAddNewQuestion());
        }}
      >
        {this.props.questionButtonText}
      </button>
    );
  }
}

AddQuestionButton.propTypes = {
  // The question which is typed on the input box, usually obtained from state container
  question: PropTypes.string,
  // The Add Question translated string from the globals.
  questionButtonText: PropTypes.string
};

export default connect(state => ({
  question: state.faqListOptions.question
}))(AddQuestionButton);
