/**
 * A mock selected blocks object obtained from gutenberg, used
 * for testing the hook externally.
 */
const fakeParagraphBlocksData = [
  {
    clientId: "1595319b-0c37-41b3-addf-93804ded1a68",
    name: "core/paragraph",
    isValid: true,
    originalContent: "this is a answer in first paragraph",
    attributes: {
      content: "this is a answer in first paragraph",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "7f122677-7ebd-44bd-80fb-5f9ecdff11f5",
    name: "core/paragraph",
    originalContent: "this is a answer in second",
    isValid: true,
    attributes: {
      content: "this is a answer in second",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "cfe47d7b-b5cd-4af2-b4c1-8c747313a6e0",
    name: "core/paragraph",
    originalContent: "this is answer in third",
    isValid: true,
    attributes: {
      content: "this is answer in third",
      dropCap: false
    },
    innerBlocks: []
  }
];

/**
 * A mock  blocks object obtained from gutenberg with previously highlighted tags, used
 * for testing the hook externally.
 */
const fakeParagraphBlocksHighlightingData = [
  {
    clientId: "1595319b-0c37-41b3-addf-93804ded1a68",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content:
        "<wl-faq-answer class='123'>this</wl-faq-answer> is a <wl-faq-question class='123'>answer</wl-faq-question> in first paragraph",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "7f122677-7ebd-44bd-80fb-5f9ecdff11f5",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content: "this is a answer in second",
      dropCap: false
    },
    innerBlocks: []
  },
  {
    clientId: "cfe47d7b-b5cd-4af2-b4c1-8c747313a6e0",
    name: "core/paragraph",
    isValid: true,
    attributes: {
      content: "this is answer in third",
      dropCap: false
    },
    innerBlocks: []
  }
];

export const updateBlockAttributesMethod = jest.fn();
export const blockEditorWithSelectedBlocks = {
  data: {
    select: editorString => {
      if (editorString === "core/block-editor") {
        return {
          getMultiSelectedBlocks: () => fakeParagraphBlocksData,
          getSelectedBlock: () => {
            return null
          }
        };
      }
    },
    dispatch: editorString => {
      if (editorString === "core/block-editor") {
        return {
          updateBlockAttributes: updateBlockAttributesMethod
        };
      }
    }
  }
};

export const blockEditorWithNoSelectedBlocks = {
  data: {
    select: editorString => {
      if (editorString === "core/block-editor") {
        return {
          getMultiSelectedBlocks: () => [],
          getSelectedBlock: () => {
            return null
          }
        };
      }
    },
    dispatch: editorString => {
      if (editorString === "core/block-editor") {
        return {
          updateBlockAttributes: updateBlockAttributesMethod
        };
      }
    }
  }
};

export const blockEditorWithHighlightedBlocks = {
  data: {
    select: editorString => {
      if (editorString === "core/block-editor") {
        return {
          getBlocks: () => fakeParagraphBlocksHighlightingData
        };
      }
    },
    dispatch: editorString => {
      if (editorString === "core/block-editor") {
        return {
          updateBlockAttributes: updateBlockAttributesMethod
        };
      }
    }
  }
};
