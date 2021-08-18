/**
 *  @author Naveen Muthusamy <naveen@wordlift.io>
 *  @since 3.32.6
 *  @abstract
 */
export class LinkServiceInterface {
    /**
     * Set the link flag on the provided `occurrences`.
     *
     * @since 3.11.0
     * @param {Array} occurrences An array of occurrences ids (which match dom
     *     elements).
     * @param {boolean} value True to enable linking or false to disable it.
     * @abstract
     */
    setLink(occurrences, value) {
    }

    /**
     * Switch the link on.
     *
     * @since 3.13.0
     * @param {object} elem A DOM element.
     * @abstract
     */
    setYesLink(elem) {
    }

    /**
     * Switch the link off.
     *
     * @since 3.13.0
     * @param {object} elem A DOM element.
     * @abstract
     */
    setNoLink(elem) {
    }

    /**
     * Get the link flag given the provided `occurrences`. A link flag is
     * considered true when at least one occurrences enables linking.
     *
     * @since 3.11.0
     * @param {Array} occurrences An array of occurrences dom ids.
     * @return {boolean} True if at least one occurrences enables linking,
     *     otherwise false.
     * @abstract
     */
    getLink(occurrences) {
    }
}