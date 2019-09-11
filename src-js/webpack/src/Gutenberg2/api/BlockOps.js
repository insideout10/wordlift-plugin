export default class BlockOps {
  constructor(start, end, clientId, content, dirty) {
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
}
