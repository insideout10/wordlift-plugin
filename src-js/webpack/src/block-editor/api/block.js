export default class Block {
    constructor(block, dispatch, start) {
        this._block = block;
        this._dispatch = dispatch;
        this._start = start;
        this._dirty = false;
    }

    get content() {
        return this._content;
    }

    set content(value) {
        this._content = value;
        this._dirty = true;
    }

    get clientId() {
        return this._block.clientId;
    }

    get start() {
        return this._start;
    }


    insertHtml(at, fragment) {
        // Insert the HTML.
        const newContent = this.content.substring(0, at) + fragment + this.content.substring(at);

        // Bail out if the content didn't change.
        if (newContent === this.content) return;

        this.content = newContent;
        this._dirty = true;
    }

    replace(pattern, replacement) {
        const newContent = this.content.replace(pattern, replacement);

        // Bail out if the content didn't change.
        if (newContent === this.content) return;

        this.content = newContent;
        this._dirty = true;
    }

    apply() {
        if (this._dirty) {
            console.debug("Block.apply", {
                clientId: this.clientId,
                content: this.content,
                dispatch: this._dispatch,
                updateBlockAttributes: this._dispatch.updateBlockAttributes
            });
        }
    }
}
