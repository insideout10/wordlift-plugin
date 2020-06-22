import ClassicEditorBlock from "../api/classic-editor-block";


it("when given blockvalue and attribute key name, should replace content correctly", () => {
    let instance = new ClassicEditorBlock('<p>this is a string template</p>', 'content')
    instance.replaceWithAnnotation('string', {
        id: "urn:enhancement-121312",
        itemid: "foo"
    })
    expect(instance.getContent()).toEqual('<p>this is a <span id="urn:enhancement-121312" class="textannotation" itemid="foo">' +
        'string</span> template</p>')
})
