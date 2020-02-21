/**
 * This files provide the filters for transforming ui data to api and viceversa.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Takes an array of FAQ items from API and transform it to ui data.
 * @param items
 * @return {Array} Transformed FAQ items.
 */
export const transformAPIDataToUi = items => {
  return items.map((item) => ({
    ...item,
    /**
     * We save the value of question and answer before supplying to ui, they will
     * be used when it is used to update in the db
     */
    id: item.id.toString(),
    previousQuestionValue: item.question,
    previousAnswerValue: item.answer
  }));
};

/**
 * Takes an array of FAQ Ui items from API and transform it to api data.
 * @param items FAQ items from redux store.
 * @return {Array} Transformed FAQ items.
 */
export const transformUiDataToApiFormat = items => {
  return items.map((item, index) => ({
    id: item.id,
    question: item.question,
    answer: item.answer,
    previous_question_value: item.previousQuestionValue,
    previous_answer_value: item.previousAnswerValue
  }));
};
