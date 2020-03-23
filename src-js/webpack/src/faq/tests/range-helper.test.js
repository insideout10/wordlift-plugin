import RangeHelper from "../hooks/helpers/range-helper";

it("when given range object on same start and end container should perform slice and " +
    "return nodes to be highlighted and nodes which should not be highlighted", () => {

    const textNode = document.createTextNode("This is foo text");
    const paragraph = document.createElement('p');
    paragraph.appendChild(textNode);
    /**
     * Lets assume user has selected on the same text node with offsets
     * 4, 12, so he selected the text `is foo`.
     */
    const range = {
        startContainer: textNode,
        endContainer: textNode,
        startOffset: 4,
        endOffset: 12
    };
    const rangeHelper = new RangeHelper(range);
    // if we now look at the parent element, we should have 3 children text nodes
    expect(paragraph.childNodes).toHaveLength(3)

});
