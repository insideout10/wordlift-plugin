import BlockEditorFormatTypeHandler from "../hooks/block-editor/block-editor-format-type-handler";
import {FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME} from "../hooks/custom-faq-elements";

const customElementDefineFunction = jest.fn()

beforeEach(() => {
    global["customElements"] = {
        get: jest.fn(() => {
            return undefined
        }),
        define: customElementDefineFunction
    }
})

it("when format type handler is registering, should register correct formats", () => {
    const handler = new BlockEditorFormatTypeHandler()
    handler.registerAllFormatTypes()
    // When registering format types we also register those custom elements.
    expect(customElementDefineFunction.mock.calls).toHaveLength(2)
    // we should have question and answer tags in the args.
    expect(customElementDefineFunction.mock.calls[0][0]).toEqual(FAQ_ANSWER_TAG_NAME);
    expect(customElementDefineFunction.mock.calls[1][0]).toEqual(FAQ_QUESTION_TAG_NAME);
})