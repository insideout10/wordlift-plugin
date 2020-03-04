/**
 * FaqValidator validates the text selected by user, determines if it is question
 * or answer.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class FaqValidator {
  static isQuestion(text) {
    return text.trim().endsWith("?");
  }
}

export default FaqValidator;
