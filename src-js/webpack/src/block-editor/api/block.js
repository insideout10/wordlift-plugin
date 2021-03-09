export default class Block {
  constructor(block, dispatch, start = 0, end = -1) {
    this._block = block;
    this._dispatch = dispatch;
    this._start = start;
    /**
     * @since 3.29.1
     * @see 1311
     * Set the attribute only if its available since we are going to support
     * other core blocks list & table.
     */
    if ( block.attributes.content ) {
      this._content = block.attributes.content;
      this._end = 0 <= end ? end : block.attributes.content.length;
    }

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

  get end() {
    return this._end;
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

      if ( "core/paragraph" === this._block.name || "core/freeform" === this._block.name ) {
        // WP 5.0 returns undefined to this call.
        this._dispatch.updateBlockAttributes(this.clientId, {content: this.content});
        this._dirty = false;
      }

    }
  }
}
