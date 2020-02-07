/**
 * This files provide the filters for transforming ui data to api and viceversa.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

export const transformAPIDataToUi = items => {
  return items.map((item, index) => ({
    ...item,
    id: index,
    /**
     * We save the value of question and answer before supplying to ui, they will
     * be used when it is used to update in the db
     */
    previousQuestionValue: item.question,
    previousAnswerValue: item.answer
  }));
};
