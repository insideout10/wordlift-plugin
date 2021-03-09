import Block from "./block";

export default class TextBlock extends Block{
    constructor(block, dispatch, start = 0, blockSeparatorLength) {
        super(block, dispatch, start);
        this._content = block.attributes.content;
        this._end = this.start +  block.attributes.content.length + blockSeparatorLength;
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
