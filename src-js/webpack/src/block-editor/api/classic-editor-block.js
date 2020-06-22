/**
 * The classic editor block class is designed to create entities on the
 * block editor, this block cant be altered by using applyFormat.
 *
 * see here: https://github.com/insideout10/wordlift-plugin/issues/1039
 * @since 3.26.1
 */

import {annotationSettings} from "../formats/register-format-type-wordlift-annotation";

class ClassicEditorBlock {
  /**
   * @param attributeName {string} Attribute name of the classic editor block, usually defaults
   * to content
   * @param content  {string} The HTML Content value of this block.
   */
  constructor(content, attributeName = "content") {
    this._content = content;
    this._attributeName = attributeName;
  }

  /**
   * This method replaces the string with the annotation id,
   * it does not hard code the html, it generates the wrapper html
   * from the format registered for annotation.
   * @param stringToBeFound
   * @param annotationId
   * @param attrs The extra attributes which needs to be present in html tag.
   */
  replaceWithAnnotation(stringToBeFound, attrs = {}) {
    const {openTag, closeTag} = this.generateOpenAndCloseTag(annotationSettings, attrs);
    this._content = this._content.replace(stringToBeFound, openTag + stringToBeFound + closeTag);
  }

  getContent() {
    return this._content;
  }

  generateOpenAndCloseTag(annotationSettings, attributes) {
    let openTag = "<" + annotationSettings.tagName;
    let closeTag = `</${annotationSettings.tagName}>`;
    // We add the class name since it is already present in the registration.
    attributes["class"] = annotationSettings.className
    // Process the settings and add attributes.
    // Loop through all annotation attributes and create value in the tag if they have it.
    let annotationAttrs = annotationSettings.attributes || {};
    for (let key in annotationAttrs) {
      const value = attributes[key] || "";
      if (value !== "") {
        openTag += ` ${key}="${value}"`;
      }
    }
    openTag += ">";

    return {openTag, closeTag};
  }
}

export default ClassicEditorBlock;
