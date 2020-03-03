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
    /**
     * NOTE: answer is a trusted input from user, this component is not protected against xss,
     * although the tags are filtered by faq filter.
     */
  return (
    <div className={"wl-faq-answer-container"}>
      <p className={"wl-faq-answer-title"} dangerouslySetInnerHTML={{__html:answer}} />
    </div>
  );
};

export default Answer;
