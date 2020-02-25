/**
 * FaqListHeader shows the ui to save a new question.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependenices.
 */
import QuestionInputBox from "../question-input-box";
import AddQuestionButton from "../add-question-button";
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";

export const FaqListHeader = () => {
  const { addQuestionText } = global["_wlFaqSettings"];
  return (
    <React.Fragment>
      <WlContainer>
        <WlColumn className={"wl-col--width-60"}>
          <QuestionInputBox />
        </WlColumn>
        <WlColumn className={" wl-col--width-20"}>
          <AddQuestionButton questionButtonText={addQuestionText} />
        </WlColumn>
      </WlContainer>
    </React.Fragment>
  );
};
