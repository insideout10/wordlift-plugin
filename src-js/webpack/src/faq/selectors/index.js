/**
 * This files provide the selectors to select the state.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Return the faq items from the store
 * @param state The full state of FAQ store
 * @return {[]} Array of FAQ items.
 */
export const getAllFAQItems = state => state.faqItems;

/**
 * Get currently typed question in the FAQ header
 * @param state The full state of the FAQ store.
 * @return {*} String
 */
export const getCurrentQuestion = state => state.question;
