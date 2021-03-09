import Block from "./block";

export default class ListBlock extends Block{

    constructor(block, dispatch, start = 0, blockSeparatorLength) {
        super(block, dispatch, start = 0, -1);
        // In the list block we need to get the content from values attribute.
        this._content = block.attributes.values;
        this._end = block.attributes.values.length + blockSeparatorLength;
    }

    apply() {
        super.apply();
        if (this._dirty) {
            this._dispatch.updateBlockAttributes(this.clientId, {values: this.content});
            this._dirty = false;
        }
    }
}