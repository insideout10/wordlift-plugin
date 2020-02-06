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

const AddQuestionButton = ({ question, questionButtonText }) => {
  return (
    <button disabled={question.length === 0} className={"wl-add-question-button"}>
      &nbsp;
      <span className="dashicons dashicons-plus" />
      {questionButtonText}
    </button>
  );
};

AddQuestionButton.propTypes = {
  // The question which is typed on the input box, usually obtained from state container
  question: PropTypes.string,
  // The Add Question translated string from the globals.
  questionButtonText: PropTypes.string
};

export default AddQuestionButton;
