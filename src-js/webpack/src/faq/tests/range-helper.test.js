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
    expect(paragraph.childNodes).toHaveLength(3);
    const processedRangeObj = rangeHelper.getProcessedRange();
    /**
     * check if the nodes are correctly returned.
     */
    expect(processedRangeObj.nodesToBeHighlighted).toHaveLength(1);
    expect(processedRangeObj.nodesShouldNotBeHighlighted).toHaveLength(2);
    // the highlighted node should contain text ` is foo `
    expect(processedRangeObj.nodesToBeHighlighted[0].textContent).toEqual(" is foo ")

});


it("when the range object start and end containers are not equal should return correct" +
    "node to be highlighted and non highlighted nodes", () => {

    /**
     * We are creating two text nodes and two paragraphs, the range extend along
     * these two paragraphs.
     */
    const textNode1 = document.createTextNode("This is foo text");
    const paragraph1 = document.createElement('p');
    paragraph1.appendChild(textNode1);

    const textNode2 = document.createTextNode("some sample text");
    const paragraph2 = document.createElement('p');
    paragraph2.appendChild(textNode2);

    /**
     * Lets assume user has selected on the same text node with offsets
     * 4, 12, so he selected the text `is foo text` and `some`.
     */
    const range = {
        startContainer: textNode1,
        endContainer: textNode2,
        startOffset: 4,
        endOffset: 4
    };

    const rangeHelper = new RangeHelper(range);
    // if we now look at the parent element, we should have 2 children text nodes
    expect(paragraph1.childNodes).toHaveLength(2);
    expect(paragraph2.childNodes).toHaveLength(2);
    const processedRangeObj = rangeHelper.getProcessedRange();
    expect(processedRangeObj.nodesToBeHighlighted[0].textContent).toEqual(" is foo text");
    // end text should be some
    expect(processedRangeObj.nodesToBeHighlighted[1].textContent).toEqual("some")

});