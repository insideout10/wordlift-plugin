/**
 *  @abstract
 */
export class AbstractBlock {

    get block() {
        return this._block
    }

    set block(block) {
        this._block = block
    }

    /**
     *  @abstract
     */
    get content() {
    }

    /**
     *  @abstract
     */
    set content(value) {
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