import {AbstractBlock} from "./abstract-block";

/**
 * Text block represents the core/list
 * in the gutenberg editor.
 */

export default class ListBlock extends AbstractBlock {

    constructor(block, dispatch, start) {
        super(block, dispatch, start);
        this._content = block.attributes.values;
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
            this._dispatch.updateBlockAttributes(this.clientId, {values: this.content});
            this._dirty = false;
        }
    }
}
