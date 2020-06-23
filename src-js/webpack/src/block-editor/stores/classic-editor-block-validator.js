/**
 * @since 3.26.1
 * This class provides compatibility to classic editor block present
 * present inside the block editor.
 */

export default class ClassicEditorBlockValidator {

    /**
     * Render the html on dom element, return the text.
     * @param html {string}
     * @return text {string}
     */
    static getTextValue(html) {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = html;
        return wrapper.textContent
    }

    static getValue(entityLabel) {
        let selectedBlock = wp.data.select("core/editor").getSelectedBlock();
        // Return early if there is no selected block.
        if (selectedBlock === null || selectedBlock === undefined) {
            return false
        }
        // Return early if there is no attributes
        if (selectedBlock.attributes === undefined || selectedBlock.attributes === null) {
            return false
        }

        // If there isn o content on the block we cant create the value dependency.
        if (selectedBlock.attributes.content === null || selectedBlock.attributes.content === ""
            || selectedBlock.attributes.content === undefined) {
            return false;
        }
        const blockHtml = selectedBlock.attributes.content;
        const startIndex = ClassicEditorBlockValidator.getTextValue(blockHtml)
            .indexOf(entityLabel);

        if (startIndex === -1) {
            // we cant find the string, return early.
            return false;
        }
        // we have constructed the value dependency.
        return {
            start: startIndex,
            end: startIndex + entityLabel.length
        };
    }

}

