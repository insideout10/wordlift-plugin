import {AbstractBlock} from "./abstract-block";

/**
 * Text block represents the core/paragraph and also the core/freeform block
 * in the gutenberg editor.
 */

export default class TextBlock extends AbstractBlock {

    constructor(block, dispatch, start = 0) {
        super();
        this._block = block;
        this._dispatch = dispatch;
        this._content = block.attributes.content;
        this._start = start;
        this._end = -1
        this._dirty = false;
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
    apply() {
        if (this._dirty) {
            console.debug("Block.apply", {
                clientId: this.clientId,
                content: this.content,
                dispatch: this._dispatch,
                updateBlockAttributes: this._dispatch.updateBlockAttributes
            });
            // WP 5.0 returns undefined to this call.
            this._dispatch.updateBlockAttributes(this.clientId, {content: this.content});
            this._dirty = false;
        }
    }
}
