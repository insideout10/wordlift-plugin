/*global wlSettings*/
/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.21.0
 */

/**
 * Define the `LinkService` class.
 *
 * @since 3.21.0
 */
class LinkService {
  /**
   * Create an `LinkService` instance.
   *
   * @since 3.21.0
   * @param {boolean} linkByDefault Whether to link by default.
   */
  constructor(linkByDefault) {
    // Set the `link by default` setting.
    this.linkByDefault = linkByDefault;
  }

  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.21.0
   * @param {Array} occurrences An array of occurrences ids (which match dom
   *     elements).
   * @param {boolean} value True to enable linking or false to disable it.
   */
  setLink(occurrences, value) {
    // If the request is to enable links, remove the `wl-no-link` class on
    // all the occurrences.
    if (value) {
      occurrences.forEach(x => this.setYesLink(x));
    } else {
      // If the request is to disable links, add the `wl-no-link` class to
      // all occurrences.
      occurrences.forEach(x => this.setNoLink(x));
    }
  }

  /**
   * Switch the link on.
   *
   * @since 3.21.0
   * @param {object} elem A DOM element.
   */
  setYesLink(elem) {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          let content = block.attributes.content;
          let blockUid = block.clientId;
          let contentElem = document.createElement("div");
          let selector = elem.replace("urn:", "urn\\3A ");

          contentElem.innerHTML = content;
          if (contentElem.querySelector("#" + selector)) {
            contentElem.querySelector("#" + selector).classList.remove("wl-no-link");
            contentElem.querySelector("#" + selector).classList.add("wl-link");
            wp.data.dispatch("core/editor").updateBlock(blockUid, {
              attributes: {
                content: contentElem.innerHTML
              }
            });
          }
        }
      });
  }

  /**
   * Switch the link off.
   *
   * @since 3.21.0
   * @param {object} elem A DOM element.
   */
  setNoLink(elem) {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          let content = block.attributes.content;
          let blockUid = block.clientId;
          let contentElem = document.createElement("div");
          let selector = elem.replace("urn:", "urn\\3A ");

          contentElem.innerHTML = content;
          if (contentElem.querySelector("#" + selector)) {
            contentElem.querySelector("#" + selector).classList.remove("wl-link");
            contentElem.querySelector("#" + selector).classList.add("wl-no-link");
            wp.data.dispatch("core/editor").updateBlock(blockUid, {
              attributes: {
                content: contentElem.innerHTML
              }
            });
          }
        }
      });
  }

  /**
   * Get the link flag given the provided `occurrences`. A link flag is
   * considered true when at least one occurrences enables linking.
   *
   * @since 3.21.0
   * @param {Array} occurrences An array of occurrences dom ids.
   * @return {boolean} True if at least one occurrences enables linking,
   *     otherwise false.
   */
  getLink(occurrences) {
    let content = "";

    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          content = content + block.attributes.content;
        }
      });

    let contentElem = document.createElement("div");
    contentElem.innerHTML = content;

    return occurrences.reduce((acc, id) => {
      let selector = id.replace("urn:", "urn\\3A ");
      return acc || this.linkByDefault
        ? !contentElem.querySelector("#" + selector + ".wl-no-link")
        : !!contentElem.querySelector("#" + selector + ".wl-link");
    }, false);
  }
}

// Finally export the `LinkService`.
export default new LinkService("1" === wlSettings.link_by_default);
