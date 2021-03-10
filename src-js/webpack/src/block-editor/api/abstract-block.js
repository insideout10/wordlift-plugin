/**
 *  @abstract
 */
export class AbstractBlock {

    constructor(block, dispatch, start) {
        this._block = block;
        this._dispatch = dispatch;
        this._start = start;
        this._end = -1
        this._dirty = false;
    }

    get block() {
        return this._block
    }

    set block(block) {
        this._block = block
    }

    get content() {
        return this._content;
    }
    set content(value) {
        this._content = value;
        this._dirty = true;
    }

    get end() {
        return this._end;
    }

    set end(end) {
        this._end = end
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

    /**
     *  @abstract
     */
    apply() {
    }
}