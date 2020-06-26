import ClassicEditorBlockValidator from "../stores/classic-editor-block-validator";

let getSelectedBlockFn = jest.fn()

beforeEach(() => {
    global["wp"] = {
        data: {
            select: (name) => {
                if (name === "core/editor") {
                    return {
                        getSelectedBlock: getSelectedBlockFn
                    }
                }
            }
        }
    }
})

it("when classic block editor given invalid selected block should return false", () => {
    expect(ClassicEditorBlockValidator.getValue("foo")).toEqual(false)
})

it("when classic block editor with valid selected block should return valid value object", () => {

    getSelectedBlockFn.mockReturnValueOnce({
        attributes: {
            content: "this is a string with foo text"
        }
    })

    let value = ClassicEditorBlockValidator.getValue("foo")
    expect(value !== undefined).toEqual(true)
    expect(value.hasOwnProperty("start")).toEqual(true)
    expect(value.hasOwnProperty("end")).toEqual(true)
    expect(value.start).toEqual(22)
    expect(value.end).toEqual(25)
})
