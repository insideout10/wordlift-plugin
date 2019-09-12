export default class Block {
  constructor(block, dispatch, start = 0, end = -1) {
    this._dispatch = dispatch;
    this._block = block;
    this._content = block.attributes.content;
    this._start = start;
    this._end = 0 <= end ? end : block.attributes.content.length;
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

  replace(pattern, replacement) {
    const newContent = this.content.replace(pattern, replacement);

    // Bail out if the content didn't change.
    if (newContent === this.content) return;

    this.content = newContent;
  }

  apply() {
    this._dispatch.updateBlockAttributes(this.clientId, { content: this.content }).then(() => (this._dirty = false));
  }
}
