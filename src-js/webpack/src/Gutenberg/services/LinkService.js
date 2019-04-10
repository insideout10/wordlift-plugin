/* globals wp, wlSettings */
/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.2x
 */

/**
 * Define the `LinkService` class.
 *
 * @since 3.2x
 */
class LinkService {
  /**
   * Create an `LinkService` instance.
   *
   * @since 3.2x
   * @param {boolean} linkByDefault Whether to link by default.
   */
  constructor(linkByDefault) {
    // Set the `link by default` setting.
    this.linkByDefault = linkByDefault;
  }

  syncOccurrences(entity) {
    for (var annotation in entity.annotations) {
      if (!entity.occurrences.includes(annotation)) {
        entity.occurrences.push(annotation);
      }
    }
    return entity;
  }

  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.2x
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
   * @since 3.2x
   * @param {object} elem A DOM element.
   */
  setYesLink(elem) {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          const selector = elem.replace("urn:", "urn\\3A ");
          let contentElem = document.createElement("div");

          contentElem.innerHTML = block.attributes.content;
          if (contentElem.querySelectorAll("#" + selector).length > 0) {
            contentElem.querySelectorAll("#" + selector).forEach(nodeValue => {
              nodeValue.classList.remove("wl-no-link");
              nodeValue.classList.add("wl-link");
            });
            wp.data.dispatch("core/editor").updateBlock(block.clientId, {
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
   * @since 3.2x
   * @param {object} elem A DOM element.
   */
  setNoLink(elem) {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          const selector = elem.replace("urn:", "urn\\3A ");
          let contentElem = document.createElement("div");

          contentElem.innerHTML = block.attributes.content;
          if (contentElem.querySelectorAll("#" + selector).length > 0) {
            contentElem.querySelectorAll("#" + selector).forEach(nodeValue => {
              nodeValue.classList.remove("wl-link");
              nodeValue.classList.add("wl-no-link");
            });
            wp.data.dispatch("core/editor").updateBlock(block.clientId, {
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
   * @since 3.2x
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
      const selector = id.replace("urn:", "urn\\3A ");
      if (
        contentElem.querySelectorAll("#" + selector + ".wl-no-link").length === 0 &&
        contentElem.querySelectorAll("#" + selector + ".wl-link").length === 0
      ) {
        return false;
      }
      return acc || this.linkByDefault
        ? !contentElem.querySelectorAll("#" + selector + ".wl-no-link").length
        : !!contentElem.querySelectorAll("#" + selector + ".wl-link").length;
    }, false);
  }
}

// Finally export the `LinkService`.
export default new LinkService("1" === wlSettings.link_by_default);
