/**
 * Question component displaying single question
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss";

const Question = ({ question }) => {
  return (
    <div className={"wl-faq-question-container"}>
      <p className={"wl-faq-question-title"}>{question}</p>
    </div>
  );
};
export default Question;
