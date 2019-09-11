export default class BlockOps {
  constructor(dispatch, start, end, clientId, content, dirty) {
    this._dispatch = dispatch;
    this._start = start;
    this._end = end;
    this._clientId = clientId;
    this._content = content;
    this._dirty = dirty;
  }

  get start() {
    return this._start;
  }

  get end() {
    return this._end;
  }

  get clientId() {
    return this._clientId;
  }

  get content() {
    return this._content;
  }

  set content(value) {
    this._content = value;
    this._dirty = true;
  }

  get dirty() {
    return this._dirty;
  }

  /**
   *
   * @param at
   * @param fragment
   */
  insertHtml(at, fragment) {
    // Insert the HTML.
    this.content = this.content.substring(0, at) + fragment + this.content.substring(at);
  }

  applyChanges() {
    if (this.dirty) {
      this._dispatch
        .updateBlock(this.clientId, {
          attributes: { content: this.content }
        })
        .then(() => (this._dirty = false));
    }
  }
}
