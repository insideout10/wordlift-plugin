/**
 * Answer component displaying single question
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

const Answer = ({ answer }) => {
  return (
    <div className={"wl-faq-answer-container"}>
      <p className={"wl-faq-answer-title"}>{answer}</p>
    </div>
  );
};

export default Answer;
