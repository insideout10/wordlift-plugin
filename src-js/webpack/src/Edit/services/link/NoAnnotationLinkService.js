/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.32.6
 */

/**
 * Internal dependencies
 */
import {LinkServiceInterface} from "./LinkServiceInterface";

/**
 * Define the `NoAnnotationLinkService` class, this shouldn't perform any functions.
 *
 * @since 3.32.6
 */
export default class NoAnnotationLinkService extends LinkServiceInterface {
  /**
   * Create an `LinkService` instance.
   *
   * @since 3.13.0
   */
  constructor() {
    super();
  }

  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences ids (which match dom
   *     elements).
   * @param {boolean} value True to enable linking or false to disable it.
   */
  setLink(occurrences, value) {}
  /**
   * Switch the link on.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */
  setYesLink(elem) {}
  /**
   * Switch the link off.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */
  setNoLink(elem) {}
  /**
   * Get the link flag given the provided `occurrences`. A link flag is
   * considered true when at least one occurrences enables linking.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences dom ids.
   * @return {boolean} True if at least one occurrences enables linking,
   *     otherwise false.
   */
  getLink(occurrences) {}
}
