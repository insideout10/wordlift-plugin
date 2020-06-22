import ClassicEditorBlock from "../api/classic-editor-block";

let updateBlockAttributeFn = jest.fn()

beforeEach(() => {
    global["wp"] = {
        data: {
            dispatch: (name) => {
                if (name === "core/editor") {
                    return {
                        updateBlockAttributes: updateBlockAttributeFn
                    }
                }
            }
        }
    }
})

it("when given blockvalue and attribute key name, should replace content correctly", () => {
    let instance = new ClassicEditorBlock(123, '<p>this is a string template</p>', 'content')
    instance.replaceWithAnnotation('string', {
        id: "urn:enhancement-121312",
        itemid: "foo"
    })
    expect(instance.getContent()).toEqual('<p>this is a <span id="urn:enhancement-121312" class="textannotation" itemid="foo">' +
        'string</span> template</p>')
})

it("when classic editor block is updated, should call the correct method", () => {
    let instance = new ClassicEditorBlock(123, '<p>this is a string template</p>', 'content')
    instance.replaceWithAnnotation('string', {
        id: "urn:enhancement-121312",
        itemid: "foo"
    })
    instance.update()
    // block update should be called once.
    expect(updateBlockAttributeFn.mock.calls).toHaveLength(1);
    const call = updateBlockAttributeFn.mock.calls[0]
    // id should be 123
    expect(call[0]).toEqual(123)
    expect(call[1].content).toEqual('<p>this is a <span id="urn:enhancement-121312" class="textannotation" itemid="foo">' +
        'string</span> template</p>')
})
